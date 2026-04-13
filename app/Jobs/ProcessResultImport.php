<?php

namespace App\Jobs;

use App\Models\UploadLog;
use App\Services\FileImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessResultImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 7200; // 2 hours
    public $tries = 1;

    protected $uploadLogId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $uploadLogId)
    {
        $this->uploadLogId = $uploadLogId;
    }

    /**
     * Execute the job.
     */
    public function handle(FileImportService $importService): void
    {
        $uploadLog = UploadLog::find($this->uploadLogId);
        
        if (!$uploadLog) {
            Log::error("UploadLog not found: {$this->uploadLogId}");
            return;
        }

        // Prevent re-processing completed or failed uploads
        if (in_array($uploadLog->status, ['completed', 'failed'])) {
            Log::warning("Upload {$this->uploadLogId} already processed with status: {$uploadLog->status}");
            return;
        }

        // Check if already has results (double-processing protection)
        $existingResults = \App\Models\Result::where('upload_log_id', $this->uploadLogId)->count();
        if ($existingResults > 0) {
            Log::warning("Upload {$this->uploadLogId} already has {$existingResults} results. Skipping to prevent duplicates.");
            $uploadLog->update([
                'status' => 'completed',
                'records_count' => $existingResults,
                'processed_rows' => $existingResults,
                'successful_rows' => $existingResults,
            ]);
            return;
        }

        Log::info("Starting import job for file: {$uploadLog->file_path}");

        try {
            // Ensure fresh DB connection for long running process
            \Illuminate\Support\Facades\DB::reconnect();
            
            // Get full path
            $fullPath = storage_path('app/' . $uploadLog->file_path);
            
            // Prepare mapping
            // The mapping in DB is: field => header_name
            // We need to pass this to the service
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

            Log::info("Import completed: " . json_encode($result));

        } catch (\Exception $e) {
            Log::error("Import Job Failed: " . $e->getMessage());
            $uploadLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            $this->fail($e);
        }
    }
}
