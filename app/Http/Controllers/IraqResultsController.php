<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Governorate;
use App\Services\SeoService;
use App\Services\SchemaService;
use Illuminate\Http\Request;

class IraqResultsController extends Controller
{
    public function __construct(
        private SeoService $seoService
    ) {}

    /**
     * Show Iraq provinces grid
     */
    public function index()
    {
        $iraq = Country::where('code', 'IQ')->firstOrFail();
        $governorates = $iraq->governorates;

        $meta = $this->seoService->generateMetaTags(
            'نتائج العراق - السادس الإعدادي',
            'نتيجة السادس الإعدادي لجميع محافظات العراق'
        );

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج العراق', 'url' => route('iraq.index')],
        ];

        // Get exam type for structured data
        $examType = $iraq->examTypes()->where('slug', 'preparatory')->first();
        if ($examType) {
            $structuredData = SchemaService::examTypePage($examType, $governorates->all());
        } else {
            $structuredData = SchemaService::simpleExamTypePage('السادس الإعدادي', 'العراق', $governorates->all());
        }

        return view('iraq.index', compact('meta', 'governorates', 'breadcrumbs', 'structuredData'));
    }

    /**
     * Show province results search page
     */
    public function provinceResults(Governorate $governorate)
    {
        $meta = $this->seoService->generateMetaTags(
            "نتيجة السادس الإعدادي {$governorate->name_ar}",
            "نتيجة السادس الإعدادي محافظة {$governorate->name_ar} - ابحث برقم الجلوس أو الاسم"
        );

        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('home')],
            ['name' => 'نتائج العراق', 'url' => route('iraq.index')],
            ['name' => $governorate->name_ar, 'url' => route('iraq.province.results', $governorate)],
        ];

        $showYearFilter = \App\Models\Setting::where('key', 'show_academic_year_filter')->value('value');
        $academicYears = $showYearFilter ? \App\Models\AcademicYear::where('is_active', true)->orderBy('year', 'desc')->get() : collect([]);

        $structuredData = SchemaService::simpleGovernoratePage($governorate, 'السادس الإعدادي');

        return view('iraq.search', compact('meta', 'governorate', 'breadcrumbs', 'showYearFilter', 'academicYears', 'structuredData'));
    }
}
