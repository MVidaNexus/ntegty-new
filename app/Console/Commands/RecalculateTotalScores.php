<?php

namespace App\Console\Commands;

use App\Models\ExamType;
use App\Models\Result;
use Illuminate\Console\Command;

class RecalculateTotalScores extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'results:recalculate-totals 
                            {--exam-type= : Specific exam type ID to process}
                            {--governorate= : Specific governorate ID to process}
                            {--force : Recalculate all, even if total_score exists}';

    /**
     * The console command description.
     */
    protected $description = 'Recalculate total scores from subjects data, excluding specified subjects';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $examTypeId = $this->option('exam-type');
        $governorateId = $this->option('governorate');

        $query = Result::query()->with('examType');
        
        if ($examTypeId) {
            $query->where('exam_type_id', $examTypeId);
        }
        
        if ($governorateId) {
            $query->where('governorate_id', $governorateId);
        }
        
        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('total_score')
                  ->orWhere('total_score', 0);
            });
        }

        $total = $query->count();
        
        if ($total === 0) {
            $this->info('No results to update.');
            return 0;
        }

        $this->info("Found {$total} results to process...");
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updated = 0;
        $skipped = 0;
        
        $query->chunk(500, function ($results) use (&$updated, &$skipped, $bar) {
            foreach ($results as $result) {
                if (!$result->examType || empty($result->subjects_data)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $newTotal = $result->examType->calculateTotalScore($result->subjects_data);
                
                if ($newTotal > 0) {
                    $result->total_score = $newTotal;
                    $result->save();
                    $updated++;
                } else {
                    $skipped++;
                }
                
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Updated: {$updated} results");
        $this->info("⏭️  Skipped: {$skipped} results");
        
        return 0;
    }
}
