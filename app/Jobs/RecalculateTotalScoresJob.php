<?php

namespace App\Jobs;

use App\Models\ExamType;
use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecalculateTotalScoresJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour max
    public $tries = 1;

    protected int $examTypeId;
    protected int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $examTypeId, int $userId)
    {
        $this->examTypeId = $examTypeId;
        $this->userId = $userId;
    }

    /**
     * Get the cache key for progress tracking
     */
    public static function progressKey(int $examTypeId): string
    {
        return "recalculate_progress_{$examTypeId}";
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $examType = ExamType::find($this->examTypeId);
        
        if (!$examType) {
            Log::error("RecalculateTotalScoresJob: ExamType {$this->examTypeId} not found");
            return;
        }

        $cacheKey = self::progressKey($this->examTypeId);
        
        // Get total count
        $totalCount = Result::where('exam_type_id', $this->examTypeId)
            ->whereNotNull('subjects_data')
            ->count();

        // Initialize progress
        Cache::put($cacheKey, [
            'status' => 'running',
            'total' => $totalCount,
            'processed' => 0,
            'updated' => 0,
            'started_at' => now()->toDateTimeString(),
            'exam_type_name' => $examType->name_ar,
        ], 7200); // 2 hours TTL

        $updated = 0;
        $processed = 0;
        $chunkSize = 500;

        try {
            Result::where('exam_type_id', $this->examTypeId)
                ->whereNotNull('subjects_data')
                ->chunkById($chunkSize, function ($results) use ($examType, &$updated, &$processed, $cacheKey, $totalCount) {
                    foreach ($results as $result) {
                        if (!empty($result->subjects_data)) {
                            $newTotal = $examType->calculateTotalScore($result->subjects_data);
                            if ($newTotal !== null && $newTotal >= 0) {
                                // Only update if total changed
                                if (abs($result->total_score - $newTotal) > 0.01) {
                                    $result->total_score = $newTotal;
                                    $result->save();
                                    $updated++;
                                }
                            }
                        }
                        $processed++;
                    }

                    // Update progress every chunk
                    Cache::put($cacheKey, [
                        'status' => 'running',
                        'total' => $totalCount,
                        'processed' => $processed,
                        'updated' => $updated,
                        'started_at' => Cache::get($cacheKey)['started_at'] ?? now()->toDateTimeString(),
                        'exam_type_name' => $examType->name_ar,
                        'percentage' => $totalCount > 0 ? round(($processed / $totalCount) * 100, 1) : 0,
                    ], 7200);
                });

            // Mark as completed
            Cache::put($cacheKey, [
                'status' => 'completed',
                'total' => $totalCount,
                'processed' => $processed,
                'updated' => $updated,
                'started_at' => Cache::get($cacheKey)['started_at'] ?? now()->toDateTimeString(),
                'completed_at' => now()->toDateTimeString(),
                'exam_type_name' => $examType->name_ar,
                'percentage' => 100,
            ], 7200);

            Log::info("RecalculateTotalScoresJob completed: ExamType {$this->examTypeId}, Updated {$updated} of {$totalCount} results");

        } catch (\Exception $e) {
            // Mark as failed
            Cache::put($cacheKey, [
                'status' => 'failed',
                'total' => $totalCount,
                'processed' => $processed,
                'updated' => $updated,
                'error' => $e->getMessage(),
                'exam_type_name' => $examType->name_ar,
            ], 7200);

            Log::error("RecalculateTotalScoresJob failed: " . $e->getMessage());
            throw $e;
        }
    }
}
