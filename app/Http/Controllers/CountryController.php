<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\SeoService;
use App\Services\SchemaService;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function __construct(
        private SeoService $seoService
    ) {}

    public function show($code)
    {
        $country = Country::with('examTypes')->where('code', $code)->where('is_active', true)->firstOrFail();
        
        $meta = $this->seoService->generateMetaTags(
            "نتائج الامتحانات في {$country->name_ar}",
            "استعلم الآن عن نتائج الامتحانات والشهادات في {$country->name_ar} عبر منصة نتيجتي"
        );
        
        // Generate structured data for country page
        $structuredData = SchemaService::countryPage($country, $country->examTypes->all());

        return view('countries.show', compact('country', 'meta', 'structuredData'));
    }
}
