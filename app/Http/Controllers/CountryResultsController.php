<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\ExamType;
use App\Models\Result;
use App\Services\SeoService;
use App\Services\SchemaService;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CountryResultsController extends Controller
{
    public function __construct(
        private SeoService $seoService
    ) {}

    /**
     * Show country exam types
     */
    public function index(Country $country)
    {
        // Redirect Egypt to its own controller
        if ($country->code === 'EG') {
            return redirect()->route('egypt.index');
        }

        $examTypes = $country->examTypes;

        // Dynamic Academic Year Calculation
        // If current month is Sep (9) or later, we are in the new academic year (ending next cal year)
        $currentMonth = date('n');
        $currentYear = date('Y');
        $year = ($currentMonth >= 9) ? $currentYear + 1 : $currentYear;

        $fullYear = $year;
        if ($year > 2000) {
            $fullYear = ($year - 1) . ' - ' . $year;
        }

        $title = "نتائج شهادات {$country->name_ar} {$fullYear}";
        if ($country->semester) {
             $title .= " {$country->semester}";
        }
        
        $meta = $this->seoService->generateMetaTags(
            $title,
            "نتائج الامتحانات في {$country->name_ar} للعام الدراسي {$fullYear}"
        );

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => $country->name_ar, 'url' => route('country.index', $country)],
        ];

        $structuredData = SchemaService::countryPage($country, $examTypes->all());

        return view('country.index', compact('title', 'meta', 'examTypes', 'country', 'breadcrumbs', 'fullYear', 'structuredData'));
    }

    /**
     * Show exam type results page
     * Uses result_service_type to determine the view
     */
    public function examType(Country $country, string $slug)
    {
        // Redirect Egypt to its own controller
        if ($country->code === 'EG') {
            return redirect()->route('egypt.index');
        }

        $examType = $country->examTypes()->where('slug', $slug)->firstOrFail();
        
        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => $country->name_ar, 'url' => route('country.index', $country)],
            ['name' => $examType->name_ar, 'url' => route('country.exam', [$country, $slug])],
        ];

        // Dynamic Academic Year Calculation
        $currentMonth = date('n');
        $currentYear = date('Y');
        $year = ($currentMonth >= 9) ? $currentYear + 1 : $currentYear;

        $fullYear = $year;
        if ($year > 2000) {
            $fullYear = ($year - 1) . ' - ' . $year;
        }

        // Build Title - استخدام العنوان من لوحة التحكم أولاً
        $suffix = $fullYear;
        if ($country->semester) {
             $suffix .= ' ' . $country->semester;
        }

        $title = $examType->content_title ?? ("نتيجة {$examType->name_ar} في {$country->name_ar} {$suffix}");

        $meta = $this->seoService->generateMetaTags(
            $examType->seo_title ?? $title,
            $examType->seo_description ?? ("نتيجة {$examType->name_ar} في {$country->name_ar} - ابحث برقم الجلوس أو الاسم {$fullYear}")
        );

        $structuredData = SchemaService::examTypePage($examType, $country->governorates->all());

        $showYearFilter = \App\Models\Setting::where('key', 'show_academic_year_filter')->value('value');
        $academicYears = $showYearFilter ? \App\Models\AcademicYear::where('is_active', true)->orderBy('year', 'desc')->get() : collect([]);

        // Use country.search view which handles all service types
        return view('country.search', compact(
            'title', 
            'meta', 
            'examType', 
            'country', 
            'breadcrumbs', 
            'structuredData', 
            'fullYear', 
            'showYearFilter', 
            'academicYears'
        ));
    }

    /**
     * Show specific student result page with caching
     */
    public function studentResult(Country $country, string $slug, string $seat_number)
    {
        // Redirect Egypt to its own controller
        if ($country->code === 'EG') {
            return redirect()->route('egypt.index');
        }

        $examType = $country->examTypes()->where('slug', $slug)->firstOrFail();
        
        // Get academic year
        $academicYear = \App\Models\AcademicYear::where('is_active', true)->first();

        // Dynamic Academic Year Calculation
        $currentMonth = date('n');
        $currentYear = date('Y');
        $year = ($currentMonth >= 9) ? $currentYear + 1 : $currentYear;

        $fullYear = $year;
        if ($year > 2000) {
            $fullYear = ($year - 1) . ' - ' . $year;
        }

        // Build suffix
        $suffix = $fullYear;
        if ($country->semester) {
             $suffix .= ' ' . $country->semester;
        }

        $title = "نتيجة الطالب {$seat_number} - {$examType->name_ar} {$country->name_ar} {$suffix}";

        $meta = $this->seoService->generateMetaTags(
            $title,
            "نتيجة الطالب رقم جلوس {$seat_number} في {$examType->name_ar} - تفاصيل الدرجات والمجموع الكلي"
        );

        $structuredData = SchemaService::examTypePage($examType, $country->governorates->all());

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => $country->name_ar, 'url' => route('country.index', $country)],
            ['name' => $examType->name_ar, 'url' => route('country.exam', [$country, $slug])],
            ['name' => "نتيجة {$seat_number}"],
        ];

        $showYearFilter = \App\Models\Setting::where('key', 'show_academic_year_filter')->value('value');
        $academicYears = $showYearFilter ? \App\Models\AcademicYear::where('is_active', true)->orderBy('year', 'desc')->get() : collect([]);

        // Pass seat_number to auto-search
        return view('country.search', compact(
            'title', 
            'meta', 
            'examType', 
            'country', 
            'breadcrumbs', 
            'structuredData', 
            'fullYear', 
            'showYearFilter', 
            'academicYears',
            'seat_number'
        ));
    }
}
