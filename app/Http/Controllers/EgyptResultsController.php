<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\ExamType;
use App\Models\Governorate;
use App\Services\SeoService;
use App\Services\SchemaService;
use Illuminate\Http\Request;

class EgyptResultsController extends Controller
{
    public function __construct(
        private SeoService $seoService
    ) {}

    /**
     * Show Egypt exam types
     */
    public function index()
    {
        $egypt = Country::where('code', 'EG')->firstOrFail();
        $examTypes = $egypt->examTypes;

        $meta = $this->seoService->generateMetaTags(
            $egypt->getDynamicTitle(),
            'نتائج الشهادة الإعدادية والثانوية العامة والدبلومات الفنية في مصر'
        );

        $title = $egypt->getDynamicTitle();
        return view('egypt.index', compact('title', 'meta', 'examTypes', 'egypt'));
    }

    /**
     * Show preparatory exam governorates grid
     */
    public function preparatory()
    {
        $egypt = Country::where('code', 'EG')->firstOrFail();
        $examType = ExamType::where('code', 'eg_preparatory')->firstOrFail();
        
        // Get governorates and sort by population (major cities first)
        $governorates = $egypt->governorates->sortBy(function($gov) {
            // Population-based sorting (approximate)
            $order = [
                'Cairo' => 1, 'الق' => 1,
                'Giza' => 2, 'الجيزة' => 2,
                'Alexandria' => 3, 'الإسكندرية' => 3,
                'Qalyubia' => 4, 'القليوبية' => 4,
                'Sharqia' => 5, 'الشرقية' => 5,
                'Dakahlia' => 6, 'الدقهلية' => 6,
                'Beheira' => 7, 'البحيرة' => 7,
                'Minya' => 8, 'المنيا' => 8,
                'Gharbia' => 9, 'الغربية' => 9,
                'Sohag' => 10, 'سوهاج' => 10,
            ];
            
            // Check both English and Arabic names
            foreach ($order as $name => $priority) {
                if (stripos($gov->name_ar, $name) !== false || stripos($gov->name_en, $name) !== false) {
                    return $priority;
                }
            }
            
            return 999; // Other governorates at the end
        })->values();

        // استخدام البيانات من لوحة التحكم
        $certName = $examType->name_ar ?? 'الشهادة الإعدادية';
        $title = $examType->content_title ?? $examType->seo_title ?? ('نتيجة الشهادة الإعدادية في ' . $egypt->getDynamicTitle(false, true));
        
        $meta = $this->seoService->generateMetaTags(
            $examType->seo_title ?? $title,
            $examType->seo_description ?? ('نتيجة الشهادة الإعدادية لجميع محافظات مصر ' . $egypt->academic_year)
        );

        $structuredData = SchemaService::examTypePage($examType, $governorates->all());

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج مصر', 'url' => route('egypt.index')],
            ['name' => $certName, 'url' => route('egypt.preparatory')],
        ];

        $showYearFilter = \App\Models\Setting::where('key', 'show_academic_year_filter')->value('value');
        $academicYears = $showYearFilter ? \App\Models\AcademicYear::where('is_active', true)->orderBy('year', 'desc')->get() : collect([]);

