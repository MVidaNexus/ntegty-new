<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Governorate;
use App\Services\SeoService;
use App\Services\SchemaService;
use Illuminate\Http\Request;

class LibyaResultsController extends Controller
{
    public function __construct(
        private SeoService $seoService
    ) {}

    /**
     * Show Libya preparatory governorates grid
     */
    public function index()
    {
        $libya = Country::where('code', 'LY')->firstOrFail();
        $governorates = $libya->governorates;

        $meta = $this->seoService->generateMetaTags(
            'نتائج الشهادة الإعدادية - ليبيا',
            'نتيجة الشهادة الإعدادية لجميع مناطق ومدن ليبيا'
        );

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج ليبيا', 'url' => route('libya.index')],
        ];

        // Get exam type for structured data
        $examType = $libya->examTypes()->where('slug', 'preparatory')->first();
        if ($examType) {
            $structuredData = SchemaService::examTypePage($examType, $governorates->all());
        } else {
            $structuredData = SchemaService::simpleExamTypePage('الشهادة الإعدادية', 'ليبيا', $governorates->all());
        }

        return view('libya.index', compact('meta', 'governorates', 'breadcrumbs', 'structuredData'));
    }

    /**
     * Show governorate results search page
     */
    public function governorateResults(Governorate $governorate)
    {
        $meta = $this->seoService->generateMetaTags(
            "نتيجة الشهادة الإعدادية {$governorate->name_ar}",
            "نتيجة الشهادة الإعدادية منطقة {$governorate->name_ar} - ليبيا - ابحث برقم الجلوس أو الاسم"
        );

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج ليبيا', 'url' => route('libya.index')],
            ['name' => $governorate->name_ar, 'url' => route('libya.governorate.results', $governorate)],
        ];

        $showYearFilter = \App\Models\Setting::where('key', 'show_academic_year_filter')->value('value');
        $academicYears = $showYearFilter ? \App\Models\AcademicYear::where('is_active', true)->orderBy('year', 'desc')->get() : collect([]);

        $structuredData = SchemaService::simpleGovernoratePage($governorate, 'الشهادة الإعدادية');

        return view('libya.search', compact('meta', 'governorate', 'breadcrumbs', 'showYearFilter', 'academicYears', 'structuredData'));
    }
}
