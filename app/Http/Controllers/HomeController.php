<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\SeoService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        private SeoService $seoService
    ) {}

    public function index(Request $request)
    {
        $meta = $this->seoService->generateMetaTags(
            'نتائج الطلاب في الوطن العربي',
            'منصة نتيجتي لعرض نتائج الشهادات الإعدادية والثانوية والدبلومات في الوطن العربي'
        );

        $countries = Country::with('examTypes')->where('is_active', true)->get();

        $structuredData = $this->seoService->generateOrganizationSchema();

        $latestPosts = \App\Models\Post::published()
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('home', compact('meta', 'countries', 'structuredData', 'latestPosts'));
    }
}