        return view('egypt.preparatory', compact('title', 'meta', 'governorates', 'breadcrumbs', 'egypt', 'examType', 'structuredData', 'showYearFilter', 'academicYears'));
    }

    /**
     * Show governorate results search page
     */
    public function governorateResults(Governorate $governorate, \Illuminate\Http\Request $request)
    {
        $egypt = Country::where('code', 'EG')->firstOrFail();
        
        // Build suffix with certificate name
        $certName = 'الشهادة الإعدادية';
        $suffix = $egypt->academic_year;
        if ($egypt->semester) {
            $suffix .= ' - ' . $egypt->semester;
        }

        // Use governorate SEO fields if available, otherwise use defaults
        $defaultTitle = "نتيجة {$certName} - محافظة {$governorate->name_ar} - {$suffix}";
        $defaultDesc = "نتيجة {$certName} محافظة {$governorate->name_ar} - ابحث برقم الجلوس أو الاسم - {$suffix}";
        
        $title = $governorate->seo_title ?: $defaultTitle;
        $metaDescription = $governorate->seo_description ?: $defaultDesc;
        $metaKeywords = $governorate->seo_keywords ?: "نتيجة الشهادة الإعدادية, محافظة {$governorate->name_ar}, نتيجتي, رقم الجلوس";

        $meta = $this->seoService->generateMetaTags($title, $metaDescription);
        $meta['keywords'] = $metaKeywords;
        
        // Ensure examType is passed for the timer
        $examType = ExamType::where('code', 'eg_preparatory')->firstOrFail();

        $structuredData = SchemaService::governoratePage($examType, $governorate);

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج مصر', 'url' => route('egypt.index')],
            ['name' => $certName, 'url' => route('egypt.preparatory')],
            ['name' => $governorate->name_ar, 'url' => route('egypt.governorate.results', $governorate)],
        ];

        $showYearFilter = \App\Models\Setting::where('key', 'show_academic_year_filter')->value('value');
        $academicYears = $showYearFilter ? \App\Models\AcademicYear::where('is_active', true)->orderBy('year', 'desc')->get() : collect([]);
        
        // Get selected academic year from request or default to active
        $selectedAcademicYearId = $request->get('academic_year_id');
        if (!$selectedAcademicYearId && $academicYears->isNotEmpty()) {
            $activeYear = $academicYears->first();
            $selectedAcademicYearId = $activeYear ? $activeYear->id : $academicYears->first()->id;
        }

        return view('egypt.search', compact('title', 'meta', 'governorate', 'breadcrumbs', 'egypt', 'examType', 'structuredData', 'showYearFilter', 'academicYears', 'suffix', 'certName', 'selectedAcademicYearId'));
    }

    /**
     * Show all results for a governorate in table format
     */
    public function governorateAllResults(Governorate $governorate, \Illuminate\Http\Request $request)
    {
        $egypt = Country::where('code', 'EG')->firstOrFail();
        $examType = ExamType::where('code', 'eg_preparatory')->firstOrFail();
        
        // Get academic year from request or use active one
        $academicYearId = $request->get('academic_year_id');
        if ($academicYearId) {
            $academicYear = \App\Models\AcademicYear::find($academicYearId);
        }
        if (!isset($academicYear) || !$academicYear) {
            $academicYear = \App\Models\AcademicYear::where('is_active', true)->first();
        }
        
        // Build suffix with certificate name
        $certName = 'الشهادة الإعدادية';
        $suffix = $egypt->academic_year;
        if ($egypt->semester) {
            $suffix .= ' - ' . $egypt->semester;
        }

        // Get search term to customize title for SEO
        $search = $request->get('search');
        
        // Build dynamic title based on search
        if ($search) {
            // Detect if search is for school or administration
            $searchLower = mb_strtolower($search);
            if (str_contains($searchLower, 'مدرسة') || str_contains($searchLower, 'مدرسه') || str_contains($searchLower, 'اعدادي') || str_contains($searchLower, 'إعدادي')) {
                $title = "نتائج مدرسة {$search} - {$certName} - محافظة {$governorate->name_ar}";
                $metaDesc = "نتائج طلاب مدرسة {$search} في {$certName} محافظة {$governorate->name_ar} - {$suffix}";
            } elseif (str_contains($searchLower, 'إدارة') || str_contains($searchLower, 'ادارة') || str_contains($searchLower, 'التعليمية')) {
                $title = "نتائج إدارة {$search} التعليمية - {$certName} - محافظة {$governorate->name_ar}";
                $metaDesc = "نتائج طلاب إدارة {$search} التعليمية في {$certName} محافظة {$governorate->name_ar} - {$suffix}";
            } else {
                $title = "نتائج {$search} - {$certName} - محافظة {$governorate->name_ar}";
                $metaDesc = "نتائج البحث عن {$search} في {$certName} محافظة {$governorate->name_ar} - {$suffix}";
            }
            $pageTitle = $search; // For display in page
        } else {
            $title = "نتائج {$certName} - محافظة {$governorate->name_ar} - {$suffix}";
            $metaDesc = "عرض جميع نتائج طلاب {$certName} محافظة {$governorate->name_ar} مرتبة حسب المجموع - {$suffix}";
            $pageTitle = null;
        }

        $meta = $this->seoService->generateMetaTags(
            $title,
            $metaDesc
        );

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج مصر', 'url' => route('egypt.index')],
            ['name' => $certName, 'url' => route('egypt.preparatory')],
            ['name' => $governorate->name_ar, 'url' => route('egypt.governorate.results', $governorate)],
            ['name' => 'جميع النتائج'],
        ];

        // Build base query conditions
        $baseConditions = [
            ['governorate_id', '=', $governorate->id],
            ['exam_type_id', '=', $examType->id],
        ];
        
        if ($academicYear) {
            $baseConditions[] = ['academic_year_id', '=', $academicYear->id];
        }

        // Build query
        $query = \App\Models\Result::where($baseConditions);

        // Search filter
        $search = $request->get('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('seat_number', 'like', "%{$search}%")
                  ->orWhere('student_name', 'like', "%{$search}%")
                  ->orWhere('subjects_data', 'like', "%{$search}%");
            });
        }

        // Get statistics based on search filter (not cached when searching)
        // Calculate passed count using passing_score from semester settings
        // First, determine semester from results (most results have same semester)
        $sampleResult = \App\Models\Result::where($baseConditions)->first();
        $semester = $sampleResult ? ($sampleResult->semester ?? 1) : 1;
        $semesterSettings = $examType->getSettingsForSemester($semester);
        $passingScore = $semesterSettings['passing_score'] ?? ($examType->passing_score ?? 70);
        
        if ($search) {
            // Calculate stats for filtered results
            $statsQuery = \App\Models\Result::where($baseConditions)
                ->where(function($q) use ($search) {
                    $q->where('seat_number', 'like', "%{$search}%")
                      ->orWhere('student_name', 'like', "%{$search}%")
                      ->orWhere('subjects_data', 'like', "%{$search}%");
                });
            
            $total = (clone $statsQuery)->count();
            $highest = (clone $statsQuery)->max('total_score');
            // Calculate passed using semester-specific passing_score
            $passed = (clone $statsQuery)->where('total_score', '>=', $passingScore)->count();
            
            $stats = [
                'total' => $total,
                'passed' => $passed,
                'failed' => $total - $passed,
                'highest' => $highest,
            ];
        } else {
            // Use caching for governorate-wide stats (with semester awareness)
            $cacheKey = "gov_stats_{$governorate->id}_{$examType->id}_{$semester}" . ($academicYear ? "_{$academicYear->id}" : "");
            $stats = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($baseConditions, $passingScore) {
                $baseQuery = \App\Models\Result::where($baseConditions);
                
                $total = (clone $baseQuery)->count();
                $highest = (clone $baseQuery)->max('total_score');
                // Calculate passed using semester-specific passing_score
                $passed = (clone $baseQuery)->where('total_score', '>=', $passingScore)->count();
                
                return [
                    'total' => $total,
                    'passed' => $passed,
                    'failed' => $total - $passed,
                    'highest' => $highest,
                ];
            });
        }

        // Check if print mode (get all results without pagination)
        $printMode = $request->get('print') === 'all';
        
        // Get results ordered by score
        if ($printMode) {
            // For print mode, get all results (max 500 to prevent memory issues)
            $results = $query->orderBy('total_score', 'desc')
                ->orderBy('student_name', 'asc')
                ->limit(500)
                ->get();
            
            // Convert to LengthAwarePaginator-like object for compatibility
            $results = new \Illuminate\Pagination\LengthAwarePaginator(
                $results,
                $results->count(),
                $results->count() ?: 1,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // Normal pagination
            $results = $query->orderBy('total_score', 'desc')
                ->orderBy('student_name', 'asc')
                ->paginate(50);
        }
        
        // Calculate ranks efficiently using a single query for all unique scores on this page
        $uniqueScores = $results->pluck('total_score')->unique()->filter()->values()->toArray();
        
        if (!empty($uniqueScores)) {
            // Get count of students with higher scores for each unique score in one query
            $rankData = [];
            $minScore = min($uniqueScores);
            
            // Single query to get count of students with score > minScore
            $higherThanMin = \App\Models\Result::where($baseConditions)
                ->where('total_score', '>', $minScore)
                ->count();
            
            // Calculate ranks based on position in sorted results
            $currentPage = $results->currentPage();
            $perPage = $results->perPage();
            $startRank = ($currentPage - 1) * $perPage + 1;
            
            // For first page, we can calculate exact ranks
            // For other pages, estimate based on page position
            $prevScore = null;
            $prevRank = $startRank;
            $sameScoreCount = 0;
            
            foreach ($results as $index => $result) {
                if ($result->total_score !== null) {
                    if ($prevScore === null || $result->total_score != $prevScore) {
                        // New score - calculate actual rank if on first page, else estimate
                        if ($currentPage == 1) {
                            $result->rank = $index + 1;
                        } else {
                            // For other pages, use position-based rank
                            $result->rank = $startRank + $index;
                        }
                        $prevScore = $result->total_score;
                        $prevRank = $result->rank;
                        $sameScoreCount = 1;
                    } else {
                        // Same score as previous - same rank
                        $result->rank = $prevRank;
                        $sameScoreCount++;
                    }
                } else {
                    $result->rank = '-';
                }
            }
        } else {
            foreach ($results as $result) {
                $result->rank = '-';
            }
        }

        // Get unique subjects from first few results (not all)
        // Exclude metadata fields - only keep actual subject names
        $metadataFields = ['الإدارة', 'الادارة', 'الاداره', 'الإداره', 'المدرسة', 'المدرسه', 'الاسم', 'رقم الجلوس', 'المجموع', 'المجموع الكلي', 'الحالة', 'الترتيب', 'اسم الإدارة', 'اسم الادارة', 'اسم الاداره', 'اسم المدرسة', 'اسم المدرسه'];
        $subjects = [];
        $sampleResults = \App\Models\Result::where($baseConditions)
            ->whereNotNull('subjects_data')
            ->limit(5)
            ->get();
            
        foreach ($sampleResults as $result) {
            if ($result->subjects_data) {
                foreach (array_keys($result->subjects_data) as $subject) {
                    // Skip metadata fields
                    if (!in_array($subject, $subjects) && !in_array($subject, $metadataFields)) {
                        $subjects[] = $subject;
                    }
                }
            }
        }
        
        // Check if we have administration and school columns
        $hasAdministration = false;
        $hasSchool = false;
        foreach ($sampleResults as $result) {
            if ($result->subjects_data) {
                if (isset($result->subjects_data['الادارة']) || isset($result->subjects_data['الاداره']) || isset($result->subjects_data['الإدارة']) || isset($result->subjects_data['الإداره'])) {
                    $hasAdministration = true;
                }
                if (isset($result->subjects_data['المدرسة']) || isset($result->subjects_data['المدرسه'])) {
                    $hasSchool = true;
                }
            }
        }

        return view('egypt.governorate-all-results', compact(
            'title', 'meta', 'governorate', 'breadcrumbs', 'results', 
            'subjects', 'stats', 'examType', 'egypt', 'hasAdministration', 'hasSchool',
            'suffix', 'certName', 'academicYear', 'pageTitle'
        ));
    }

    /**
     * Show top 10 results for governorate, administration or school
     */
    public function governorateTopResults(Governorate $governorate, \Illuminate\Http\Request $request)
    {
        $egypt = Country::where('code', 'EG')->firstOrFail();
        $examType = ExamType::where('code', 'eg_preparatory')->firstOrFail();
        
        // Get academic year from request or use active one
        $academicYearId = $request->get('academic_year_id');
        if ($academicYearId) {
            $academicYear = \App\Models\AcademicYear::find($academicYearId);
        }
        if (!isset($academicYear) || !$academicYear) {
            $academicYear = \App\Models\AcademicYear::where('is_active', true)->first();
        }
        
        // Get type and name from request
        $type = $request->get('type', 'governorate'); // governorate, admin, school
        $name = $request->get('name', '');
        
        // Build full suffix with certificate name, semester and year
        $certName = 'الشهادة الإعدادية';
        $suffix = $egypt->academic_year;
        if ($egypt->semester) {
            $suffix .= ' - ' . $egypt->semester;
        }
        
        // Determine title and filter based on type
        $filterLabel = '';
        $pageSubtitle = '';
        $adminName = $request->get('admin', '');
        switch ($type) {
            case 'school':
                $adminPart = $adminName ? " - إدارة {$adminName}" : '';
                $title = "العشرة الأوائل في {$certName} - مدرسة {$name}{$adminPart} - محافظة {$governorate->name_ar} - {$suffix}";
                $filterLabel = "مدرسة {$name}";
                $pageSubtitle = "مدرسة {$name}" . ($adminName ? " - إدارة {$adminName}" : '') . " - محافظة {$governorate->name_ar}";
                break;
            case 'admin':
                $title = "العشرة الأوائل في {$certName} - إدارة {$name} - محافظة {$governorate->name_ar} - {$suffix}";
                $filterLabel = "إدارة {$name}";
                $pageSubtitle = "إدارة {$name} - محافظة {$governorate->name_ar}";
                break;
            default:
                $title = "العشرة الأوائل في {$certName} - محافظة {$governorate->name_ar} - {$suffix}";
                $filterLabel = "محافظة {$governorate->name_ar}";
                $pageSubtitle = "محافظة {$governorate->name_ar}";
        }

        $meta = $this->seoService->generateMetaTags(
            $title,
            "العشرة الأوائل في {$certName} - {$filterLabel} - {$suffix}"
        );

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج مصر', 'url' => route('egypt.index')],
            ['name' => $certName, 'url' => route('egypt.preparatory')],
            ['name' => $governorate->name_ar, 'url' => route('egypt.governorate.results', $governorate)],
            ['name' => 'العشرة الأوائل'],
        ];

        // Build cache key based on filters
        $cacheKey = "top10_{$governorate->id}_{$examType->id}_{$type}_" . md5($name) . ($academicYear ? "_{$academicYear->id}" : "");
        
        // Get cached results or query
        $cachedData = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($governorate, $examType, $academicYear, $type, $name) {
            // Build base query conditions
            $baseConditions = [
                ['governorate_id', '=', $governorate->id],
                ['exam_type_id', '=', $examType->id],
            ];
            
            if ($academicYear) {
                $baseConditions[] = ['academic_year_id', '=', $academicYear->id];
            }
            
            // Build filter closure
            $filterClosure = null;
            if ($type === 'school' && $name) {
                $filterClosure = function($q) use ($name) {
                    $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"المدرسة\"')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"المدرسه\"')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم المدرسة\"')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم المدرسه\"')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.SCHOOL')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.School')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.school')) = ?", [$name]);
                };
            } elseif ($type === 'admin' && $name) {
                $filterClosure = function($q) use ($name) {
                    $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"الادارة\"')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"الاداره\"')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"الإدارة\"')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"الإداره\"')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم الإدارة\"')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم الاداره\"')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"اسم الادارة\"')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.EDARA')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.Edara')) = ?", [$name])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.edara')) = ?", [$name]);
                };
            }
            
            // Single query for top 10 + count using subquery
            $query = \App\Models\Result::where($baseConditions);
            if ($filterClosure) {
                $query->where($filterClosure);
            }
            
            // Get total count
            $totalStudents = (clone $query)->count();
            
            // Get top 50 results to satisfy "increase students" request
            $results = $query->orderBy('total_score', 'desc')
                ->orderBy('student_name', 'asc')
                ->limit(50)
                ->get();
            
            return [
                'results' => $results,
                'totalStudents' => $totalStudents,
            ];
        });
        
        $results = $cachedData['results'];
        $totalStudents = $cachedData['totalStudents'];
        
        // Add ranks using Dense Ranking (1, 1, 2, 2, 3...)
        $rank = 0;
        $prevScore = null;
        $displayRank = 0;
        foreach ($results as $result) {
            if ($result->total_score !== null) {
                if ($prevScore === null || $result->total_score != $prevScore) {
                    $displayRank++;
                    $prevScore = $result->total_score;
                }
                $result->rank = $displayRank;
            } else {
                $result->rank = '-';
            }
        }
        
        $highestScore = $results->first()?->total_score ?? 0;
        
        // Get subjects list
        $metadataFields = ['الإدارة', 'الادارة', 'الاداره', 'الإداره', 'المدرسة', 'المدرسه', 'الاسم', 'رقم الجلوس', 'المجموع', 'المجموع الكلي', 'الحالة', 'الترتيب', 'اسم الإدارة', 'اسم الادارة', 'اسم الاداره', 'اسم المدرسة', 'اسم المدرسه'];
        $subjects = [];
        foreach ($results as $result) {
            if ($result->subjects_data) {
                foreach (array_keys($result->subjects_data) as $subject) {
                    if (!in_array($subject, $subjects) && !in_array($subject, $metadataFields)) {
                        $subjects[] = $subject;
                    }
                }
            }
        }

        return view('egypt.governorate-top-results', compact(
            'title', 'meta', 'governorate', 'breadcrumbs', 'results', 
            'subjects', 'examType', 'egypt', 'type', 'name', 'filterLabel',
            'totalStudents', 'highestScore', 'pageSubtitle', 'suffix', 'academicYear'
        ));
    }

    /**
     * Show specific student result page
     * New URL format: /egypt/preparatory/{governorate}/{academic_year}/{term}/{seat_number}
     */
    public function governorateResultDetail(Governorate $governorate, $academic_year, $term, $seat_number)
    {
        $egypt = Country::where('code', 'EG')->firstOrFail();
        $examType = ExamType::where('code', 'eg_preparatory')->firstOrFail();
        
        // Get academic year from URL slug (e.g., "2023-2024")
        $academicYear = null;
        if ($academic_year && preg_match('/^\d{4}-\d{4}$/', $academic_year)) {
            // Search by exact year string since DB stores it as string like "2023-2024"
            $academicYear = \App\Models\AcademicYear::where('year', $academic_year)->first();
        }
        
        // Fallback to active academic year if not found
        if (!$academicYear) {
            $academicYear = \App\Models\AcademicYear::where('is_active', true)->first();
        }
        
        // Determine semester from term parameter
        $semesterFilter = null;
        if ($term === 'term1') {
            $semesterFilter = 1;
        } elseif ($term === 'term2') {
            $semesterFilter = 2;
        }
        // If 'all', don't filter by semester
        
        // Try to get student data for title
        $studentQuery = \App\Models\Result::where('governorate_id', $governorate->id)
            ->where('exam_type_id', $examType->id)
            ->where('seat_number', $seat_number);
        
        if ($academicYear) {
            $studentQuery->where('academic_year_id', $academicYear->id);
        }
        
        if ($semesterFilter) {
            $studentQuery->where('semester', $semesterFilter);
        }
        
        $studentResult = $studentQuery->first();
        
        // Build suffix with certificate name
        $certName = 'الشهادة الإعدادية';
        $termLabel = $term === 'term1' ? 'الترم الأول' : ($term === 'term2' ? 'الترم الثاني' : 'العام الدراسي كاملاً');
        $suffix = $academic_year . ' - ' . $termLabel;

        // Build title with student name if available
        if ($studentResult && $studentResult->student_name) {
            $studentName = $studentResult->student_name;
            $title = "نتيجة {$studentName}";
            
            // Add school if available
            $school = $studentResult->subjects_data['المدرسة'] ?? $studentResult->subjects_data['المدرسه'] ?? null;
            if ($school) {
                $title .= " - {$school}";
            }
            
            $title .= " - {$certName} - محافظة {$governorate->name_ar}";
            
            $metaDesc = "نتيجة {$studentName} رقم جلوس {$seat_number} في {$certName} محافظة {$governorate->name_ar}";
            if ($studentResult->total_score) {
                $metaDesc .= " - المجموع: {$studentResult->total_score}";
            }
            $metaDesc .= " - {$suffix}";
        } else {
            $title = "نتيجة رقم جلوس {$seat_number} - {$certName} - محافظة {$governorate->name_ar} - {$suffix}";
            $metaDesc = "نتيجة رقم جلوس {$seat_number} في {$certName} محافظة {$governorate->name_ar} - تفاصيل الدرجات والمجموع الكلي - {$suffix}";
        }

        $meta = $this->seoService->generateMetaTags($title, $metaDesc);

        $structuredData = SchemaService::governoratePage($examType, $governorate);

        // Update breadcrumb with student name
        $breadcrumbName = $studentResult && $studentResult->student_name 
            ? "نتيجة {$studentResult->student_name}" 
            : "نتيجة {$seat_number}";
            
        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج مصر', 'url' => route('egypt.index')],
            ['name' => $certName, 'url' => route('egypt.preparatory')],
            ['name' => $governorate->name_ar, 'url' => route('egypt.governorate.results', $governorate)],
            ['name' => $breadcrumbName, 'url' => url()->current()],
        ];

        $showYearFilter = \App\Models\Setting::where('key', 'show_academic_year_filter')->value('value');
        $academicYears = $showYearFilter ? \App\Models\AcademicYear::where('is_active', true)->orderBy('year', 'desc')->get() : collect([]);
        
        $selectedAcademicYearId = $academicYear ? $academicYear->id : null;

        // Pass URL components for building links in view
        $urlAcademicYear = $academic_year;
        $urlTerm = $term;

        // Pass the seat_number to the view so Alpine can auto-search
        return view('egypt.search', compact(
            'title', 'meta', 'governorate', 'breadcrumbs', 'egypt', 'examType', 
            'structuredData', 'seat_number', 'showYearFilter', 'academicYears', 
            'suffix', 'certName', 'selectedAcademicYearId', 'urlAcademicYear', 'urlTerm'
        ));
    }

    /**
     * Show secondary exam search page
     */
    public function secondary()
    {
        $egypt = Country::where('code', 'EG')->firstOrFail();
        $examType = ExamType::where('code', 'eg_secondary')->with('branches')->firstOrFail();

        // استخدام العنوان من لوحة التحكم أولاً
        $title = $examType->content_title ?? $examType->seo_title ?? ('نتيجة الثانوية العامة في ' . $egypt->getDynamicTitle(false, false));
        $certName = $examType->name_ar ?? 'الثانوية العامة';

        $meta = $this->seoService->generateMetaTags(
            $examType->seo_title ?? $title,
            $examType->seo_description ?? ('نتيجة الثانوية العامة في مصر - ابحث برقم الجلوس أو الاسم ' . $egypt->academic_year)
        );
        
        // Get secondary branches from database (علمي علوم - علمي رياضة - أدبي)
        $branches = $examType->branches()->where('is_active', true)->orderBy('sort_order')->get();

        $structuredData = SchemaService::examTypePage($examType, []);

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج مصر', 'url' => route('egypt.index')],
            ['name' => $certName, 'url' => route('egypt.secondary')],
        ];

        $showYearFilter = \App\Models\Setting::where('key', 'show_academic_year_filter')->value('value');
        $academicYears = $showYearFilter ? \App\Models\AcademicYear::where('is_active', true)->orderBy('year', 'desc')->get() : collect([]);

        return view('egypt.search', compact('title', 'meta', 'examType', 'breadcrumbs', 'egypt', 'structuredData', 'showYearFilter', 'academicYears', 'branches', 'certName'));
    }

    /**
     * Show all secondary results
     */
    public function secondaryAllResults(Request $request)
    {
        $egypt = Country::where('code', 'EG')->firstOrFail();
        $examType = ExamType::where('code', 'eg_secondary')->with('branches')->firstOrFail();
        
        // Get academic year from request or default to one with results
        $academicYearId = $request->input('academic_year_id');
        if ($academicYearId) {
            $academicYear = \App\Models\AcademicYear::find($academicYearId);
        }
        if (!isset($academicYear) || !$academicYear) {
            // Find the academic year that has secondary results
            $yearWithResults = \App\Models\Result::where('exam_type_id', $examType->id)
                ->select('academic_year_id')
                ->distinct()
                ->first();
            
            if ($yearWithResults) {
                $academicYear = \App\Models\AcademicYear::find($yearWithResults->academic_year_id);
            } else {
                // Fallback to latest active year
                $academicYear = \App\Models\AcademicYear::where('is_active', true)->orderBy('year', 'desc')->first();
            }
        }
        
        // Get top_count to determine title
        $topCount = $request->input('top_count', 10);
        $isFullList = $topCount == 'all';

        // استخدام العنوان بناءً على نوع العرض
        if ($isFullList) {
            $title = 'كشف نتائج ' . ($examType->name_ar ?? 'الثانوية العامة') . ' في ' . $egypt->getDynamicTitle(false, false);
            $metaDesc = 'كشف درجات ' . ($examType->name_ar ?? 'الثانوية العامة') . ' كامل على مستوى الجمهورية - ' . $egypt->academic_year;
            $breadcrumbTitle = 'كشف النتائج';
        } else {
            $title = 'أوائل ' . ($examType->name_ar ?? 'الثانوية العامة') . ' في ' . $egypt->getDynamicTitle(false, false);
            $metaDesc = 'قائمة أوائل ' . ($examType->name_ar ?? 'الثانوية العامة') . ' على مستوى الجمهورية - ' . $egypt->academic_year;
            $breadcrumbTitle = 'أوائل الجمهورية';
        }

        $meta = $this->seoService->generateMetaTags($title, $metaDesc);

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج مصر', 'url' => route('egypt.index')],
            ['name' => 'الثانوية العامة', 'url' => route('egypt.secondary')],
            ['name' => $breadcrumbTitle],
        ];

        // Get filters
        $branchId = $request->input('branch_id');
        $systemType = $request->input('system_type');
        $search = $request->input('search');

        // Get top results
        $query = \App\Models\Result::where('exam_type_id', $examType->id)
            ->where('academic_year_id', $academicYear->id)
            ->with(['governorate', 'branch', 'examType'])
            ->orderByDesc('total_score');

        // Filter by branch if provided
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        // Filter by system type (old/new)
        if ($systemType) {
            $query->where('system_type', $systemType);
        }

        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('seat_number', $search);
            });
        }

        // Get total count before limiting
        $totalStudents = $query->count();
        
        // Get highest score
        $highestScore = (clone $query)->max('total_score') ?? 0;

        // Get only top results (not paginated if showing top X)
        if (!$search && $topCount != 'all') {
            $results = $query->take((int)$topCount)->get();
            $isPaginated = false;
        } else {
            $results = $query->paginate(50);
            $isPaginated = true;
        }
        
        $branches = $examType->branches()->where('is_active', true)->orderBy('sort_order')->get();
        
        // Get selected branch if filter is applied
        $selectedBranch = $branchId ? $branches->where('id', $branchId)->first() : null;
        
        // Get academic years for filter
        $showYearFilter = \App\Models\Setting::where('key', 'show_academic_year_filter')->value('value');
        $academicYears = $showYearFilter ? \App\Models\AcademicYear::where('is_active', true)->orderBy('year', 'desc')->get() : collect([]);

        return view('egypt.secondary-all-results', compact(
            'title', 'meta', 'breadcrumbs', 'egypt', 'examType', 'results', 'branches', 
            'branchId', 'systemType', 'search', 'topCount', 'isPaginated', 'academicYear',
            'totalStudents', 'highestScore',
            'showYearFilter', 'academicYears', 'selectedBranch'
        ));
    }

    /**
     * Show secondary branch page
     */
    public function secondaryBranch($branchCode)
    {
        $egypt = Country::where('code', 'EG')->firstOrFail();
        $examType = ExamType::where('code', 'eg_secondary')->firstOrFail();
        $branch = \App\Models\ExamBranch::where('exam_type_id', $examType->id)
            ->where('code', $branchCode)
            ->firstOrFail();

        $title = "نتيجة الثانوية العامة - {$branch->name_ar} في " . $egypt->getDynamicTitle(false, false);

        $meta = $this->seoService->generateMetaTags(
            $title,
            "نتيجة الثانوية العامة شعبة {$branch->name_ar} - ابحث برقم الجلوس " . $egypt->academic_year
        );

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج مصر', 'url' => route('egypt.index')],
            ['name' => 'الثانوية العامة', 'url' => route('egypt.secondary')],
            ['name' => $branch->name_ar],
        ];

        $branches = $examType->branches()->where('is_active', true)->orderBy('sort_order')->get();

        $showYearFilter = \App\Models\Setting::where('key', 'show_academic_year_filter')->value('value');
        $academicYears = $showYearFilter ? \App\Models\AcademicYear::where('is_active', true)->orderBy('year', 'desc')->get() : collect([]);

        return view('egypt.search', compact('title', 'meta', 'examType', 'breadcrumbs', 'egypt', 'showYearFilter', 'academicYears', 'branches', 'branch'));
    }

    /**
     * Show secondary branch all results
     */
    public function secondaryBranchAllResults(Request $request, $branchCode)
    {
        $egypt = Country::where('code', 'EG')->firstOrFail();
        $examType = ExamType::where('code', 'eg_secondary')->firstOrFail();
        $branch = \App\Models\ExamBranch::where('exam_type_id', $examType->id)
            ->where('code', $branchCode)
            ->firstOrFail();
        $academicYear = \App\Models\AcademicYear::where('is_active', true)->first();

        $title = "أوائل الثانوية العامة - {$branch->name_ar} في " . $egypt->getDynamicTitle(false, false);

        $meta = $this->seoService->generateMetaTags(
            $title,
            "قائمة أوائل الثانوية العامة شعبة {$branch->name_ar} - " . $egypt->academic_year
        );

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج مصر', 'url' => route('egypt.index')],
            ['name' => 'الثانوية العامة', 'url' => route('egypt.secondary')],
            ['name' => $branch->name_ar, 'url' => route('egypt.secondary.branch', $branch->code)],
            ['name' => 'الأوائل'],
        ];

        // Get top results for this branch
        $query = \App\Models\Result::where('exam_type_id', $examType->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('branch_id', $branch->id)
            ->with(['governorate'])
            ->orderByDesc('total_score');

        // Search filter
        $search = $request->input('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('seat_number', $search);
            });
        }

        $results = $query->paginate(50);

        return view('egypt.secondary-branch-results', compact('title', 'meta', 'breadcrumbs', 'egypt', 'examType', 'branch', 'results', 'search'));
    }

    /**
     * Show specific secondary student result page
     */
    public function secondaryStudentResult($seat_number)
    {
        $egypt = Country::where('code', 'EG')->firstOrFail();
        $examType = ExamType::where('code', 'eg_secondary')->with('branches')->firstOrFail();
        
        // Get student name for title
        $result = \App\Models\Result::where('exam_type_id', $examType->id)
            ->where('seat_number', $seat_number)
            ->first();
        
        $studentName = $result ? $result->student_name : $seat_number;

        $title = "نتيجة {$studentName} - الثانوية العامة " . $egypt->getDynamicTitle(false, false);

        $meta = $this->seoService->generateMetaTags(
            $title,
            "نتيجة {$studentName} رقم جلوس {$seat_number} في الثانوية العامة - تفاصيل الدرجات والترتيب"
        );

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج مصر', 'url' => route('egypt.index')],
            ['name' => 'الثانوية العامة', 'url' => route('egypt.secondary')],
            ['name' => "نتيجة {$studentName}"],
        ];

        $branches = $examType->branches()->where('is_active', true)->orderBy('sort_order')->get();

        $showYearFilter = \App\Models\Setting::where('key', 'show_academic_year_filter')->value('value');
        $academicYears = $showYearFilter ? \App\Models\AcademicYear::where('is_active', true)->orderBy('year', 'desc')->get() : collect([]);

        // Pass the seat_number to auto-search
        return view('egypt.search', compact('title', 'meta', 'examType', 'breadcrumbs', 'egypt', 'showYearFilter', 'academicYears', 'branches', 'seat_number'));
    }

    /**
     * Show unified diplomas search page
     */
    public function diplomasIndex()
    {
        $egypt = Country::where('code', 'EG')->firstOrFail();

        // Get the main diploma ExamType (eg_diploma)
        $diplomaExamType = ExamType::where('code', 'eg_diploma')->with('branches')->first();

        // Get diploma branches from database
        $branches = $diplomaExamType ? $diplomaExamType->branches()->where('is_active', true)->orderBy('sort_order')->get() : collect([]);

        // استخدام العنوان من لوحة التحكم أولاً
        $title = $diplomaExamType->content_title ?? $diplomaExamType->seo_title ?? ('نتيجة الدبلومات الفنية في ' . $egypt->getDynamicTitle(false, false));

        $meta = $this->seoService->generateMetaTags(
            $diplomaExamType->seo_title ?? $title,
            $diplomaExamType->seo_description ?? ('نتيجة الدبلومات الفنية (تجاري - صناعي - زراعي - فندقي) - ابحث برقم الجلوس ' . $egypt->academic_year)
        );

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج مصر', 'url' => route('egypt.index')],
            ['name' => 'الدبلومات الفنية', 'url' => route('egypt.diplomas.index')],
        ];

        $structuredData = $diplomaExamType 
            ? SchemaService::examTypePage($diplomaExamType, []) 
            : SchemaService::simpleExamTypePage('الدبلومات الفنية', 'مصر', []);

        $showYearFilter = \App\Models\Setting::where('key', 'show_academic_year_filter')->value('value');
        $academicYears = $showYearFilter ? \App\Models\AcademicYear::where('is_active', true)->orderBy('year', 'desc')->get() : collect([]);

        return view('egypt.diplomas', compact('title', 'meta', 'diplomaExamType', 'branches', 'breadcrumbs', 'egypt', 'structuredData', 'showYearFilter', 'academicYears'));
    }

    /**
     * Show diplomas search page (Legacy/Specific)
     */
    public function diplomas(string $type)
    {
        // Redirect legacy individual pages to the main diplomas page
        return redirect()->route('egypt.diplomas.index', [], 301);
    }

    /**
     * Show Azhar Secondary search page
     */
    public function azharSecondary()
    {
        return $this->azharExam('azhar-secondary', 'الثانوية الأزهرية');
    }

    /**
     * Show Azhar Preparatory search page
     */
    public function azharPreparatory()
    {
        return $this->azharExam('azhar-preparatory', 'الإعدادية الأزهرية');
    }

    /**
     * Show Azhar Primary search page
     */
    public function azharPrimary()
    {
        return $this->azharExam('azhar-primary', 'الابتدائية الأزهرية');
    }

    /**
     * Common method for Azhar exams
     */
    private function azharExam(string $slug, string $examName)
    {
        $egypt = Country::where('code', 'EG')->firstOrFail();
        $examType = ExamType::where('slug', $slug)->firstOrFail();

        // استخدام العنوان من لوحة التحكم أولاً
        $title = $examType->content_title ?? $examType->seo_title ?? ('نتيجة ' . $examName . ' ' . $egypt->academic_year);

        $meta = $this->seoService->generateMetaTags(
            $examType->seo_title ?? $title,
            $examType->seo_description ?? ('نتيجة ' . $examName . ' - ابحث عن نتيجتك برقم الجلوس أو الاسم ' . $egypt->academic_year)
        );

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج مصر', 'url' => route('egypt.index')],
            ['name' => $examName, 'url' => ''],
        ];

        $structuredData = SchemaService::examTypePage($examType, []);

        $showYearFilter = \App\Models\Setting::where('key', 'show_academic_year_filter')->value('value');
        $academicYears = $showYearFilter ? \App\Models\AcademicYear::where('is_active', true)->orderBy('year', 'desc')->get() : collect([]);

        return view('egypt.azhar', compact('title', 'meta', 'examType', 'breadcrumbs', 'egypt', 'structuredData', 'showYearFilter', 'academicYears', 'examName'));
    }
}
