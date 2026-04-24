<?php

namespace App\Services;

use App\Models\ColumnMapping;
use App\Models\Result;
use App\Models\UploadLog;
use App\Models\ExamType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Common\Entity\Row;

class FileImportService
{
    /**
     * Get headers from a spreadsheet file using OpenSpout (memory efficient)
     */
    public function getHeaders(string $filePath): array
    {
        try {
            if (!file_exists($filePath)) {
                Log::error('File not found: ' . $filePath);
                return [];
            }
            
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            
            // For .xls files (old Excel format), use PhpSpreadsheet since OpenSpout doesn't support it
            if ($extension === 'xls') {
                return $this->getHeadersWithPhpSpreadsheet($filePath);
            }
            
            // Use OpenSpout for streaming - only read first 10 rows (for xlsx and csv)
            $reader = match($extension) {
                'xlsx' => new XlsxReader(),
                'csv' => new CsvReader(),
                default => throw new \Exception('Unsupported file type: ' . $extension),
            };
            
            if ($extension === 'csv') {
                $reader->setFieldDelimiter(',');
                $reader->setFieldEnclosure('"');
            }
            
            $reader->open($filePath);
            
            $data = [];
            $rowCount = 0;
            $maxRows = 10;
            
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    if ($rowCount >= $maxRows) break;
                    $data[] = $row->toArray();
                    $rowCount++;
                }
                break; // Only first sheet
            }
            
            $reader->close();
            
            if (empty($data)) {
                Log::error('No data found in file: ' . $filePath);
                return [];
            }
            
            $headerRow = null;
            
            // Detect header row by looking for common header keywords
            $headerKeywords = ['رقم الجلوس', 'الاسم', 'اسم', 'المجموع', 'الدرجة', 'seat', 'name', 'total', 'score'];
            
            foreach ($data as $rowIndex => $row) {
                if (empty($row)) continue;
                
                // Convert row to string for checking
                $rowText = implode(' ', array_map('strval', $row));
                
                // Check if this row contains header keywords
                foreach ($headerKeywords as $keyword) {
                    if (mb_stripos($rowText, $keyword) !== false) {
                        $headerRow = $row;
                        Log::info("Found header row at index: " . $rowIndex);
                        break 2;
                    }
                }
            }
            
            // If no header detected, use first non-empty row
            if (!$headerRow) {
                foreach ($data as $row) {
                    if (!empty(array_filter($row))) {
                        $headerRow = $row;
                        break;
                    }
                }
            }
            
            if (!$headerRow) {
                Log::error('Could not detect header row in file: ' . $filePath);
                return [];
            }
            
            // Filter empty values and return
            $headers = array_values(array_filter($headerRow, function($val) {
                return !is_null($val) && $val !== '';
            }));
            
