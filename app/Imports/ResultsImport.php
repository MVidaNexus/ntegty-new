<?php

namespace App\Imports;

use App\Models\Result;
use App\Models\UploadLog;
use App\Models\ExamType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class ResultsImport implements ToCollection, WithChunkReading, WithStartRow, WithCustomCsvSettings
{
    protected $examTypeId;
    protected $academicYearId;
    protected $governorateId;
    protected $branchId;
    protected $columnMapping;
    protected $uploadLog;
    protected $startRowNumber;
    protected $autoCalculateTotal;
    protected $examType;
    public $recordsCount = 0;

    /**
     * Helper function to check if a value is an Excel formula
     */
    private function isFormula($value): bool
    {
        if ($value === null || $value === '') {
            return true; // Treat empty as formula (skip)
        }
        $str = trim((string)$value);
        // Check if starts with = or is just =
        if (str_starts_with($str, '=')) {
            return true;
        }
        // Check for formula functions without = (sometimes Excel strips it)
        if (preg_match('/^(SUM|AVERAGE|COUNT|MIN|MAX|IF|SUMIF)\s*\(/i', $str)) {
            return true;
        }
        return false;
    }

    /**
     * Helper function to clean a numeric value
     */
    private function cleanNumericValue($value)
    {
        if ($this->isFormula($value)) {
            return null;
        }
        if (is_numeric($value)) {
            return $value;
        }
        return $value; // Return as-is for text values
    }

    public function __construct(
        int $examTypeId, 
        int $academicYearId, 
        ?int $governorateId, 
        array $columnMapping,
        ?UploadLog $uploadLog = null,
        int $startRowNumber = 2,
        ?int $branchId = null
    ) {
        $this->examTypeId = $examTypeId;
        $this->academicYearId = $academicYearId;
        $this->governorateId = $governorateId;
        $this->branchId = $branchId ?? ($uploadLog?->branch_id);
        $this->columnMapping = $columnMapping;
        $this->uploadLog = $uploadLog;
        $this->startRowNumber = $startRowNumber;
        $this->autoCalculateTotal = $columnMapping['auto_calculate_total'] ?? false;
        
        // Load exam type for auto calculation
        if ($this->autoCalculateTotal) {
            $this->examType = ExamType::find($examTypeId);
        }
    }

    public function collection(Collection $rows)
    {
        $records = [];
        
        foreach ($rows as $row) {
            // Check if row is empty
            if ($row->filter()->isEmpty()) {
                continue;
            }

            // Extract subject data
            $subjectsData = [];
            
            // Add administration if mapped
            if (isset($this->columnMapping['administration']) && isset($row[$this->columnMapping['administration']])) {
                $adminValue = $row[$this->columnMapping['administration']];
                if (!empty($adminValue)) {
                    $subjectsData['الإدارة'] = $adminValue;
                }
            }
            
            // Add school if mapped
            if (isset($this->columnMapping['school']) && isset($row[$this->columnMapping['school']])) {
                $schoolValue = $row[$this->columnMapping['school']];
                if (!empty($schoolValue)) {
                    $subjectsData['المدرسة'] = $schoolValue;
                }
            }
            
            if (isset($this->columnMapping['subjects'])) {
                foreach ($this->columnMapping['subjects'] as $subjectName => $index) {
                    if (isset($row[$index])) {
                        $value = $row[$index];
                        // Skip Excel formulas in subjects
                        if ($this->isFormula($value)) {
                            continue; // Don't add formula fields
                        }
                        $subjectsData[$subjectName] = $value;
                    }
                }
            }

            // Always calculate total score automatically (ignore formula columns)
            // Load exam type if not loaded
            if (!$this->examType) {
                $this->examType = ExamType::find($this->examTypeId);
            }
            
            $totalScore = null;
            if ($this->examType) {
                $totalScore = $this->examType->calculateTotalScore($subjectsData);
            }
            
            // If we couldn't calculate and there's a mapped total column with a valid number, use it
            if ($totalScore === null && isset($this->columnMapping['total_score'])) {
                $rawTotal = $row[$this->columnMapping['total_score']] ?? null;
                if ($rawTotal !== null && !$this->isFormula($rawTotal) && is_numeric($rawTotal)) {
                    $totalScore = $rawTotal;
                }
            }

            $records[] = [
                'seat_number' => isset($this->columnMapping['seat_number']) ? ($row[$this->columnMapping['seat_number']] ?? null) : null,
                'student_name' => isset($this->columnMapping['student_name']) ? ($row[$this->columnMapping['student_name']] ?? null) : null,
                'governorate_id' => $this->governorateId,
                'exam_type_id' => $this->examTypeId,
                'branch_id' => $this->branchId,
                'system_type' => $this->uploadLog ? $this->uploadLog->system_type : null,
                'semester' => $this->uploadLog ? ($this->uploadLog->semester ?? 0) : 0,
                'academic_year_id' => $this->academicYearId,
                'upload_log_id' => $this->uploadLog ? $this->uploadLog->id : null,
                'subjects_data' => json_encode($subjectsData, JSON_UNESCAPED_UNICODE),
                'total_score' => $totalScore,
                'status' => isset($this->columnMapping['status']) ? ($row[$this->columnMapping['status']] ?? null) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($records)) {
            // Use transaction for better performance
            \DB::transaction(function () use ($records) {
                // Insert in smaller batches to reduce memory
                foreach (array_chunk($records, 100) as $batch) {
                    Result::insert($batch);
                }
            });
            
            $count = count($records);
            $this->recordsCount += $count;
            
            if ($this->uploadLog) {
                // Update in single query instead of increment
                \DB::table('upload_logs')
                    ->where('id', $this->uploadLog->id)
                    ->update([
                        'processed_rows' => \DB::raw('processed_rows + ' . $count),
                        'successful_rows' => \DB::raw('successful_rows + ' . $count),
                        'records_count' => \DB::raw('processed_rows + ' . $count),
                    ]);
            }
            
            // Free memory aggressively
            $records = null;
            unset($records);
            gc_collect_cycles();
            
            // Clear any query logs
            \DB::connection()->flushQueryLog();
        }
    }

    public function startRow(): int
    {
        return $this->startRowNumber;
    }

    public function chunkSize(): int
    {
        return 200; // Reduced from 500 to save memory
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
        ];
    }
}
