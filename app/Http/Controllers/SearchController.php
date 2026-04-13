<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\AcademicYear;
use App\Services\CacheService;
use App\Services\SchemaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    /**
     * Search for student results
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1',
            'exam_type_id' => 'required|exists:exam_types,id',
            'governorate_id' => 'nullable|exists:governorates,id',
        ]);

        $query = $request->input('query');
        $examTypeId = $request->input('exam_type_id');
        $governorateId = $request->input('governorate_id');
        $systemType = $request->input('system_type');
        $branchId = $request->input('branch_id');
        $academicYearId = $request->input('academic_year_id');
        
        // Determine academic year
        if ($academicYearId) {
            $academicYear = AcademicYear::find($academicYearId);
        } else {
            $academicYear = AcademicYear::where('is_active', true)->first();
        }
        
        if (!$academicYear) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد سنة دراسية محددة',
            ]);
        }

        // NOTE: We don't use cache for results anymore to ensure ranks are always fresh

        // Build query
        $resultsQuery = Result::with(['governorate', 'examType', 'academicYear', 'branch'])
            ->where('exam_type_id', $examTypeId)
            ->where('academic_year_id', $academicYear->id);

        if ($governorateId) {
            $resultsQuery->where('governorate_id', $governorateId);
        }

        if ($systemType) {
            $resultsQuery->where('system_type', $systemType);
        }

        if ($branchId) {
            $resultsQuery->where('branch_id', $branchId);
        }

        // Search by seat number or name
        // Search by seat number or name
        if (is_numeric($query)) {
            // Search by seat number
            $resultsQuery->where('seat_number', $query);
        } else {
            // Validate name length (at least 3 words)
            $nameParts = array_filter(explode(' ', trim($query)));
            if (count($nameParts) < 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'عفواً، يجب كتابة الاسم ثلاثي على الأقل لضمان دقة البحث',
                ]);
            }

            // Prepare the "wildcarded" version of the query for fuzzy matching of specific internal chars
            // 1. Replace Taa Marbuta (ة) with wildcard (_)
            // 2. Replace Haa (ه) at the end of words with wildcard (_)
            // 3. Replace Yaa (ي) and Alif Maqsura (ى) at the end of words with wildcard (_)
            $processedQuery = str_replace('ة', '_', $query);
            $processedQuery = preg_replace('/ه(?=\s|$)/u', '_', $processedQuery);
            $processedQuery = preg_replace('/[يى](?=\s|$)/u', '_', $processedQuery);

            // Handle Arabic Aleph variations for the first letter to ensure index usage (Starts With)
            $firstChar = mb_substr($processedQuery, 0, 1);
            $alephs = ['ا', 'أ', 'إ', 'آ'];
            
            if (in_array($firstChar, $alephs)) {
                // Remove the first char (which is an Aleph or wildcarded version of it)
                $suffix = mb_substr($processedQuery, 1);
                $resultsQuery->where(function ($q) use ($alephs, $suffix) {
                    foreach ($alephs as $aleph) {
                        $q->orWhere('student_name', 'like', $aleph . $suffix . '%');
                    }
                });
            } else {
                // Standard StartsWith search with internal wildcards
                $resultsQuery->where('student_name', 'like', $processedQuery . '%');
            }
        }

        $results = $resultsQuery->limit(10)->get();

        if ($results->isEmpty()) {
            // تحديد الرسالة المناسبة حسب حالة المحافظة
            $message = 'لم يتم العثور على نتيجة، برجاء مراجعة المدرسة';
            $notDeclared = false;
            
            if ($governorateId) {
                $governorate = \App\Models\Governorate::find($governorateId);
                if ($governorate && !$governorate->is_declared) {
                    $message = 'النتيجة لم تُعتمد بعد في محافظة ' . $governorate->name_ar . '. يرجى المتابعة لاحقاً.';
                    $notDeclared = true;
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => $message,
                'not_declared' => $notDeclared,
            ]);
        }

        $mappedResults = $results->map(function ($result) use ($examTypeId, $academicYear) {
                // Calculate status automatically if enabled
                $status = $result->status; // Use existing status if available
                
                // Calculate total from subjects if total_score is null or zero
                $totalScore = $result->total_score;
                if (($totalScore === null || $totalScore == 0) && $result->examType && !empty($result->subjects_data)) {
                    $totalScore = $result->examType->calculateTotalScore($result->subjects_data);
                    
                    // Save to database for future queries
                    if ($totalScore > 0) {
                        $result->total_score = $totalScore;
                        $result->saveQuietly();
                    }
                }
                
                // Get semester from result (0 = both, 1 = first, 2 = second)
                $semester = $result->semester ?? 0;
                
                if ($result->examType && $result->examType->auto_calculate_status && $totalScore !== null) {
                    $status = $result->examType->calculateStatus($totalScore, $semester);
                }

                // Check if exam is unified (no governorate required)
                $isUnified = $result->examType?->is_unified ?? false;
                
                // Check if this is secondary exam (has branches)
                $isSecondary = str_contains($result->examType?->code ?? '', 'secondary');
                
                // Initialize rank variables
                $rank = null;
                $totalInGovernorate = null;
                $adminRank = null;
                $totalInAdmin = null;
                $schoolRank = null;
                $totalInSchool = null;
                $countryRank = null;
                $totalInCountry = null;
                $branchRank = null;
                $totalInBranch = null;
                
                // Get administration and school from subjects_data (handle all Arabic and English variants)
                $administration = $result->subjects_data['الادارة'] ?? $result->subjects_data['الاداره'] ?? $result->subjects_data['الإدارة'] ?? $result->subjects_data['الإداره'] ?? $result->subjects_data['الإدارة التعليمية'] ?? $result->subjects_data['اسم الإدارة'] ?? $result->subjects_data['اسم الاداره'] ?? $result->subjects_data['اسم الادارة'] ?? $result->subjects_data['EDARA'] ?? $result->subjects_data['Edara'] ?? $result->subjects_data['edara'] ?? null;
                $school = $result->subjects_data['المدرسة'] ?? $result->subjects_data['المدرسه'] ?? $result->subjects_data['اسم المدرسة'] ?? $result->subjects_data['اسم المدرسه'] ?? $result->subjects_data['SCHOOL'] ?? $result->subjects_data['School'] ?? $result->subjects_data['school'] ?? null;
                
                // Calculate ranks only if we have a score - USE CACHE for totals
                if ($totalScore !== null && $totalScore > 0) {
                    
                    // For Secondary: Rank in Country and Branch (filtered by system_type)
                    if ($isSecondary) {
                        // Get student's system type for filtering
                        $studentSystemType = $result->system_type;
                        $systemTypeSuffix = $studentSystemType ? "_{$studentSystemType}" : "";
                        
                        // Cache key for country totals (including system_type for secondary)
                        $countryTotalKey = "country_total_{$examTypeId}_{$academicYear->id}{$systemTypeSuffix}";
                        $totalInCountry = Cache::remember($countryTotalKey, 300, function() use ($examTypeId, $academicYear, $studentSystemType) {
                            $countryQuery = Result::where('exam_type_id', $examTypeId)
                                ->where('academic_year_id', $academicYear->id);
                            // Filter by system_type for accurate ranking
                            if ($studentSystemType) {
                                $countryQuery->where('system_type', $studentSystemType);
                            }
                            return $countryQuery->count();
                        });
                        
                        // Cache rank calculation (filtered by system_type)
                        $countryRankKey = "country_rank_{$examTypeId}_{$academicYear->id}{$systemTypeSuffix}_{$totalScore}";
                        $countryRank = Cache::remember($countryRankKey, 60, function() use ($examTypeId, $academicYear, $studentSystemType, $totalScore) {
                            $countryRankQuery = Result::where('exam_type_id', $examTypeId)
                                ->where('academic_year_id', $academicYear->id)
                                ->where('total_score', '>', $totalScore);
                            // Filter by system_type for accurate ranking
                            if ($studentSystemType) {
                                $countryRankQuery->where('system_type', $studentSystemType);
                            }
                            return $countryRankQuery->count() + 1;
                        });
                        
                        // Branch rank = Country rank for secondary
                        if ($result->branch_id) {
                            $branchRank = $countryRank;
                            $totalInBranch = $totalInCountry;
                        }
                    }
                    
                    // For non-unified exams: Rank in Governorate
                    if ($result->governorate_id && !$isUnified) {
                        // Cache governorate total
                        $govTotalKey = "gov_total_{$examTypeId}_{$academicYear->id}_{$result->governorate_id}" . ($result->branch_id ? "_{$result->branch_id}" : "");
                        $totalInGovernorate = Cache::remember($govTotalKey, 300, function() use ($examTypeId, $academicYear, $result) {
                            $govQuery = Result::where('exam_type_id', $examTypeId)
                                ->where('academic_year_id', $academicYear->id)
                                ->where('governorate_id', $result->governorate_id);
                            if ($result->branch_id) {
                                $govQuery->where('branch_id', $result->branch_id);
                            }
                            return $govQuery->count();
                        });
                        
                        // Cache governorate rank
                        $govRankKey = "gov_rank_{$examTypeId}_{$academicYear->id}_{$result->governorate_id}" . ($result->branch_id ? "_{$result->branch_id}" : "") . "_{$totalScore}";
                        $rank = Cache::remember($govRankKey, 60, function() use ($examTypeId, $academicYear, $result, $totalScore) {
                            $govRankQuery = Result::where('exam_type_id', $examTypeId)
                                ->where('academic_year_id', $academicYear->id)
                                ->where('governorate_id', $result->governorate_id)
                                ->where('total_score', '>', $totalScore);
                            if ($result->branch_id) {
                                $govRankQuery->where('branch_id', $result->branch_id);
                            }
                            return $govRankQuery->count() + 1;
                        });
                        
                        // Administration rank - using indexed column search
                        if ($administration) {
                            // Cache admin total with md5 hash of admin name
                            $adminHash = md5($administration);
                            $adminTotalKey = "admin_total_{$examTypeId}_{$academicYear->id}_{$result->governorate_id}_{$adminHash}";
                            $totalInAdmin = Cache::remember($adminTotalKey, 300, function() use ($examTypeId, $academicYear, $result, $administration) {
                                return Result::where('exam_type_id', $examTypeId)
                                    ->where('academic_year_id', $academicYear->id)
                                    ->where('governorate_id', $result->governorate_id)
                                    ->where(function($q) use ($administration) {
                                        $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.الادارة')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.الاداره')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"الإدارة\"')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"الإداره\"')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم الإدارة\"')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم الاداره\"')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم الادارة\"')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.EDARA')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.Edara')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.edara')) = ?", [$administration]);
                                    })->count();
                            });
                            
                            // Cache admin rank
                            $adminRankKey = "admin_rank_{$examTypeId}_{$academicYear->id}_{$result->governorate_id}_{$adminHash}_{$totalScore}";
                            $adminRank = Cache::remember($adminRankKey, 60, function() use ($examTypeId, $academicYear, $result, $administration, $totalScore) {
                                return Result::where('exam_type_id', $examTypeId)
                                    ->where('academic_year_id', $academicYear->id)
                                    ->where('governorate_id', $result->governorate_id)
                                    ->where('total_score', '>', $totalScore)
                                    ->where(function($q) use ($administration) {
                                        $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.الادارة')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.الاداره')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"الإدارة\"')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"الإداره\"')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم الإدارة\"')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم الاداره\"')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم الادارة\"')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.EDARA')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.Edara')) = ?", [$administration])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.edara')) = ?", [$administration]);
                                    })->count() + 1;
                            });
                        }
                        
                        // School rank - using indexed column search
                        if ($school) {
                            // Cache school total with md5 hash of school name
                            $schoolHash = md5($school);
                            $schoolTotalKey = "school_total_{$examTypeId}_{$academicYear->id}_{$result->governorate_id}_{$schoolHash}";
                            $totalInSchool = Cache::remember($schoolTotalKey, 300, function() use ($examTypeId, $academicYear, $result, $school) {
                                return Result::where('exam_type_id', $examTypeId)
                                    ->where('academic_year_id', $academicYear->id)
                                    ->where('governorate_id', $result->governorate_id)
                                    ->where(function($q) use ($school) {
                                        $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.المدرسة')) = ?", [$school])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.المدرسه')) = ?", [$school])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم المدرسة\"')) = ?", [$school])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم المدرسه\"')) = ?", [$school])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.SCHOOL')) = ?", [$school])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.School')) = ?", [$school])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.school')) = ?", [$school]);
                                    })->count();
                            });
                            
                            // Cache school rank
                            $schoolRankKey = "school_rank_{$examTypeId}_{$academicYear->id}_{$result->governorate_id}_{$schoolHash}_{$totalScore}";
                            $schoolRank = Cache::remember($schoolRankKey, 60, function() use ($examTypeId, $academicYear, $result, $school, $totalScore) {
                                return Result::where('exam_type_id', $examTypeId)
                                    ->where('academic_year_id', $academicYear->id)
                                    ->where('governorate_id', $result->governorate_id)
                                    ->where('total_score', '>', $totalScore)
                                    ->where(function($q) use ($school) {
                                        $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.المدرسة')) = ?", [$school])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.المدرسه')) = ?", [$school])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم المدرسة\"')) = ?", [$school])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم المدرسه\"')) = ?", [$school])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.SCHOOL')) = ?", [$school])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.School')) = ?", [$school])
                                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.school')) = ?", [$school]);
                                    })->count() + 1;
                            });
                        }
                    }
                }
                
                // Get semester settings for correct total_score display
                $semesterSettings = $result->examType?->getSettingsForSemester($semester) ?? [];
                $displayTotalScore = $semesterSettings['total_score'] ?? $result->examType?->total_score;
                
                return [
                    'id' => $result->id,
                    'seat_number' => $result->seat_number,
                    'student_name' => $result->student_name,
                    'governorate' => $result->governorate?->name_ar,
                    'governorate_slug' => $result->governorate?->slug,
                    'total_score' => $totalScore ?? $result->total_score,
                    'status' => $status ?: 'غير محدد',
                    'subjects' => $result->subjects_data,
                    'system_type' => $result->system_type,
                    'system_type_label' => $result->system_type === 'old' ? 'نظام قديم' : ($result->system_type === 'new' ? 'نظام حديث' : null),
                    'branch' => $result->branch?->name_ar,
                    'branch_code' => $result->branch?->code,
                    'branch_id' => $result->branch_id,
                    'branch_total_score' => $result->branch?->total_score ?? $displayTotalScore,
                    'exam_total_score' => $displayTotalScore,
                    'semester' => $semester,
                    'semester_label' => match($semester) {
                        1 => 'الترم الأول',
                        2 => 'الترم الثاني',
                        default => 'الترمين',
                    },
                    'is_unified' => $isUnified,
                    'is_secondary' => $isSecondary,
                    'rank' => $rank,
                    'total_students' => $totalInGovernorate,
                    'country_rank' => $countryRank,
                    'total_in_country' => $totalInCountry,
                    'branch_rank' => $branchRank,
                    'total_in_branch' => $totalInBranch,
                    'admin_rank' => $adminRank,
                    'total_in_admin' => $totalInAdmin,
                    'administration' => $administration,
                    'school_rank' => $schoolRank,
                    'total_in_school' => $totalInSchool,
                    'school' => $school,
                    'exam_type_code' => $result->examType?->code,
                    'absent_markers' => $result->examType?->getAbsentMarkers() ?? \App\Models\ExamType::DEFAULT_ABSENT_MARKERS,
                    'excluded_subjects' => $result->examType?->excluded_subjects ?? [],
                    // New fields for URL building
                    'academic_year_slug' => $academicYear?->year ?? (date('Y') . '-' . (date('Y') + 1)),
                    'term_slug' => match($semester) {
                        1 => 'term1',
                        2 => 'term2',
                        default => 'term1',
                    },
                ];
            });
            
        // NOTE: We don't cache results anymore to ensure ranks are always fresh
        
        return response()->json([
            'success' => true,
            'results' => $mappedResults,
        ]);
    }

    /**
     * Show individual result
     */
    public function show($id)
    {
        $result = Result::with(['governorate', 'examType', 'examType.country', 'academicYear'])
            ->findOrFail($id);
        
        // Generate structured data for result page
        $structuredData = SchemaService::resultPage($result);

        return view('result.show', compact('result', 'structuredData'));
    }

    /**
     * Print result
     */
    public function print($id)
    {
        $result = Result::with(['governorate', 'examType', 'academicYear'])
            ->findOrFail($id);

        return view('result.print', compact('result'));
    }

    /**
     * Parse excluded subjects - handles both array and comma-separated string
     */
    private function parseExcludedSubjects($excludedSubjects): array
    {
        if (empty($excludedSubjects)) {
            return [];
        }

        // If already an array, return it
        if (is_array($excludedSubjects)) {
            return $excludedSubjects;
        }

        // If it's a string, try to decode as JSON first
        if (is_string($excludedSubjects)) {
            // Remove quotes if wrapped in quotes (stored as JSON string)
            $trimmed = trim($excludedSubjects, '"\'');
            
            // Try JSON decode
            $decoded = json_decode($excludedSubjects, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            // If not JSON, split by comma
            return array_map('trim', explode(',', $trimmed));
        }

        return [];
    }
}
