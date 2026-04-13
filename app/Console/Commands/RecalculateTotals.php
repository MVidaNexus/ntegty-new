<?php

namespace App\Console\Commands;

use App\Models\Result;
use App\Models\ExamType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalculateTotals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'results:recalculate-totals 
                            {--exam-type= : Exam type ID or code to filter by}
                            {--governorate= : Governorate ID to filter by}
                            {--seat= : Specific seat number to fix}
                            {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate total scores for results based on subjects data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $examTypeOption = $this->option('exam-type');
        $governorateId = $this->option('governorate');
        $seatNumber = $this->option('seat');
        
        // Build query
        $query = Result::query();
        
        // Filter by specific seat number
        if ($seatNumber) {
            $query->where('seat_number', $seatNumber);
        }
        
        // Filter by exam type
        if ($examTypeOption) {
            $examType = ExamType::where('id', $examTypeOption)
                ->orWhere('code', $examTypeOption)
                ->first();
            
            if (!$examType) {
                $this->error("Exam type '{$examTypeOption}' not found.");
                return 1;
            }
            
            $query->where('exam_type_id', $examType->id);
            $this->info("Filtering by exam type: {$examType->name} (ID: {$examType->id})");
        }
        
        // Filter by governorate
        if ($governorateId) {
            $query->where('governorate_id', $governorateId);
            $this->info("Filtering by governorate ID: {$governorateId}");
        }
        
        $total = $query->count();
        
        if ($total === 0) {
            $this->info('No records found matching the criteria.');
            return 0;
        }
        
        $this->info("Found {$total} records to process.");
        
        if ($dryRun) {
            $this->info('Dry run mode - showing first 10 records that would be affected:');
            
            $samples = $query->limit(10)->get();
            
            $table = [];
            foreach ($samples as $result) {
                $examType = $result->examType;
                $subjects = $result->subjects_data;
                
                if (!$subjects || !is_array($subjects)) {
                    continue;
                }
                
                $calculated = $examType ? $examType->calculateTotalScore($subjects) : $this->simpleCalculate($subjects);
                $current = $result->total_score;
                $diff = abs($calculated - $current);
                
                if ($diff > 0.01) {
                    $table[] = [
                        'seat' => $result->seat_number,
                        'name' => mb_substr($result->student_name, 0, 20),
                        'current' => $current,
                        'calculated' => $calculated,
                        'diff' => $diff > 0 ? "+{$diff}" : $diff,
                    ];
                }
            }
            
            if (!empty($table)) {
                $this->table(['Seat', 'Name', 'Current', 'Calculated', 'Diff'], $table);
            } else {
                $this->info('No differences found in sample records.');
            }
            
            $this->newLine();
            $this->info("Run without --dry-run to apply changes.");
            return 0;
        }
        
        // Process in chunks
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        $fixed = 0;
        $unchanged = 0;
        $errors = 0;
        
        $query->chunk(500, function ($results) use (&$fixed, &$unchanged, &$errors, $bar) {
            foreach ($results as $result) {
                try {
                    $examType = $result->examType;
                    $subjects = $result->subjects_data;
                    
                    if (!$subjects || !is_array($subjects)) {
                        $unchanged++;
                        $bar->advance();
                        continue;
                    }
                    
                    $calculated = $examType ? $examType->calculateTotalScore($subjects) : $this->simpleCalculate($subjects);
                    $current = floatval($result->total_score ?? 0);
                    
                    // Only update if there's a significant difference
                    if (abs($calculated - $current) > 0.01) {
                        $result->total_score = $calculated;
                        $result->save();
                        $fixed++;
                    } else {
                        $unchanged++;
                    }
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("Error processing seat {$result->seat_number}: " . $e->getMessage());
                }
                
                $bar->advance();
            }
        });
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Fixed {$fixed} records.");
        $this->info("   Unchanged: {$unchanged}");
        
        if ($errors > 0) {
            $this->warn("⚠️  {$errors} errors occurred.");
        }
        
        return $errors > 0 ? 1 : 0;
    }
    
    /**
     * Simple calculation when no exam type is available
     */
    private function simpleCalculate(array $subjects): float
    {
        $total = 0;
        $skipKeys = ['المدرسة', 'الإدارة', 'الادارة', 'المركز', 'الحالة', 'الاسم', 'رقم الجلوس', 'الملاحظات'];
        
        foreach ($subjects as $name => $score) {
            // Skip non-subject fields
            $nameLower = mb_strtolower($name);
            $skip = false;
            foreach ($skipKeys as $key) {
                if (str_contains($nameLower, mb_strtolower($key))) {
                    $skip = true;
                    break;
                }
            }
            
            if ($skip) continue;
            
            // Skip if not numeric
            if (!is_numeric($score)) continue;
            
            $total += floatval($score);
        }
        
        return round($total, 2);
    }
}
