<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\ExamType;
use App\Models\Governorate;
use App\Models\Result;
use App\Models\ExamBranch;
use App\Models\SitemapSetting;
use App\Models\SitemapLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    /**
     * الحصول على الإعدادات
     */
    protected function getSettings(): ?SitemapSetting
    {
        return SitemapSetting::getSettings();
    }

    /**
     * عدد الروابط الأقصى لكل خريطة
     */
    protected function getMaxUrlsPerSitemap(): int
    {
        return $this->getSettings()?->urls_per_sitemap ?? 5000;
    }

    /**
     * مدة التخزين المؤقت بالثواني
     */
    protected function getCacheTtl(): int
    {
        $hours = $this->getSettings()?->cache_hours ?? 6;
        return $hours * 3600;
    }

    // ========================================
    // 1. الفهرس الرئيسي - Sitemap Index
    // ========================================

    public function index()
    {
        $settings = $this->getSettings();
        
        // التحقق من تفعيل الخرائط
        if ($settings && !$settings->is_enabled) {
            return response('Sitemap is disabled', 503);
        }
        
        $xml = Cache::remember('sitemap:index', $this->getCacheTtl(), function () {
            return $this->generateSitemapIndex();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    protected function generateSitemapIndex(): string
    {
        $baseUrl = config('app.url');
        $lastmod = now()->toW3cString();
        $sitemaps = [];
        $settings = $this->getSettings();

        // ===== 1. الخرائط الأساسية =====
        if (!$settings || $settings->include_pages) {
            $sitemaps[] = ['loc' => "{$baseUrl}/sitemap-pages.xml", 'lastmod' => $lastmod];
            $sitemaps[] = ['loc' => "{$baseUrl}/sitemap-posts.xml", 'lastmod' => $lastmod];
        }
        if (!$settings || $settings->include_countries) {
            $sitemaps[] = ['loc' => "{$baseUrl}/sitemap-countries.xml", 'lastmod' => $lastmod];
        }
        if (!$settings || $settings->include_exam_types) {
            $sitemaps[] = ['loc' => "{$baseUrl}/sitemap-exam-types.xml", 'lastmod' => $lastmod];
        }

        // ===== 2. خرائط المحافظات (مصر فقط حالياً) =====
        if (!$settings || $settings->include_governorates) {
            $egypt = Country::where('slug', 'egypt')->where('is_active', true)->first();
            if ($egypt && $egypt->governorates()->count() > 0) {
                $sitemaps[] = ['loc' => "{$baseUrl}/sitemap-governorates-egypt.xml", 'lastmod' => $lastmod];
            }
        }

        // ===== 3. خرائط الشعب والفروع =====
        if (!$settings || $settings->include_branches) {
            $sitemaps[] = ['loc' => "{$baseUrl}/sitemap-branches.xml", 'lastmod' => $lastmod];
        }

        // ===== 4. خرائط أنظمة الدراسة =====
        $sitemaps[] = ['loc' => "{$baseUrl}/sitemap-study-systems.xml", 'lastmod' => $lastmod];

        // ===== 5. خرائط الأوائل =====
        if (!$settings || $settings->include_top_students) {
            $sitemaps[] = ['loc' => "{$baseUrl}/sitemap-top-students.xml", 'lastmod' => $lastmod];
        }

        // ===== 6. خرائط نتائج الطلاب - مقسمة حسب السنوات الدراسية =====
        if (!$settings || $settings->include_students) {
            $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
            
            foreach ($academicYears as $year) {
                // نتحقق من وجود نتائج لهذه السنة
                $hasResults = Result::where('academic_year_id', $year->id)->exists();
                if ($hasResults) {
                    $sitemaps[] = [
                        'loc' => "{$baseUrl}/sitemap-results-{$year->year}-index.xml",
                        'lastmod' => $lastmod
                    ];
                }
            }
        } // إغلاق شرط include_students

        // ===== 7. خرائط المدارس =====
        if (!$settings || $settings->include_schools) {
            $schoolsCount = $this->getUniqueSchoolsCount();
            if ($schoolsCount > 0) {
                $maxUrls = $this->getMaxUrlsPerSitemap();
                $pages = ceil($schoolsCount / $maxUrls);
                for ($i = 1; $i <= $pages; $i++) {
                    $sitemaps[] = ['loc' => "{$baseUrl}/sitemap-schools-{$i}.xml", 'lastmod' => $lastmod];
                }
            }
        }

        // ===== 8. خرائط الإدارات =====
        if (!$settings || $settings->include_administrations) {
            $adminsCount = $this->getUniqueAdministrationsCount();
            if ($adminsCount > 0) {
                $maxUrls = $this->getMaxUrlsPerSitemap();
                $pages = ceil($adminsCount / $maxUrls);
                for ($i = 1; $i <= $pages; $i++) {
                    $sitemaps[] = ['loc' => "{$baseUrl}/sitemap-administrations-{$i}.xml", 'lastmod' => $lastmod];
                }
            }
        }

        return $this->buildSitemapIndexXml($sitemaps);
    }

    /**
     * فهرس فرعي للسنة الدراسية
     */
    public function academicYearIndex(string $yearSlug)
    {
        $xml = Cache::remember("sitemap:index:{$yearSlug}", $this->getCacheTtl(), function () use ($yearSlug) {
            $baseUrl = config('app.url');
            $lastmod = now()->toW3cString();
            $sitemaps = [];
            $maxUrls = $this->getMaxUrlsPerSitemap();
            
            $year = \App\Models\AcademicYear::where('year', $yearSlug)->first();
            if (!$year) return '';

            $examTypes = ExamType::whereHas('country', fn($q) => $q->where('is_active', true))->get();
            
            foreach ($examTypes as $examType) {
                $country = $examType->country;
                $resultsCount = Result::where('exam_type_id', $examType->id)
                    ->where('academic_year_id', $year->id)
                    ->count();
                
                if ($resultsCount > 0) {
                    if ($this->needsGovernoratesSplit($examType)) {
                        // تقسيم حسب المحافظات
                        $govResults = Result::where('exam_type_id', $examType->id)
                            ->where('academic_year_id', $year->id)
                            ->select('governorate_id', DB::raw('count(*) as cnt'))
                            ->whereNotNull('governorate_id')
                            ->groupBy('governorate_id')
                            ->get();
                        
                        foreach ($govResults as $gr) {
                            $gov = Governorate::find($gr->governorate_id);
                            if (!$gov) continue;
                            
                            $pages = ceil($gr->cnt / $maxUrls);
                            for ($i = 1; $i <= $pages; $i++) {
                                $sitemaps[] = [
                                    'loc' => "{$baseUrl}/sitemap/students/{$yearSlug}/{$country->slug}/{$examType->slug}/{$gov->slug}/{$i}.xml",
                                    'lastmod' => $lastmod
                                ];
                            }
                        }
                    } else {
                        // بدون تقسيم محافظات
                        $pages = ceil($resultsCount / $maxUrls);
                        for ($i = 1; $i <= $pages; $i++) {
                            $sitemaps[] = [
                                'loc' => "{$baseUrl}/sitemap/students/{$yearSlug}/{$country->slug}/{$examType->slug}/{$i}.xml",
                                'lastmod' => $lastmod
                            ];
                        }
                    }
                }
            }

            return $this->buildSitemapIndexXml($sitemaps);
        });

        if (empty($xml)) return abort(404);

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    protected function needsGovernoratesSplit(ExamType $examType): bool
    {
        $needsSplit = ['prep', 'azhar-preparatory', 'azhar-primary'];
        return in_array($examType->slug, $needsSplit);
    }

    // ========================================
    // 2. الصفحات الثابتة
    // ========================================

    public function pages()
    {
        $urls = Cache::remember('sitemap:pages', $this->getCacheTtl(), function () {
            $baseUrl = config('app.url');
            $lastmod = now()->toW3cString();

            return [
                ['loc' => $baseUrl, 'lastmod' => $lastmod, 'changefreq' => 'daily', 'priority' => '1.0'],
                ['loc' => "{$baseUrl}/sitemap", 'lastmod' => $lastmod, 'changefreq' => 'weekly', 'priority' => '0.5'],
                ['loc' => "{$baseUrl}/certificate", 'lastmod' => $lastmod, 'changefreq' => 'monthly', 'priority' => '0.6'],
                ['loc' => "{$baseUrl}/contact", 'lastmod' => $lastmod, 'changefreq' => 'monthly', 'priority' => '0.4'],
                ['loc' => "{$baseUrl}/privacy", 'lastmod' => $lastmod, 'changefreq' => 'yearly', 'priority' => '0.2'],
                ['loc' => "{$baseUrl}/terms", 'lastmod' => $lastmod, 'changefreq' => 'yearly', 'priority' => '0.2'],
            ];
        });

        return response($this->buildUrlsetXml($urls), 200)->header('Content-Type', 'application/xml');
    }

    /**
     * 2.1. خريطة المقالات والمدونة
     */
    public function posts()
    {
        $urls = Cache::remember('sitemap:posts', $this->getCacheTtl(), function () {
            $baseUrl = config('app.url');
            $urls = [];

            // 1. الصفحة الرئيسية للمدونة
            $urls[] = [
                'loc' => "{$baseUrl}/blog",
                'lastmod' => now()->toW3cString(),
                'changefreq' => 'daily',
                'priority' => '0.9'
            ];

            // 2. المقالات الفردية
            $posts = \App\Models\Post::published()->get();
            foreach ($posts as $post) {
                $urls[] = [
                    'loc' => route('blog.show', $post),
                    'lastmod' => $post->updated_at->toW3cString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8'
                ];
            }

            return $urls;
        });

        return response($this->buildUrlsetXml($urls), 200)->header('Content-Type', 'application/xml');
    }

    // ========================================
    // 3. خريطة الدول
    // ========================================

    public function countries()
    {
        $urls = Cache::remember('sitemap:countries', $this->getCacheTtl(), function () {
            $baseUrl = config('app.url');
            $lastmod = now()->toW3cString();
            $urls = [];

            foreach (Country::where('is_active', true)->get() as $country) {
                $urls[] = ['loc' => "{$baseUrl}/{$country->slug}", 'lastmod' => $lastmod, 'changefreq' => 'daily', 'priority' => '0.9'];
            }

            return $urls;
        });

        return response($this->buildUrlsetXml($urls), 200)->header('Content-Type', 'application/xml');
    }

    // ========================================
    // 4. خريطة أنواع الشهادات
    // ========================================

    public function examTypes()
    {
        $urls = Cache::remember('sitemap:exam-types', $this->getCacheTtl(), function () {
            $baseUrl = config('app.url');
            $lastmod = now()->toW3cString();
            $urls = [];

            $countries = Country::where('is_active', true)->with('examTypes')->get();
            
            foreach ($countries as $country) {
                foreach ($country->examTypes as $examType) {
                    $url = $this->buildExamTypeUrl($baseUrl, $country, $examType);
                    if ($url) {
                        $urls[] = ['loc' => $url, 'lastmod' => $lastmod, 'changefreq' => 'daily', 'priority' => '0.85'];
                    }
                }
            }

            return collect($urls)->unique('loc')->values()->toArray();
        });

        return response($this->buildUrlsetXml($urls), 200)->header('Content-Type', 'application/xml');
    }

    protected function buildExamTypeUrl(string $baseUrl, Country $country, ExamType $examType): ?string
    {
        if ($country->slug === 'egypt') {
            return match($examType->slug) {
                'prep' => "{$baseUrl}/egypt/preparatory",
                'secondary' => "{$baseUrl}/egypt/secondary",
                'diplomas' => "{$baseUrl}/egypt/diplomas",
                'azhar-secondary' => "{$baseUrl}/egypt/azhar/secondary",
                'azhar-preparatory' => "{$baseUrl}/egypt/azhar/preparatory",
                'azhar-primary' => "{$baseUrl}/egypt/azhar/primary",
                default => "{$baseUrl}/egypt/{$examType->slug}"
            };
        }
        
        return "{$baseUrl}/{$country->slug}/{$examType->slug}";
    }

    // ========================================
    // 5. خريطة المحافظات
    // ========================================

    public function governorates(string $countrySlug)
    {
        $urls = Cache::remember("sitemap:governorates:{$countrySlug}", $this->getCacheTtl(), function () use ($countrySlug) {
            $baseUrl = config('app.url');
            $lastmod = now()->toW3cString();
            $urls = [];

            $country = Country::where('slug', $countrySlug)->where('is_active', true)->first();
            if (!$country) return [];

            foreach ($country->governorates as $governorate) {
                foreach ($country->examTypes as $examType) {
                    $govUrls = $this->buildGovernorateUrls($baseUrl, $country, $examType, $governorate, $lastmod);
                    $urls = array_merge($urls, $govUrls);
                }
            }

            return $urls;
        });

        return response($this->buildUrlsetXml($urls), 200)->header('Content-Type', 'application/xml');
    }

    protected function buildGovernorateUrls(string $baseUrl, Country $country, ExamType $examType, Governorate $governorate, string $lastmod): array
    {
        $urls = [];
        
        // فقط مصر لديها صفحات محافظات حالياً
        if ($country->slug === 'egypt') {
            if ($examType->slug === 'prep') {
                $urls[] = ['loc' => "{$baseUrl}/egypt/preparatory/{$governorate->slug}", 'lastmod' => $lastmod, 'changefreq' => 'daily', 'priority' => '0.85'];
                $urls[] = ['loc' => "{$baseUrl}/egypt/preparatory/{$governorate->slug}/all", 'lastmod' => $lastmod, 'changefreq' => 'daily', 'priority' => '0.75'];
                $urls[] = ['loc' => "{$baseUrl}/egypt/preparatory/{$governorate->slug}/top", 'lastmod' => $lastmod, 'changefreq' => 'weekly', 'priority' => '0.8'];
            }
            // يمكن إضافة محافظات للثانوية والأزهر لاحقاً عند توفر الصفحات
        }
        // للدول الأخرى: لا نولد روابط محافظات حتى يتم إنشاء الصفحات
        
        return $urls;
    }

    // ========================================
    // 6. خريطة الشعب والفروع
    // ========================================

    public function branches()
    {
        $urls = Cache::remember('sitemap:branches', $this->getCacheTtl(), function () {
            $baseUrl = config('app.url');
            $lastmod = now()->toW3cString();
            $urls = [];

            // فروع الدبلومات
            $branches = ExamBranch::where('is_active', true)->get();
            foreach ($branches as $branch) {
                $urls[] = ['loc' => "{$baseUrl}/egypt/diplomas/{$branch->slug}", 'lastmod' => $lastmod, 'changefreq' => 'daily', 'priority' => '0.8'];
            }

            // صفحات إضافية
            $urls[] = ['loc' => "{$baseUrl}/egypt/secondary/all", 'lastmod' => $lastmod, 'changefreq' => 'daily', 'priority' => '0.8'];
            $urls[] = ['loc' => "{$baseUrl}/egypt/diplomas", 'lastmod' => $lastmod, 'changefreq' => 'daily', 'priority' => '0.85'];

            return $urls;
        });

        return response($this->buildUrlsetXml($urls), 200)->header('Content-Type', 'application/xml');
    }

    // ========================================
    // 7. خريطة أنظمة الدراسة
    // ========================================

    public function studySystems()
    {
        $urls = Cache::remember('sitemap:study-systems', $this->getCacheTtl(), function () {
            $baseUrl = config('app.url');
            $lastmod = now()->toW3cString();
            $urls = [];

            $systems = Result::select('system_type')->whereNotNull('system_type')->distinct()->pluck('system_type');

            foreach ($systems as $system) {
                if ($system) {
                    $urls[] = ['loc' => "{$baseUrl}/egypt/secondary/all?system={$system}", 'lastmod' => $lastmod, 'changefreq' => 'weekly', 'priority' => '0.7'];
                }
            }

            return $urls;
        });

        return response($this->buildUrlsetXml($urls), 200)->header('Content-Type', 'application/xml');
    }

    // ========================================
    // 8. خريطة الأوائل
    // ========================================

    public function topStudents()
    {
        $urls = Cache::remember('sitemap:top-students', $this->getCacheTtl(), function () {
            $baseUrl = config('app.url');
            $lastmod = now()->toW3cString();
            $urls = [];

            // أوائل الثانوية
            if (Result::whereHas('examType', fn($q) => $q->where('slug', 'secondary'))->exists()) {
                $urls[] = ['loc' => "{$baseUrl}/egypt/secondary/all?sort=total_desc", 'lastmod' => $lastmod, 'changefreq' => 'weekly', 'priority' => '0.9'];
            }

            // أوائل الإعدادية حسب المحافظات
            $prepExamType = ExamType::where('slug', 'prep')->where('country_id', 1)->first();
            if ($prepExamType) {
                $govsWithResults = Result::where('exam_type_id', $prepExamType->id)
                    ->select('governorate_id')
                    ->distinct()
                    ->whereNotNull('governorate_id')
                    ->pluck('governorate_id');

                foreach ($govsWithResults as $govId) {
                    $gov = Governorate::find($govId);
                    if ($gov) {
                        $urls[] = ['loc' => "{$baseUrl}/egypt/preparatory/{$gov->slug}/top", 'lastmod' => $lastmod, 'changefreq' => 'weekly', 'priority' => '0.85'];
                    }
                }
            }

            return $urls;
        });

        return response($this->buildUrlsetXml($urls), 200)->header('Content-Type', 'application/xml');
    }

    // ========================================
    // 9. خرائط نتائج الطلاب
    // ========================================

    /**
     * نتائج بدون تقسيم محافظات
     */
    public function students(string $yearSlug, string $countrySlug, string $examTypeSlug, int $page = 1)
    {
        $cacheKey = "sitemap:students:{$yearSlug}:{$countrySlug}:{$examTypeSlug}:{$page}";
        
        $urls = Cache::remember($cacheKey, $this->getCacheTtl(), function () use ($yearSlug, $countrySlug, $examTypeSlug, $page) {
            $baseUrl = config('app.url');
            $lastmod = now()->toW3cString();
            $urls = [];
            $offset = ($page - 1) * $this->getMaxUrlsPerSitemap();

            $year = \App\Models\AcademicYear::where('year', $yearSlug)->first();
            if (!$year) return [];

            $country = Country::where('slug', $countrySlug)->first();
            if (!$country) return [];

            $examType = ExamType::where('slug', $examTypeSlug)->where('country_id', $country->id)->first();
            if (!$examType) return [];

            $results = Result::where('exam_type_id', $examType->id)
                ->where('academic_year_id', $year->id)
                ->select('seat_number', 'governorate_id', 'semester')
                ->orderBy('id')
                ->offset($offset)
                ->limit($this->getMaxUrlsPerSitemap())
                ->get();

            foreach ($results as $result) {
                $url = $this->buildStudentUrl($baseUrl, $country, $examType, $yearSlug, $result);
                if ($url) {
                    $urls[] = ['loc' => $url, 'lastmod' => $lastmod, 'changefreq' => 'yearly', 'priority' => '0.5'];
                }
            }

            return $urls;
        });

        return response($this->buildUrlsetXml($urls), 200)->header('Content-Type', 'application/xml');
    }

    /**
     * نتائج مع تقسيم محافظات
     */
    public function studentsWithGov(string $yearSlug, string $countrySlug, string $examTypeSlug, string $govSlug, int $page = 1)
    {
        $cacheKey = "sitemap:students:{$yearSlug}:{$countrySlug}:{$examTypeSlug}:{$govSlug}:{$page}";
        
        $urls = Cache::remember($cacheKey, $this->getCacheTtl(), function () use ($yearSlug, $countrySlug, $examTypeSlug, $govSlug, $page) {
            $baseUrl = config('app.url');
            $lastmod = now()->toW3cString();
            $urls = [];
            $offset = ($page - 1) * $this->getMaxUrlsPerSitemap();

            $year = \App\Models\AcademicYear::where('year', $yearSlug)->first();
            if (!$year) return [];

            $country = Country::where('slug', $countrySlug)->first();
            if (!$country) return [];

            $examType = ExamType::where('slug', $examTypeSlug)->where('country_id', $country->id)->first();
            if (!$examType) return [];

            $governorate = Governorate::where('slug', $govSlug)->first();
            if (!$governorate) return [];

            $results = Result::where('exam_type_id', $examType->id)
                ->where('governorate_id', $governorate->id)
                ->where('academic_year_id', $year->id)
                ->select('seat_number', 'semester')
                ->orderBy('id')
                ->offset($offset)
                ->limit($this->getMaxUrlsPerSitemap())
                ->get();

            foreach ($results as $result) {
                $url = $this->buildStudentUrlWithGov($baseUrl, $country, $examType, $governorate, $yearSlug, $result);
                if ($url) {
                    $urls[] = ['loc' => $url, 'lastmod' => $lastmod, 'changefreq' => 'yearly', 'priority' => '0.5'];
                }
            }

            return $urls;
        });

        return response($this->buildUrlsetXml($urls), 200)->header('Content-Type', 'application/xml');
    }

    protected function buildStudentUrl(string $baseUrl, Country $country, ExamType $examType, string $yearSlug, $result): ?string
    {
        $termSlug = ($result->semester == 2) ? 'term2' : 'term1';

        if ($country->slug === 'egypt') {
            return match($examType->slug) {
                'secondary' => "{$baseUrl}/egypt/secondary/student/{$result->seat_number}",
                'azhar-secondary' => "{$baseUrl}/egypt/azhar/secondary/student/{$result->seat_number}",
                default => null
            };
        }
        
        return "{$baseUrl}/{$country->slug}/{$examType->slug}/student/{$result->seat_number}";
    }

    protected function buildStudentUrlWithGov(string $baseUrl, Country $country, ExamType $examType, Governorate $gov, string $yearSlug, $result): ?string
    {
        $termSlug = ($result->semester == 2) ? 'term2' : 'term1';

        if ($country->slug === 'egypt') {
            return match($examType->slug) {
                'prep' => "{$baseUrl}/egypt/preparatory/{$gov->slug}/{$yearSlug}/{$termSlug}/{$result->seat_number}",
                'azhar-preparatory' => "{$baseUrl}/egypt/azhar/preparatory/{$gov->slug}/{$result->seat_number}",
                'azhar-primary' => "{$baseUrl}/egypt/azhar/primary/{$gov->slug}/{$result->seat_number}",
                default => null
            };
        }
        
        return "{$baseUrl}/{$country->slug}/{$examType->slug}/{$gov->slug}/{$result->seat_number}";
    }

    // ========================================
    // 10. خريطة المدارس
    // ========================================

    public function schools(int $page = 1)
    {
        $cacheKey = "sitemap:schools:{$page}";
        
        $urls = Cache::remember($cacheKey, $this->getCacheTtl(), function () use ($page) {
            $baseUrl = config('app.url');
            $lastmod = now()->toW3cString();
            $urls = [];
            $offset = ($page - 1) * $this->getMaxUrlsPerSitemap();

            $schools = $this->getUniqueSchools($offset, $this->getMaxUrlsPerSitemap());

            foreach ($schools as $school) {
                $urls[] = [
                    'loc' => "{$baseUrl}/egypt/preparatory/{$school['gov_slug']}/all?search=" . urlencode($school['school_name']),
                    'lastmod' => $lastmod,
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            }

            return $urls;
        });

        return response($this->buildUrlsetXml($urls), 200)->header('Content-Type', 'application/xml');
    }

    // ========================================
    // 11. خريطة الإدارات
    // ========================================

    public function administrations(int $page = 1)
    {
        $cacheKey = "sitemap:administrations:{$page}";
        
        $urls = Cache::remember($cacheKey, $this->getCacheTtl(), function () use ($page) {
            $baseUrl = config('app.url');
            $lastmod = now()->toW3cString();
            $urls = [];
            $offset = ($page - 1) * $this->getMaxUrlsPerSitemap();

            $admins = $this->getUniqueAdministrations($offset, $this->getMaxUrlsPerSitemap());

            foreach ($admins as $admin) {
                $urls[] = [
                    'loc' => "{$baseUrl}/egypt/preparatory/{$admin['gov_slug']}/all?search=" . urlencode($admin['admin_name']),
                    'lastmod' => $lastmod,
                    'changefreq' => 'weekly',
                    'priority' => '0.65',
                ];
            }

            return $urls;
        });

        return response($this->buildUrlsetXml($urls), 200)->header('Content-Type', 'application/xml');
    }

    // ========================================
    // 12. صفحة HTML
    // ========================================

    public function htmlIndex()
    {
        $baseUrl = config('app.url');
        $countries = Country::where('is_active', true)->with(['examTypes', 'governorates'])->get();
        
        // بناء قائمة الخرائط للعرض
        $sitemaps = [];
        
        // 1. الصفحات الثابتة
        $sitemaps[] = [
            'name' => 'الصفحات الرئيسية',
            'url' => "{$baseUrl}/sitemap-pages.xml",
            'icon' => 'fa-file-alt',
            'count' => 6,
        ];
        
        // 1.1. المقالات والمدونة
        $sitemaps[] = [
            'name' => 'المدونة والأخبار التعليمية',
            'url' => "{$baseUrl}/sitemap-posts.xml",
            'icon' => 'fa-newspaper',
            'count' => \App\Models\Post::published()->count() + 1,
        ];
        
        // 2. الدول
        $sitemaps[] = [
            'name' => 'الدول العربية',
            'url' => "{$baseUrl}/sitemap-countries.xml",
            'icon' => 'fa-globe-africa',
            'count' => $countries->count(),
        ];
        
        // 3. أنواع الشهادات
        $examTypesCount = ExamType::whereHas('country', fn($q) => $q->where('is_active', true))->count();
        $sitemaps[] = [
            'name' => 'أنواع الشهادات',
            'url' => "{$baseUrl}/sitemap-exam-types.xml",
            'icon' => 'fa-graduation-cap',
            'count' => $examTypesCount,
        ];
        
        // 4. المحافظات (مصر فقط حالياً لأن صفحات المحافظات موجودة لمصر فقط)
        $egypt = $countries->firstWhere('code', 'EG');
        if ($egypt && $egypt->governorates->count() > 0) {
            $sitemaps[] = [
                'name' => "محافظات مصر",
                'url' => "{$baseUrl}/sitemap-governorates-egypt.xml",
                'icon' => 'fa-map-marked-alt',
                'count' => $egypt->governorates->count() * 3, // كل محافظة لها 3 روابط تقريباً
                'sub_count' => $egypt->governorates->count() . ' محافظة',
            ];
        }
        
        // 5. الشعب والفروع
        $branchesCount = ExamBranch::where('is_active', true)->count() + 2;
        $sitemaps[] = [
            'name' => 'الشعب والفروع',
            'url' => "{$baseUrl}/sitemap-branches.xml",
            'icon' => 'fa-code-branch',
            'count' => $branchesCount,
        ];
        
        // 6. أنظمة الدراسة
        $systemsCount = Result::select('system_type')->whereNotNull('system_type')->distinct()->count();
        if ($systemsCount > 0) {
            $sitemaps[] = [
                'name' => 'أنظمة الدراسة',
                'url' => "{$baseUrl}/sitemap-study-systems.xml",
                'icon' => 'fa-cogs',
                'count' => $systemsCount,
            ];
        }
        
        // 7. الأوائل
        $sitemaps[] = [
            'name' => 'أوائل الطلاب',
            'url' => "{$baseUrl}/sitemap-top-students.xml",
            'icon' => 'fa-trophy',
            'count' => $this->getTopStudentsCount(),
        ];
        
        // 8. نتائج الطلاب
        $studentsCount = Result::count();
        $studentsSitemaps = ceil($studentsCount / $this->getMaxUrlsPerSitemap());
        $sitemaps[] = [
            'name' => 'نتائج الطلاب',
            'url' => "{$baseUrl}/sitemap.xml",
            'icon' => 'fa-users',
            'count' => $studentsCount,
            'sub_count' => $studentsSitemaps . ' خريطة',
        ];
        
        // 9. المدارس
        $schoolsCount = $this->getUniqueSchoolsCount();
        if ($schoolsCount > 0) {
            $sitemaps[] = [
                'name' => 'المدارس',
                'url' => "{$baseUrl}/sitemap-schools-1.xml",
                'icon' => 'fa-school',
                'count' => $schoolsCount,
            ];
        }

        $stats = [
            'countries' => $countries->count(),
            'governorates' => Governorate::count(),
            'exam_types' => $examTypesCount,
            'students' => $studentsCount,
            'schools' => $schoolsCount,
        ];

        return view('sitemap.index', compact('countries', 'stats', 'sitemaps'));
    }
    
    protected function getTopStudentsCount(): int
    {
        $count = 0;
        
        // أوائل الثانوية
        if (Result::whereHas('examType', fn($q) => $q->where('slug', 'secondary'))->exists()) {
            $count++;
        }
        
        // أوائل الإعدادية حسب المحافظات
        $prepExamType = ExamType::where('slug', 'prep')->where('country_id', 1)->first();
        if ($prepExamType) {
            $count += Result::where('exam_type_id', $prepExamType->id)
                ->select('governorate_id')
                ->distinct()
                ->whereNotNull('governorate_id')
                ->count();
        }
        
        return $count;
    }

    // ========================================
    // Helper Methods
    // ========================================

    protected function getUniqueSchoolsCount(): int
    {
        return Cache::remember('sitemap:schools-count', 3600, function () {
            return Result::whereNotNull('subjects_data')
                ->whereRaw("JSON_EXTRACT(subjects_data, '$.\"المدرسة\"') IS NOT NULL")
                ->selectRaw("COUNT(DISTINCT JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"المدرسة\"'))) as cnt")
                ->value('cnt') ?? 0;
        });
    }

    protected function getUniqueAdministrationsCount(): int
    {
        return Cache::remember('sitemap:admins-count', 3600, function () {
            return Result::whereNotNull('subjects_data')
                ->whereRaw("JSON_EXTRACT(subjects_data, '$.\"الإدارة\"') IS NOT NULL")
                ->selectRaw("COUNT(DISTINCT JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"الإدارة\"'))) as cnt")
                ->value('cnt') ?? 0;
        });
    }

    protected function getUniqueSchools(int $offset, int $limit): array
    {
        $governorates = Governorate::whereHas('country', fn($q) => $q->where('slug', 'egypt'))->get();
        $allSchools = collect();

        foreach ($governorates as $governorate) {
            $schools = Result::where('governorate_id', $governorate->id)
                ->whereNotNull('subjects_data')
                ->selectRaw("DISTINCT JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"المدرسة\"')) as school_name")
                ->whereRaw("JSON_EXTRACT(subjects_data, '$.\"المدرسة\"') IS NOT NULL")
                ->pluck('school_name')
                ->filter()
                ->map(fn($name) => ['school_name' => $name, 'gov_slug' => $governorate->slug]);

            $allSchools = $allSchools->concat($schools);
        }

        return $allSchools->unique('school_name')->skip($offset)->take($limit)->values()->toArray();
    }

    protected function getUniqueAdministrations(int $offset, int $limit): array
    {
        $governorates = Governorate::whereHas('country', fn($q) => $q->where('slug', 'egypt'))->get();
        $allAdmins = collect();

        foreach ($governorates as $governorate) {
            $admins = Result::where('governorate_id', $governorate->id)
                ->whereNotNull('subjects_data')
                ->selectRaw("DISTINCT JSON_UNQUOTE(JSON_EXTRACT(subjects_data, '$.\"الإدارة\"')) as admin_name")
                ->whereRaw("JSON_EXTRACT(subjects_data, '$.\"الإدارة\"') IS NOT NULL")
                ->pluck('admin_name')
                ->filter()
                ->map(fn($name) => ['admin_name' => $name, 'gov_slug' => $governorate->slug]);

            $allAdmins = $allAdmins->concat($admins);
        }

        return $allAdmins->unique('admin_name')->skip($offset)->take($limit)->values()->toArray();
    }

    protected function buildSitemapIndexXml(array $sitemaps): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="/sitemap.xsl"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($sitemaps as $sitemap) {
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>" . $this->escapeUrl($sitemap['loc']) . "</loc>\n";
            $xml .= "    <lastmod>{$sitemap['lastmod']}</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>' . "\n";
        return $xml;
    }

    protected function buildUrlsetXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="/sitemap.xsl"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . $this->escapeUrl($url['loc']) . "</loc>\n";
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            if (isset($url['changefreq'])) {
                $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            }
            if (isset($url['priority'])) {
                $xml .= "    <priority>{$url['priority']}</priority>\n";
            }
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>' . "\n";
        return $xml;
    }

    /**
     * Escape URL for XML sitemap according to Google guidelines
     * Escapes special characters: & ' " < >
     */
    protected function escapeUrl(string $url): string
    {
        // First decode any already encoded entities to avoid double encoding
        $url = html_entity_decode($url, ENT_QUOTES | ENT_XML1, 'UTF-8');
        
        // Encode special XML characters
        return htmlspecialchars($url, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    public function clearCache()
    {
        $keys = ['sitemap:index', 'sitemap:pages', 'sitemap:countries', 'sitemap:exam-types', 'sitemap:branches', 'sitemap:study-systems', 'sitemap:top-students', 'sitemap:schools-count', 'sitemap:admins-count'];
        foreach ($keys as $key) {
            Cache::forget($key);
        }

        $countries = Country::where('is_active', true)->pluck('slug');
        foreach ($countries as $slug) {
            Cache::forget("sitemap:governorates:{$slug}");
        }

        return response()->json(['message' => 'Sitemap cache cleared']);
    }
}
