<?php

namespace App\Services;

use App\Models\Country;
use App\Models\ExamType;
use App\Models\Governorate;
use App\Models\Result;
use App\Models\SitemapSetting;
use App\Models\SitemapLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SitemapService
{
    protected ?SitemapSetting $settings;
    protected int $urlsPerSitemap;

    public function __construct()
    {
        $this->settings = SitemapSetting::getSettings();
        $this->urlsPerSitemap = $this->settings?->urls_per_sitemap ?? 5000;
    }

    /**
     * الحصول على إحصائيات شاملة لخرائط الموقع
     */
    public function getStatistics(): array
    {
        return Cache::remember('sitemap:statistics', 300, function () {
            $startTime = microtime(true);
            
            $stats = [
                'overview' => $this->getOverviewStats(),
                'sitemaps' => $this->getSitemapsBreakdown(),
                'content' => $this->getContentStats(),
                'performance' => $this->getPerformanceStats(),
            ];
            
            $stats['generation_time'] = round(microtime(true) - $startTime, 3);
            
            return $stats;
        });
    }

    /**
     * إحصائيات عامة
     */
    protected function getOverviewStats(): array
    {
        $totalUrls = 0;
        $totalSitemaps = 0;
        
        // الصفحات الثابتة
        $pagesCount = 6; // الرئيسية، الشهادة، اتصل بنا، الخصوصية، الشروط، خريطة الموقع
        $totalUrls += $pagesCount;
        $totalSitemaps += 1;
        
        // الدول
        $countriesCount = Country::where('is_active', true)->count();
        $totalUrls += $countriesCount;
        $totalSitemaps += 1;
        
        // أنواع الشهادات
        $examTypesCount = ExamType::whereHas('country', fn($q) => $q->where('is_active', true))->count();
        $totalUrls += $examTypesCount;
        $totalSitemaps += 1;
        
        // المحافظات
        $governoratesCount = Governorate::whereHas('country', fn($q) => $q->where('is_active', true))->count();
        $totalUrls += $governoratesCount;
        
        // نتائج الطلاب
        $studentsCount = Result::count();
        $totalUrls += $studentsCount;
        $studentSitemaps = ceil($studentsCount / $this->urlsPerSitemap);
        $totalSitemaps += $studentSitemaps;
        
        return [
            'total_urls' => $totalUrls,
            'total_sitemaps' => $totalSitemaps,
            'last_updated' => $this->settings?->last_generated_at,
            'cache_hours' => $this->settings?->cache_hours ?? 6,
            'urls_per_sitemap' => $this->urlsPerSitemap,
            'is_enabled' => $this->settings?->is_enabled ?? true,
        ];
    }

    /**
     * تفصيل كل خريطة
     */
    public function getSitemapsBreakdown(): array
    {
        $baseUrl = config('app.url');
        $sitemaps = [];
        
        // 1. الصفحات الثابتة
        $sitemaps[] = [
            'name' => 'sitemap-pages.xml',
            'type' => 'pages',
            'label' => 'الصفحات الثابتة',
            'urls_count' => 6,
            'url' => "{$baseUrl}/sitemap-pages.xml",
            'icon' => 'heroicon-o-document-text',
            'enabled' => $this->settings?->include_pages ?? true,
        ];
        
        // 2. الدول
        $countriesCount = Country::where('is_active', true)->count();
        $sitemaps[] = [
            'name' => 'sitemap-countries.xml',
            'type' => 'countries',
            'label' => 'الدول',
            'urls_count' => $countriesCount,
            'url' => "{$baseUrl}/sitemap-countries.xml",
            'icon' => 'heroicon-o-globe-alt',
            'enabled' => $this->settings?->include_countries ?? true,
        ];
        
        // 3. أنواع الشهادات
        $examTypesCount = ExamType::whereHas('country', fn($q) => $q->where('is_active', true))->count();
        $sitemaps[] = [
            'name' => 'sitemap-exam-types.xml',
            'type' => 'exam-types',
            'label' => 'أنواع الشهادات',
            'urls_count' => $examTypesCount,
            'url' => "{$baseUrl}/sitemap-exam-types.xml",
            'icon' => 'heroicon-o-academic-cap',
            'enabled' => $this->settings?->include_exam_types ?? true,
        ];
        
        // 4. المحافظات
        $governoratesData = $this->getGovernoratesBreakdown();
        foreach ($governoratesData as $gov) {
            $sitemaps[] = $gov;
        }
        
        // 5. الشعب والفروع
        $branchesCount = \App\Models\ExamBranch::count();
        $sitemaps[] = [
            'name' => 'sitemap-branches.xml',
            'type' => 'branches',
            'label' => 'الشعب والفروع',
            'urls_count' => $branchesCount,
            'url' => "{$baseUrl}/sitemap-branches.xml",
            'icon' => 'heroicon-o-rectangle-stack',
            'enabled' => $this->settings?->include_branches ?? true,
        ];
        
        // 6. نتائج الطلاب
        $studentsData = $this->getStudentsSitemapsBreakdown();
        foreach ($studentsData as $student) {
            $sitemaps[] = $student;
        }
        
        // 7. الأوائل (حالياً غير متاح - لا يوجد عمود is_top)
        // $topStudentsCount = Result::where('is_top', true)->count();
        $topStudentsCount = 0; // سيتم تفعيله لاحقاً
        $sitemaps[] = [
            'name' => 'sitemap-top-students.xml',
            'type' => 'top-students',
            'label' => 'الأوائل',
            'urls_count' => $topStudentsCount,
            'url' => "{$baseUrl}/sitemap-top-students.xml",
            'icon' => 'heroicon-o-trophy',
            'enabled' => $this->settings?->include_top_students ?? true,
        ];
        
        return $sitemaps;
    }

    /**
     * تفصيل خرائط المحافظات
     */
    protected function getGovernoratesBreakdown(): array
    {
        $baseUrl = config('app.url');
        $sitemaps = [];
        
        $countries = Country::where('is_active', true)
            ->whereHas('governorates')
            ->with(['governorates'])
            ->get();
        
        foreach ($countries as $country) {
            $govCount = $country->governorates->count();
            if ($govCount > 0) {
                $sitemaps[] = [
                    'name' => "sitemap-governorates-{$country->slug}.xml",
                    'type' => 'governorates',
                    'label' => "محافظات {$country->name}",
                    'urls_count' => $govCount,
                    'url' => "{$baseUrl}/sitemap-governorates-{$country->slug}.xml",
                    'icon' => 'heroicon-o-map',
                    'enabled' => $this->settings?->include_governorates ?? true,
                ];
            }
        }
        
        return $sitemaps;
    }

    /**
     * تفصيل خرائط نتائج الطلاب
     */
    protected function getStudentsSitemapsBreakdown(): array
    {
        $baseUrl = config('app.url');
        $sitemaps = [];
        
        $examTypes = ExamType::whereHas('country', fn($q) => $q->where('is_active', true))
            ->with('country')
            ->get();
        
        foreach ($examTypes as $examType) {
            $count = Result::where('exam_type_id', $examType->id)->count();
            
            if ($count > 0) {
                $pages = ceil($count / $this->urlsPerSitemap);
                
                for ($i = 1; $i <= $pages; $i++) {
                    $sitemaps[] = [
                        'name' => "sitemap-students-{$examType->country->slug}-{$examType->slug}-{$i}.xml",
                        'type' => 'students',
                        'label' => "{$examType->name} - صفحة {$i}",
                        'urls_count' => $i == $pages ? ($count % $this->urlsPerSitemap ?: $this->urlsPerSitemap) : $this->urlsPerSitemap,
                        'url' => "{$baseUrl}/sitemap-students-{$examType->country->slug}-{$examType->slug}-{$i}.xml",
                        'icon' => 'heroicon-o-users',
                        'enabled' => $this->settings?->include_students ?? true,
                        'country' => $examType->country->name,
                        'exam_type' => $examType->name,
                    ];
                }
            }
        }
        
        return $sitemaps;
    }

    /**
     * إحصائيات المحتوى
     */
    protected function getContentStats(): array
    {
        return [
            'countries' => [
                'total' => Country::count(),
                'active' => Country::where('is_active', true)->count(),
            ],
            'exam_types' => [
                'total' => ExamType::count(),
                'active' => ExamType::whereHas('country', fn($q) => $q->where('is_active', true))->count(),
            ],
            'governorates' => [
                'total' => Governorate::count(),
                'active' => Governorate::count(), // المحافظات ليس لها عمود is_active
            ],
            'results' => [
                'total' => Result::count(),
                'by_country' => $this->getResultsByCountry(),
            ],
        ];
    }

    /**
     * النتائج حسب الدولة
     */
    protected function getResultsByCountry(): array
    {
        return Result::select('countries.name_ar as country_name', DB::raw('count(*) as count'))
            ->join('exam_types', 'results.exam_type_id', '=', 'exam_types.id')
            ->join('countries', 'exam_types.country_id', '=', 'countries.id')
            ->groupBy('countries.id', 'countries.name_ar')
            ->orderByDesc('count')
            ->get()
            ->pluck('count', 'country_name')
            ->toArray();
    }

    /**
     * إحصائيات الأداء
     */
    protected function getPerformanceStats(): array
    {
        $recentLogs = SitemapLog::orderByDesc('generated_at')
            ->limit(10)
            ->get();
        
        $avgGenerationTime = SitemapLog::where('status', 'success')
            ->whereNotNull('generation_time')
            ->avg('generation_time');
        
        $totalGenerated = SitemapLog::where('status', 'success')->count();
        $totalFailed = SitemapLog::where('status', 'failed')->count();
        
        return [
            'avg_generation_time' => round($avgGenerationTime ?? 0, 2),
            'total_generated' => $totalGenerated,
            'total_failed' => $totalFailed,
            'success_rate' => $totalGenerated > 0 
                ? round(($totalGenerated / ($totalGenerated + $totalFailed)) * 100, 1) 
                : 100,
            'recent_logs' => $recentLogs,
        ];
    }

    /**
     * إعادة توليد جميع الخرائط
     */
    public function regenerateAll(): array
    {
        $startTime = microtime(true);
        $results = [];
        
        try {
            // مسح الكاش
            SitemapSetting::clearSitemapCache();
            
            // توليد الفهرس الرئيسي (سيولد باقي الخرائط)
            $response = app(\App\Http\Controllers\SitemapController::class)->index();
            
            // تحديث الإحصائيات
            $stats = $this->getStatistics();
            
            if ($this->settings) {
                $this->settings->update([
                    'total_urls' => $stats['overview']['total_urls'],
                    'total_sitemaps' => $stats['overview']['total_sitemaps'],
                    'last_generated_at' => now(),
                    'sitemaps_stats' => $stats['sitemaps'],
                ]);
            }
            
            // تسجيل النجاح
            SitemapLog::create([
                'sitemap_name' => 'sitemap.xml',
                'sitemap_type' => 'index',
                'urls_count' => $stats['overview']['total_urls'],
                'generation_time' => round(microtime(true) - $startTime, 2),
                'status' => 'success',
                'generated_at' => now(),
            ]);
            
            $results['success'] = true;
            $results['message'] = 'تم إعادة توليد جميع خرائط الموقع بنجاح';
            $results['stats'] = $stats['overview'];
            $results['time'] = round(microtime(true) - $startTime, 2) . ' ثانية';
            
        } catch (\Exception $e) {
            Log::error('Sitemap regeneration failed: ' . $e->getMessage());
            
            SitemapLog::create([
                'sitemap_name' => 'sitemap.xml',
                'sitemap_type' => 'index',
                'urls_count' => 0,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'generated_at' => now(),
            ]);
            
            $results['success'] = false;
            $results['message'] = 'فشل في توليد الخرائط: ' . $e->getMessage();
        }
        
        // مسح كاش الإحصائيات
        Cache::forget('sitemap:statistics');
        
        return $results;
    }

    /**
     * التحقق من صحة الخرائط
     */
    public function validateSitemaps(): array
    {
        $baseUrl = config('app.url');
        $results = [];
        
        $sitemapsToCheck = [
            'sitemap.xml',
            'sitemap-pages.xml',
            'sitemap-countries.xml',
            'sitemap-exam-types.xml',
        ];
        
        foreach ($sitemapsToCheck as $sitemap) {
            try {
                $url = "{$baseUrl}/{$sitemap}";
                $headers = @get_headers($url);
                $status = $headers && strpos($headers[0], '200') !== false;
                
                $results[] = [
                    'sitemap' => $sitemap,
                    'url' => $url,
                    'status' => $status ? 'valid' : 'error',
                    'message' => $status ? 'الخريطة متاحة' : 'الخريطة غير متاحة',
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'sitemap' => $sitemap,
                    'url' => "{$baseUrl}/{$sitemap}",
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ];
            }
        }
        
        return $results;
    }
}