            return $headers;
        } catch (\Exception $e) {
            Log::error('Error reading headers: ' . $e->getMessage() . ' | File: ' . $filePath);
            return [];
        }
    }

    /**
     * Get headers from .xls files using PhpSpreadsheet (for old Excel format)
     * Uses chunk reading to handle large files efficiently
     */
    private function getHeadersWithPhpSpreadsheet(string $filePath): array
    {
        try {
            // Increase memory limit for large files
            ini_set('memory_limit', '512M');
            
            // Create a chunk read filter to only read first 10 rows
            $chunkFilter = new class implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
                private $startRow = 1;
                private $endRow = 10;
                
                public function readCell($columnAddress, $row, $worksheetName = ''): bool
                {
                    return ($row >= $this->startRow && $row <= $this->endRow);
                }
            };
            
            // Create reader with the filter
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadFilter($chunkFilter);
            $reader->setReadDataOnly(true);
            
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            
            $data = [];
            $rowIndex = 0;
            $maxRows = 10;
            
            foreach ($sheet->getRowIterator() as $row) {
                if ($rowIndex >= $maxRows) break;
                
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(true);
                
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                
                if (!empty($rowData)) {
                    $data[] = $rowData;
                }
                $rowIndex++;
            }
            
            // Clean up immediately
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            
            if (empty($data)) {
                Log::error('No data found in XLS file: ' . $filePath);
                return [];
            }
            
            $headerRow = null;
            $headerKeywords = ['رقم الجلوس', 'الاسم', 'اسم', 'المجموع', 'الدرجة', 'seat', 'name', 'total', 'score'];
            
            foreach ($data as $idx => $row) {
                if (empty($row)) continue;
                
                $rowText = implode(' ', array_map('strval', $row));
                
                foreach ($headerKeywords as $keyword) {
                    if (mb_stripos($rowText, $keyword) !== false) {
                        $headerRow = $row;
                        Log::info("Found header row at index: " . $idx . " in XLS file");
                        break 2;
                    }
                }
            }
            
            if (!$headerRow && !empty($data)) {
                foreach ($data as $row) {
                    if (!empty(array_filter($row))) {
                        $headerRow = $row;
                        break;
                    }
                }
            }
            
            if (!$headerRow) {
                Log::error('Could not detect header row in XLS file: ' . $filePath);
                return [];
            }
            
            $headers = array_values(array_filter($headerRow, function($val) {
                return !is_null($val) && $val !== '';
            }));
            
            Log::info('XLS Headers extracted: ' . count($headers) . ' columns');
            
            return $headers;
            
        } catch (\Exception $e) {
            Log::error('Error reading XLS headers with PhpSpreadsheet: ' . $e->getMessage() . ' | File: ' . $filePath);
            return [];
        }
    }

    /**
     * Import file and process data
     */
    public function import(
        $file,
        int $examTypeId,
        int $academicYearId,
        ?int $governorateId,
        int $userId,
        ?array $columnMapping = null,
        ?UploadLog $existingLog = null
    ): array {
        // Optimize for large files - use streaming with OpenSpout
        set_time_limit(3600); // 60 mins for very large files
        ini_set('memory_limit', '512M'); // Increased slightly but OpenSpout is memory efficient
        
        // Disable query logging
        DB::connection()->disableQueryLog();

        // Handle both file paths and UploadedFile objects
        $filePath = is_string($file) ? $file : $file->getRealPath();
        $filename = is_string($file) ? basename($file) : $file->getClientOriginalName();
        $extension = is_string($file) ? pathinfo($file, PATHINFO_EXTENSION) : $file->getClientOriginalExtension();
        $extension = strtolower($extension);
        
        if ($existingLog) {
            $uploadLog = $existingLog;
            $uploadLog->update(['status' => 'processing']);
        } else {
            $uploadLog = UploadLog::create([
                'user_id' => $userId,
                'filename' => $filename,
                'file_type' => $extension,
                'status' => 'processing',
                'records_count' => 0,
            ]);
        }

        try {
            // New Efficient Path for Excel/CSV
            if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
                $count = $this->importSpreadsheet($filePath, $examTypeId, $academicYearId, $governorateId, $columnMapping, $uploadLog);
                
                $uploadLog->update([
                    'status' => 'completed',
                    'records_count' => $count,
                ]);

                return [
                    'success' => true,
                    'records_count' => $count,
                    'message' => "Successfully imported {$count} records",
                ];
            }

            // Legacy Path for other formats (SQL, SQLite, Access, PDF)
            $data = match($extension) {
                'sql' => $this->processSql($filePath),
                'db', 'sqlite', 'sqlite3' => $this->processSqlite($filePath),
                'mdb', 'mde', 'accdb' => $this->processAccess($filePath),
                'pdf' => $this->processPdf($filePath),
                default => throw new \Exception('Unsupported file type: ' . $extension),
            };

            if (empty($data)) {
                throw new \Exception('No data found in file');
            }

            // If no column mapping provided, detect columns from first row of data
            if (!$columnMapping) {
                $columnMapping = $this->detectColumns($data[0]);
            }

            // Validate Mapping (Basic Fallback)
            if (!isset($columnMapping['seat_number']) && !isset($columnMapping['student_name'])) {
                 $columnMapping['seat_number'] = 0; // Fallback
            }

            $recordsCount = $this->importData(
                $data,
                $columnMapping,
                $examTypeId,
                $academicYearId,
                $governorateId
            );

            $uploadLog->update([
                'status' => 'completed',
                'records_count' => $recordsCount,
            ]);

            // Invalidate page cache after successful import
            \App\Http\Middleware\CacheResponse::invalidateAll();
            CacheService::invalidateStats();

            return [
                'success' => true,
                'records_count' => $recordsCount,
                'message' => "Successfully imported {$recordsCount} records",
            ];

        } catch (\Exception $e) {
            $uploadLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('File import failed', [
                'file' => $filename,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Efficiently import Excel/CSV using OpenSpout (streaming - low memory)
     */
    private function importSpreadsheet($filePath, $examTypeId, $academicYearId, $governorateId, $columnMapping, ?UploadLog $uploadLog = null): int
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // For .xls files (old Excel format), use PhpSpreadsheet since OpenSpout doesn't support it
        if ($extension === 'xls') {
            return $this->importXlsWithPhpSpreadsheet($filePath, $examTypeId, $academicYearId, $governorateId, $columnMapping, $uploadLog);
        }
        
        $importFilePath = $filePath;
        
        // Handle CSV Encoding
        if ($extension === 'csv') {
            $handle = fopen($filePath, 'r');
            $firstLine = fgets($handle);
            fclose($handle);
            
            $encoding = mb_detect_encoding($firstLine ?? '', ['UTF-8', 'Windows-1256', 'ISO-8859-1'], true);
            
            if ($encoding && $encoding !== 'UTF-8') {
                $content = file_get_contents($filePath);
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
                
                $tempFile = tempnam(sys_get_temp_dir(), 'utf8_csv_');
                file_put_contents($tempFile, $content);
                $importFilePath = $tempFile;
            }
        }

        // Use OpenSpout for streaming (memory efficient)
        $reader = match($extension) {
            'xlsx' => new XlsxReader(),
            'xls' => new XlsxReader(), // OpenSpout doesn't support xls, fallback
            'csv' => new CsvReader(),
            default => throw new \Exception('Unsupported file type for streaming: ' . $extension),
        };
        
        if ($extension === 'csv') {
            $reader->setFieldDelimiter(',');
            $reader->setFieldEnclosure('"');
        }
        
        $reader->open($importFilePath);
        
        $headerRow = null;
        $headerRowIndex = 0;
        $rowIndex = 0;
        $headerKeywords = ['رقم الجلوس', 'جلوس', 'الاسم', 'المجموع', 'seat', 'name'];
        
        // First pass: Find header row (read only first 10 rows)
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                if ($rowIndex >= 10) break;
                
                $cells = $row->toArray();
                if (empty(array_filter($cells))) {
                    $rowIndex++;
                    continue;
                }
                
                $rowText = implode(' ', array_map('strval', array_filter($cells)));
                
                foreach ($headerKeywords as $keyword) {
                    if (mb_stripos($rowText, $keyword) !== false) {
                        $headerRow = $cells;
                        $headerRowIndex = $rowIndex;
                        break 2;
                    }
                }
                
                if (!$headerRow) {
                    $headerRow = $cells;
                }
                
                $rowIndex++;
            }
            break; // Only first sheet
        }
        
        $reader->close();
        
        if (!$headerRow) {
            throw new \Exception('Could not detect header row in file.');
        }
        
        // Resolve column mapping
        if ($columnMapping) {
            $headerIndices = [];
            foreach ($headerRow as $index => $headerValue) {
                if (is_string($headerValue) || is_int($headerValue)) {
                    $headerIndices[$headerValue] = $index;
                }
            }
            
            $resolvedMapping = [];
            
            foreach (['seat_number', 'student_name', 'total_score', 'status'] as $field) {
                if (isset($columnMapping[$field]) && isset($headerIndices[$columnMapping[$field]])) {
                    $resolvedMapping[$field] = $headerIndices[$columnMapping[$field]];
                }
            }
            
            $resolvedMapping['subjects'] = [];
            $ignoredColumns = $columnMapping['ignored_columns'] ?? [];
            $standardHeaders = array_values(array_intersect_key($columnMapping, array_flip(['seat_number', 'student_name', 'total_score', 'status'])));
            
            foreach ($headerRow as $index => $header) {
                if (in_array($header, $standardHeaders)) continue;
                if (in_array($header, $ignoredColumns)) continue;
                $resolvedMapping['subjects'][$header] = $index;
            }
            
            $columnMapping = $resolvedMapping;
        } else {
            $columnMapping = $this->detectColumns($headerRow);
        }
        
        // Auto calculate setup
        $autoCalculateTotal = $columnMapping['auto_calculate_total'] ?? false;
        $examType = $autoCalculateTotal ? ExamType::find($examTypeId) : null;
        $branchId = $uploadLog?->branch_id;
        $systemType = $uploadLog?->system_type;
        $uploadLogId = $uploadLog?->id;
        
        // Second pass: Import data using streaming
        $reader = match($extension) {
            'xlsx' => new XlsxReader(),
            'xls' => new XlsxReader(),
            'csv' => new CsvReader(),
            default => throw new \Exception('Unsupported file type'),
        };
        
        if ($extension === 'csv') {
            $reader->setFieldDelimiter(',');
            $reader->setFieldEnclosure('"');
        }
        
        $reader->open($importFilePath);
        
        $totalImported = 0;
        $batch = [];
        $batchSize = 500; // Process 500 rows at a time
        $currentRow = 0;
        
        DB::connection()->disableQueryLog();
        
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                // Skip until after header row
                if ($currentRow <= $headerRowIndex) {
                    $currentRow++;
                    continue;
                }
                
                $cells = $row->toArray();
                
                // Skip empty rows
                if (empty(array_filter($cells))) {
                    $currentRow++;
                    continue;
                }
                
                // Extract subjects data
                $subjectsData = [];
                if (isset($columnMapping['subjects'])) {
                    foreach ($columnMapping['subjects'] as $subjectName => $index) {
                        if (isset($cells[$index])) {
                            $subjectsData[$subjectName] = $cells[$index];
                        }
                    }
                }
                
                // Calculate total score
                $totalScore = null;
                if ($autoCalculateTotal && $examType) {
                    $totalScore = $examType->calculateTotalScore($subjectsData);
                } elseif (isset($columnMapping['total_score'])) {
                    $totalScore = $cells[$columnMapping['total_score']] ?? null;
                }
                
                $batch[] = [
                    'seat_number' => isset($columnMapping['seat_number']) ? ($cells[$columnMapping['seat_number']] ?? null) : null,
                    'student_name' => isset($columnMapping['student_name']) ? ($cells[$columnMapping['student_name']] ?? null) : null,
                    'governorate_id' => $governorateId,
                    'exam_type_id' => $examTypeId,
                    'branch_id' => $branchId,
                    'system_type' => $systemType,
                    'academic_year_id' => $academicYearId,
                    'upload_log_id' => $uploadLogId,
                    'subjects_data' => json_encode($subjectsData, JSON_UNESCAPED_UNICODE),
                    'total_score' => $totalScore,
                    'status' => isset($columnMapping['status']) ? ($cells[$columnMapping['status']] ?? null) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Insert batch when full
                if (count($batch) >= $batchSize) {
                    $this->insertBatch($batch, $uploadLog);
                    $totalImported += count($batch);
                    $batch = [];
                    
                    // Free memory
                    gc_collect_cycles();
                }
                
                $currentRow++;
            }
            break; // Only first sheet
        }
        
        // Insert remaining batch
        if (!empty($batch)) {
            $this->insertBatch($batch, $uploadLog);
            $totalImported += count($batch);
        }
        
        $reader->close();
        
        // Cleanup temp file
        if ($importFilePath !== $filePath && file_exists($importFilePath)) {
            unlink($importFilePath);
        }
        
        return $totalImported;
    }
    
    /**
     * Import .xls files (old Excel format) using PhpSpreadsheet
     */
    private function importXlsWithPhpSpreadsheet($filePath, $examTypeId, $academicYearId, $governorateId, $columnMapping, ?UploadLog $uploadLog = null): int
    {
        Log::info('Importing XLS file with PhpSpreadsheet: ' . $filePath);
        
        // Load the spreadsheet
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        
        // Detect header row
        $headerRow = null;
        $headerRowIndex = 0;
        $headerKeywords = ['رقم الجلوس', 'جلوس', 'الاسم', 'المجموع', 'seat', 'name'];
        
        $rowIterator = $sheet->getRowIterator();
        foreach ($rowIterator as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            
            if (empty(array_filter($cells))) {
                $headerRowIndex++;
                continue;
            }
            
            $rowText = implode(' ', array_map('strval', array_filter($cells)));
            
            foreach ($headerKeywords as $keyword) {
                if (mb_stripos($rowText, $keyword) !== false) {
                    $headerRow = $cells;
                    Log::info("Found header row at index: " . $headerRowIndex . " in XLS import");
                    break 2;
                }
            }
            
            if (!$headerRow) {
                $headerRow = $cells;
            }
            
            $headerRowIndex++;
            
            if ($headerRowIndex >= 10) break;
        }
        
        if (!$headerRow) {
            throw new \Exception('Could not detect header row in XLS file.');
        }
        
        // Resolve column mapping
        if ($columnMapping) {
            $headerIndices = [];
            foreach ($headerRow as $index => $headerValue) {
                if (is_string($headerValue) || is_int($headerValue)) {
                    $headerIndices[$headerValue] = $index;
                }
            }
            
            $resolvedMapping = [];
            
            foreach (['seat_number', 'student_name', 'total_score', 'status'] as $field) {
                if (isset($columnMapping[$field]) && isset($headerIndices[$columnMapping[$field]])) {
                    $resolvedMapping[$field] = $headerIndices[$columnMapping[$field]];
                }
            }
            
            $resolvedMapping['subjects'] = [];
            $ignoredColumns = $columnMapping['ignored_columns'] ?? [];
            $standardHeaders = array_values(array_intersect_key($columnMapping, array_flip(['seat_number', 'student_name', 'total_score', 'status'])));
            
            foreach ($headerRow as $index => $header) {
                if (in_array($header, $standardHeaders)) continue;
                if (in_array($header, $ignoredColumns)) continue;
                $resolvedMapping['subjects'][$header] = $index;
            }
            
            $columnMapping = $resolvedMapping;
        } else {
            $columnMapping = $this->detectColumns($headerRow);
        }
        
        // Auto calculate setup
        $autoCalculateTotal = $columnMapping['auto_calculate_total'] ?? false;
        $examType = $autoCalculateTotal ? ExamType::find($examTypeId) : null;
        $branchId = $uploadLog?->branch_id;
        $systemType = $uploadLog?->system_type;
        $uploadLogId = $uploadLog?->id;
        $semester = $uploadLog?->semester ?? 0;
        
        // Import data
        $totalImported = 0;
        $batch = [];
        $batchSize = 500;
        $currentRow = 0;
        
        DB::connection()->disableQueryLog();
        
        $rowIterator = $sheet->getRowIterator();
        foreach ($rowIterator as $row) {
            // Skip until after header row
            if ($currentRow <= $headerRowIndex) {
                $currentRow++;
                continue;
            }
            
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            
            // Skip empty rows
            if (empty(array_filter($cells))) {
                $currentRow++;
                continue;
            }
            
            // Extract subjects data
            $subjectsData = [];
            if (isset($columnMapping['subjects'])) {
                foreach ($columnMapping['subjects'] as $subjectName => $index) {
                    if (isset($cells[$index])) {
                        $subjectsData[$subjectName] = $cells[$index];
                    }
                }
            }
            
            // Calculate total score
            $totalScore = null;
            if ($autoCalculateTotal && $examType) {
                $totalScore = $examType->calculateTotalScore($subjectsData);
            } elseif (isset($columnMapping['total_score'])) {
                $totalScore = $cells[$columnMapping['total_score']] ?? null;
            }
            
            $batch[] = [
                'seat_number' => isset($columnMapping['seat_number']) ? ($cells[$columnMapping['seat_number']] ?? null) : null,
                'student_name' => isset($columnMapping['student_name']) ? ($cells[$columnMapping['student_name']] ?? null) : null,
                'governorate_id' => $governorateId,
                'exam_type_id' => $examTypeId,
                'branch_id' => $branchId,
                'system_type' => $systemType,
                'semester' => $semester,
                'academic_year_id' => $academicYearId,
                'upload_log_id' => $uploadLogId,
                'subjects_data' => json_encode($subjectsData, JSON_UNESCAPED_UNICODE),
                'total_score' => $totalScore,
                'status' => isset($columnMapping['status']) ? ($cells[$columnMapping['status']] ?? null) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Insert batch when full
            if (count($batch) >= $batchSize) {
                $this->insertBatch($batch, $uploadLog);
                $totalImported += count($batch);
                $batch = [];
                
                // Free memory
                gc_collect_cycles();
            }
            
            $currentRow++;
        }
        
        // Insert remaining batch
        if (!empty($batch)) {
            $this->insertBatch($batch, $uploadLog);
            $totalImported += count($batch);
        }
        
        // Free memory
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        
        Log::info('XLS Import completed: ' . $totalImported . ' records');
        
        return $totalImported;
    }
    
    /**
     * Insert a batch of records
     */
    private function insertBatch(array $records, ?UploadLog $uploadLog): void
    {
        DB::transaction(function () use ($records, $uploadLog) {
            // Insert in smaller chunks to avoid query size limits
            foreach (array_chunk($records, 100) as $chunk) {
                Result::insert($chunk);
            }
        });
        
        if ($uploadLog) {
            $count = count($records);
            DB::table('upload_logs')
                ->where('id', $uploadLog->id)
                ->update([
                    'processed_rows' => DB::raw('processed_rows + ' . $count),
                    'successful_rows' => DB::raw('successful_rows + ' . $count),
                    'records_count' => DB::raw('COALESCE(records_count, 0) + ' . $count),
                ]);
        }
    }

    /**
     * Preview file content (memory efficient)
     */
    public function preview($file, int $limit = 5): array
    {
        $filePath = is_string($file) ? $file : $file->getRealPath();
        $extension = is_string($file) ? pathinfo($file, PATHINFO_EXTENSION) : $file->getClientOriginalExtension();
        $extension = strtolower($extension);

        try {
            // For Excel/CSV, use streaming preview
            if (in_array($extension, ['xlsx', 'xls', 'xsl', 'csv'])) {
                return $this->streamingPreview($filePath, $extension, $limit);
            }
            
            // Legacy handling for other file types
            $data = match($extension) {
                'sql' => [],
                'db', 'sqlite', 'sqlite3' => $this->processSqlite($filePath),
                'mdb', 'mde', 'accdb' => $this->processAccess($filePath),
                'pdf' => $this->processPdf($filePath),
                default => throw new \Exception('Unsupported file type: ' . $extension),
            };

            if (empty($data)) {
                return [
                    'headers' => [],
                    'rows' => [],
                    'total_rows' => 0
                ];
            }

            $headers = $data[0];
            $rows = array_slice($data, 1, $limit);

            return [
                'headers' => $headers,
                'rows' => $rows,
                'total_rows' => count($data) - 1
            ];

        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Streaming preview for large Excel/CSV files
     */
    private function streamingPreview(string $filePath, string $extension, int $limit): array
    {
        $reader = match($extension) {
            'xlsx', 'xls', 'xsl' => new XlsxReader(),
            'csv' => new CsvReader(),
            default => throw new \Exception('Unsupported extension: ' . $extension),
        };
        
        if ($extension === 'csv') {
            $reader->setFieldDelimiter(',');
            $reader->setFieldEnclosure('"');
        }
        
        $reader->open($filePath);
        
        $headers = [];
        $rows = [];
        $rowCount = 0;
        $totalRows = 0;
        
        // Read only what we need (header + limit rows + count total)
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->toArray();
                
                if (empty(array_filter($cells))) {
                    continue;
                }
                
                $totalRows++;
                
                if ($rowCount === 0) {
                    $headers = $cells;
                } elseif ($rowCount <= $limit) {
                    $rows[] = $cells;
                }
                
                $rowCount++;
                
                // For very large files, estimate total instead of counting all
                if ($totalRows > 1000) {
                    // Stop counting and estimate based on file size
                    $fileSize = filesize($filePath);
                    $avgRowSize = $fileSize / $totalRows;
                    $estimatedTotal = (int)($fileSize / $avgRowSize);
                    
                    $reader->close();
                    
                    return [
                        'headers' => $headers,
                        'rows' => $rows,
                        'total_rows' => $estimatedTotal,
                        'estimated' => true
                    ];
                }
            }
            break;
        }
        
        $reader->close();
        
        return [
            'headers' => $headers,
            'rows' => $rows,
            'total_rows' => $totalRows - 1 // Exclude header
        ];
    }

    /**
     * Process Excel file using OpenSpout (memory efficient, limited rows for preview)
     */
    private function processExcel(string $filePath, int $maxRows = 100): array
    {
        $data = [];
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        $reader = match($extension) {
            'xlsx', 'xsl' => new XlsxReader(),
            'xls' => new XlsxReader(),
            default => new XlsxReader(),
        };
        
        $reader->open($filePath);
        $rowCount = 0;
        
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                if ($rowCount >= $maxRows) break;
                $cells = $row->toArray();
                if (!empty(array_filter($cells))) {
                    $data[] = $cells;
                }
                $rowCount++;
            }
            break; // Only first sheet
        }
        
        $reader->close();
        return $data;
    }

    /**
     * Process CSV file
     */
    private function processCsv(string $filePath): array
    {
        $data = [];
        $handle = fopen($filePath, 'r');
        
        // Try to detect encoding
        $firstLine = fgets($handle);
        rewind($handle);
        
        $encoding = mb_detect_encoding($firstLine, ['UTF-8', 'Windows-1256', 'ISO-8859-1'], true);
        
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if ($encoding !== 'UTF-8') {
                $row = array_map(function($item) use ($encoding) {
                    return mb_convert_encoding($item, 'UTF-8', $encoding);
                }, $row);
            }
            
            if (!empty(array_filter($row))) {
                $data[] = $row;
            }
        }
        
        fclose($handle);
        return $data;
    }

    /**
     * Process SQL file
     */
    private function processSql(string $filePath): array
    {
        // For SQL files, we'll execute them directly
        $sql = file_get_contents($filePath);
        
        // Basic validation
        if (stripos($sql, 'DROP') !== false || stripos($sql, 'DELETE') !== false) {
            throw new \Exception('SQL file contains dangerous operations');
        }

        // Execute SQL
        DB::unprepared($sql);
        
        return [];
    }

    /**
     * Process SQLite file
     */
    private function processSqlite(string $filePath): array
    {
        try {
            // Connect to SQLite database
            $pdo = new \PDO("sqlite:" . $filePath);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Get the first table name
            $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' LIMIT 1");
            $table = $stmt->fetchColumn();

            if (!$table) {
                throw new \Exception('No tables found in SQLite database');
            }

            // Get all data from the table
            $stmt = $pdo->query("SELECT * FROM \"$table\"");
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                return [];
            }

            // Format data: header row + data rows
            $data = [];
            $headers = array_keys($rows[0]);
            $data[] = $headers;

            foreach ($rows as $row) {
                $data[] = array_values($row);
            }

            return $data;

        } catch (\PDOException $e) {
            throw new \Exception('SQLite Error: ' . $e->getMessage());
        }
    }

    /**
     * Process Access (MDB/ACCDB) file
     */
    private function processAccess(string $filePath): array
    {
        // Try to use mdb-export (from mdbtools)
        // Command: mdb-tables -1 db.mdb | head -n 1
        $tablesCmd = "mdb-tables -1 " . escapeshellarg($filePath) . " | head -n 1";
        $tableName = trim(shell_exec($tablesCmd) ?? '');

        if (!empty($tableName)) {
            // Export table to CSV format
            $exportCmd = "mdb-export " . escapeshellarg($filePath) . " " . escapeshellarg($tableName);
            $output = shell_exec($exportCmd);

            if ($output) {
                $lines = explode("\n", $output);
                $data = [];
                foreach ($lines as $line) {
                    if (trim($line)) {
                        $row = str_getcsv($line);
                        
                        // Clean up quotes if mdb-export adds them aggressively
                        $data[] = $row;
                    }
                }
                return $data;
            }
        }

        // Fallback: Check if we have ODBC driver
        // This is unlikely to work without system configuration, but included for completeness
        try {
            if (in_array('odbc', get_loaded_extensions())) {
                // This connection string is highly dependent on system driver configuration
                // $pdo = new \PDO("odbc:Driver=MDBTools;DBQ=$filePath;");
                // Implementing this blindly is risky. 
            }
        } catch (\Exception $e) {
            // Ignore
        }

        throw new \Exception('Could not process Access file. Please ensure "mdbtools" is installed on the server.');
    }

    /**
     * Process PDF file
     */
    private function processPdf(string $filePath): array
    {
        $data = [];
        
        // Method 1: Try Smalot\PdfParser if installed
        if (class_exists('Smalot\PdfParser\Parser')) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filePath);
                $text = $pdf->getText();
                
                // Extremely basic parsing: Split by newlines
                $lines = explode("\n", $text);
                
                // Try to detect headers from first non-empty line
                $headersFound = false;
                
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    
                    // Split by multiple spaces expecting tabular data
                    $row = preg_split('/\s{2,}/', $line);
                    
                    if (count($row) > 1) {
                         $data[] = $row;
                    }
                }
                
                if (!empty($data)) return $data;
                
            } catch (\Exception $e) {
                Log::warning('PDF Parser failed: ' . $e->getMessage());
            }
        }

        // Method 2: pdftotext (poppler-utils)
        $output = shell_exec("pdftotext -layout " . escapeshellarg($filePath) . " - 2>/dev/null");
        if ($output) {
            $lines = explode("\n", $output);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                // Split by multiple spaces (layout mode usually preserves spacing)
                $row = preg_split('/\s{2,}/', $line);
                if (count($row) > 0) {
                    $data[] = $row;
                }
            }
            return $data;
        }

        throw new \Exception('Could not read PDF. Please install "smalot/pdfparser" via composer or "poppler-utils" on the server.');
    }

    /**
     * Auto-detect columns from first row
     */
    private function detectColumns(array $firstRow): array
    {
        $mapping = [];
        
        foreach ($firstRow as $index => $columnName) {
            $columnName = trim($columnName);
            
            // Try to match common column names
            if (preg_match('/(رقم.*جلوس|seat.*number)/i', $columnName)) {
                $mapping['seat_number'] = $index;
            } elseif (preg_match('/(اسم|name)/i', $columnName)) {
                $mapping['student_name'] = $index;
            } elseif (preg_match('/(مجموع|total)/i', $columnName)) {
                $mapping['total_score'] = $index;
            } elseif (preg_match('/(حالة|status)/i', $columnName)) {
                $mapping['status'] = $index;
            } else {
                // Assume it's a subject
                $mapping['subjects'][$columnName] = $index;
            }
        }
        
        return $mapping;
    }

    /**
     * Import data into database
     */
    private function importData(
        array $data,
        array $columnMapping,
        int $examTypeId,
        int $academicYearId,
        ?int $governorateId
    ): int {
        $headers = array_shift($data); // Remove header row
        $recordsCount = 0;

        DB::beginTransaction();
        
        try {
            // Process in chunks for better performance
            $chunks = array_chunk($data, 1000);
            
            foreach ($chunks as $chunk) {
                $records = [];
                
                foreach ($chunk as $row) {
                    // Extract subject data
                    $subjectsData = [];
                    if (isset($columnMapping['subjects'])) {
                        foreach ($columnMapping['subjects'] as $subjectName => $index) {
                            if (isset($row[$index])) {
                                $subjectsData[$subjectName] = $row[$index];
                            }
                        }
                    }

                    $records[] = [
                        'seat_number' => $row[$columnMapping['seat_number']] ?? null,
                        'student_name' => $row[$columnMapping['student_name']] ?? null,
                        'governorate_id' => $governorateId,
                        'exam_type_id' => $examTypeId,
                        'academic_year_id' => $academicYearId,
                        'subjects_data' => json_encode($subjectsData, JSON_UNESCAPED_UNICODE),
                        'total_score' => $row[$columnMapping['total_score']] ?? null,
                        'status' => isset($columnMapping['status']) ? ($row[$columnMapping['status']] ?? null) : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                Result::insert($records);
                $recordsCount += count($records);
            }

            DB::commit();
            return $recordsCount;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get detected columns from file for mapping interface
     */
    public function getFileColumns($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        $data = match($extension) {
            'xlsx', 'xls', 'xsl' => $this->processExcel($file),
            'csv' => $this->processCsv($file),
            'db', 'sqlite', 'sqlite3' => $this->processSqlite($file),
            'mdb', 'mde', 'accdb' => $this->processAccess($file),
            'pdf' => $this->processPdf($file),
            default => [],
        };

        return !empty($data) ? $data[0] : [];
    }
}
