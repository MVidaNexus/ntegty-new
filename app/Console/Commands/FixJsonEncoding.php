<?php

namespace App\Console\Commands;

use App\Models\Result;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixJsonEncoding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'results:fix-json-encoding {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix results with Unicode-escaped JSON that breaks JSON_EXTRACT queries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Searching for records with Unicode-escaped JSON...');
        
        // Find records with Unicode escapes (like \u0627)
        $affectedRecords = DB::table('results')
            ->whereRaw("subjects_data LIKE '%\\\\u%'")
            ->get(['id', 'seat_number', 'subjects_data']);
        
        $count = $affectedRecords->count();
        
        if ($count === 0) {
            $this->info('✅ No records found with Unicode encoding issues.');
            return 0;
        }
        
        $this->warn("Found {$count} records with Unicode encoding issues.");
        
        if ($dryRun) {
            $this->info('Dry run mode - showing affected records:');
            foreach ($affectedRecords as $record) {
                $this->line("  - ID: {$record->id}, Seat: {$record->seat_number}");
            }
            $this->newLine();
            $this->info("Run without --dry-run to fix these records.");
            return 0;
        }
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        $fixed = 0;
        $errors = 0;
        
        foreach ($affectedRecords as $record) {
            try {
                // Decode the JSON (PHP handles Unicode escapes correctly)
                $decoded = json_decode($record->subjects_data, true);
                
                if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                    $this->error("Failed to decode JSON for ID {$record->id}: " . json_last_error_msg());
                    $errors++;
                    continue;
                }
                
                // Re-encode with unescaped Unicode
                $correctedJson = json_encode($decoded, JSON_UNESCAPED_UNICODE);
                
                // Update the record
                DB::table('results')
                    ->where('id', $record->id)
                    ->update(['subjects_data' => $correctedJson]);
                
                $fixed++;
            } catch (\Exception $e) {
                $this->error("Error fixing ID {$record->id}: " . $e->getMessage());
                $errors++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Fixed {$fixed} records successfully.");
        
        if ($errors > 0) {
            $this->warn("⚠️  {$errors} records failed to fix.");
        }
        
        return $errors > 0 ? 1 : 0;
    }
}
