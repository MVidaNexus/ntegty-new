<?php

namespace App\Console\Commands;

use App\Models\UploadLog;
use App\Services\FileImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessImportCommand extends Command
{
    protected $signature = 'process:import {uploadLogId}';
    protected $description = 'Process a file import in the background';

    public function handle(FileImportService $importService): int
    {
        $uploadLogId = $this->argument('uploadLogId');
        $lockKey = "processing_upload_{$uploadLogId}";

        $uploadLog = UploadLog::find($uploadLogId);

        if (!$uploadLog) {
            Log::error("ProcessImportCommand: UploadLog not found: {$uploadLogId}");
            return 1;
        }

        // Prevent re-processing completed or failed uploads
        if (in_array($uploadLog->status, ['completed', 'failed'])) {
            Log::warning("Upload {$uploadLogId} already processed with status: {$uploadLog->status}");
            Cache::forget($lockKey);
            return 0;
        }

        // Check if already has results (double-processing protection)
        $existingResults = \App\Models\Result::where('upload_log_id', $uploadLogId)->count();
        if ($existingResults > 0) {
            Log::warning("Upload {$uploadLogId} already has {$existingResults} results. Skipping to prevent duplicates.");
            $uploadLog->update([
                'status' => 'completed',
                'records_count' => $existingResults,
                'processed_rows' => $existingResults,
                'successful_rows' => $existingResults,
            ]);
            Cache::forget($lockKey);
            return 0;
        }

        Log::info("ProcessImportCommand: Starting import for file: {$uploadLog->file_path}");

        try {
            // Optimize for large imports - limit memory
            ini_set('memory_limit', '256M'); // Reduced from 512M
            set_time_limit(3600); // 1 hour max
            
            // Ensure fresh DB connection
            \Illuminate\Support\Facades\DB::reconnect();
            
            // Disable query log to save memory
            \DB::connection()->disableQueryLog();
            
            // Disable Eloquent events to save memory
            \App\Models\Result::unsetEventDispatcher();

            $fullPath = storage_path('app/' . $uploadLog->file_path);
            $mapping = $uploadLog->mapping_data;

            $result = $importService->import(
                file: $fullPath,
                examTypeId: $uploadLog->exam_type_id,
                academicYearId: $uploadLog->academic_year_id,
                governorateId: $uploadLog->governorate_id,
                columnMapping: $mapping,
                userId: $uploadLog->user_id,
                existingLog: $uploadLog
            );

            Log::info("ProcessImportCommand: Import completed: " . json_encode($result));

        } catch (\Exception $e) {
            Log::error("ProcessImportCommand: Import Failed: " . $e->getMessage());
            $uploadLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        } finally {
            // Release lock
            Cache::forget($lockKey);
        }

        return 0;
    }
}
