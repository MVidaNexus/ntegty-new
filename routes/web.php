<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EgyptResultsController;
use App\Http\Controllers\CountryResultsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home')->middleware(['cache.response']);

// Dynamic Robots.txt - read from database
Route::get('/robots.txt', function () {
    $defaultContent = "User-agent: *\nDisallow: /admin\nDisallow: /nova\nDisallow: /dashboard\nAllow: /\n\nSitemap: " . url('/sitemap.xml');
    $content = \App\Models\SiteSetting::get('robots_txt', $defaultContent);
    return response($content, 200)
        ->header('Content-Type', 'text/plain; charset=utf-8')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
});

// ads.txt - serve from public folder
Route::get('/ads.txt', function () {
    $adsPath = public_path('ads.txt');
    if (file_exists($adsPath)) {
        return response(file_get_contents($adsPath), 200)
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=86400');
    }
    abort(404);
});

// Certificate Generator
Route::get('/certificate', function () {
    $settings = \App\Models\CertificateSetting::getActive();
    
    // دعم المعاينة من لوحة التحكم
    $previewData = [];
    if (request('preview')) {
        $previewData = [
            'previewMode' => true,
            'previewName' => request('name', 'طالب تجريبي'),
            'previewSchool' => request('school', 'مدرسة النصر'),
            'previewExam' => request('exam', 'الشهادة الإعدادية'),
            'previewScore' => request('score', '280'),
            'previewMax' => request('max', '280'),
            'previewPercentage' => request('percentage', '100%'),
        ];
    }
    
    return view('certificate.index', compact('settings', 'previewData'));
})->name('certificate.index');

// Static Pages
Route::view('/contact', 'contact')->name('contact');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');

// Egypt Routes (Special case - keeps governorate cards for preparatory)
Route::prefix('egypt')->name('egypt.')->middleware(['cache.response'])->group(function () {
    Route::get('/', [EgyptResultsController::class, 'index'])->name('index');
    Route::get('/preparatory', [EgyptResultsController::class, 'preparatory'])->name('preparatory');
    Route::get('/preparatory/{governorate}', [EgyptResultsController::class, 'governorateResults'])->name('governorate.results');
    Route::get('/preparatory/{governorate}/all', [EgyptResultsController::class, 'governorateAllResults'])->name('governorate.all-results');
    Route::get('/preparatory/{governorate}/top', [EgyptResultsController::class, 'governorateTopResults'])->name('governorate.top-results');
    
    // New URL format with academic year and term: /egypt/preparatory/{gov}/{year}/{term}/{seat}
    Route::get('/preparatory/{governorate}/{academic_year}/{term}/{seat_number}', [EgyptResultsController::class, 'governorateResultDetail'])
        ->name('governorate.result.detail')
        ->where([
            'academic_year' => '[0-9]{4}-[0-9]{4}',
            'term' => 'term1|term2|all',
            'seat_number' => '[0-9]+'
        ]);
    
    // Old URL format - redirect to new format for backward compatibility
    Route::get('/preparatory/{governorate}/{seat_number}', function(\App\Models\Governorate $governorate, $seat_number) {
        $academicYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $currentYear = (int)date('Y');
        $yearSlug = $academicYear?->year ?? ($currentYear . '-' . ($currentYear + 1));
        
        $egypt = \App\Models\Country::where('code', 'EG')->first();
        $term = 'term1';
        if ($egypt && $egypt->semester) {
            if (str_contains($egypt->semester, 'الثاني') || str_contains($egypt->semester, 'ثاني')) {
                $term = 'term2';
            }
        }
        
        return redirect()->route('egypt.governorate.result.detail', [
            'governorate' => $governorate->slug,
            'academic_year' => $yearSlug,
            'term' => $term,
            'seat_number' => $seat_number
        ], 301);
    })->where('seat_number', '[0-9]+')->name('governorate.result.detail.legacy');
    Route::get('/secondary', [EgyptResultsController::class, 'secondary'])->name('secondary');
    Route::get('/secondary/all', [EgyptResultsController::class, 'secondaryAllResults'])->name('secondary.all-results');
    Route::get('/secondary/{branch}', [EgyptResultsController::class, 'secondaryBranch'])->name('secondary.branch');
    Route::get('/secondary/{branch}/all', [EgyptResultsController::class, 'secondaryBranchAllResults'])->name('secondary.branch.all-results');
    Route::get('/secondary/student/{seat_number}', [EgyptResultsController::class, 'secondaryStudentResult'])->name('secondary.student');
    Route::get('/diplomas', [EgyptResultsController::class, 'diplomasIndex'])->name('diplomas.index');
    Route::get('/diplomas/{type}', [EgyptResultsController::class, 'diplomas'])->name('diplomas');
    
    // Azhar Routes
    Route::get('/azhar/secondary', [EgyptResultsController::class, 'azharSecondary'])->name('azhar.secondary');
    Route::get('/azhar/preparatory', [EgyptResultsController::class, 'azharPreparatory'])->name('azhar.preparatory');
    Route::get('/azhar/primary', [EgyptResultsController::class, 'azharPrimary'])->name('azhar.primary');
});

// Unified Country Routes (For all other countries: Libya, Iraq, Sudan, etc.)
// Search Routes
Route::post('/search', [SearchController::class, 'search'])->name('search')->middleware('throttle:30,1');
Route::get('/result/{id}', [SearchController::class, 'show'])->name('result.show')->middleware(['cache.response']);
Route::get('/result/{id}/print', [SearchController::class, 'print'])->name('result.print');

// ===== Sitemap Routes =====
// 1. الفهرس الرئيسي
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');

// 2. صفحة HTML للخريطة
Route::get('/sitemap', [SitemapController::class, 'htmlIndex'])->name('sitemap.html');

// 3. الصفحات الثابتة
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');

// 4. الدول
Route::get('/sitemap-countries.xml', [SitemapController::class, 'countries'])->name('sitemap.countries');

// 5. أنواع الشهادات
Route::get('/sitemap-exam-types.xml', [SitemapController::class, 'examTypes'])->name('sitemap.exam-types');

// 6. المحافظات لكل دولة
Route::get('/sitemap-governorates-{country}.xml', [SitemapController::class, 'governorates'])
    ->where('country', '[a-z\-]+')
    ->name('sitemap.governorates');

// 7. الشعب والفروع
Route::get('/sitemap-branches.xml', [SitemapController::class, 'branches'])->name('sitemap.branches');

// 8. أنظمة الدراسة
Route::get('/sitemap-study-systems.xml', [SitemapController::class, 'studySystems'])->name('sitemap.study-systems');

// 9. الأوائل
Route::get('/sitemap-top-students.xml', [SitemapController::class, 'topStudents'])->name('sitemap.top-students');

// 10. فهرس النتائج حسب السنة الدراسية
Route::get('/sitemap-results-{year}-index.xml', [SitemapController::class, 'academicYearIndex'])
    ->where('year', '[0-9\-]+')
    ->name('sitemap.year-index');

// 11. نتائج الطلاب (مع محافظة)
Route::get('/sitemap/students/{year}/{country}/{examType}/{gov}/{page}.xml', [SitemapController::class, 'studentsWithGov'])
    ->where(['year' => '[0-9\-]+', 'country' => '[a-z]+', 'examType' => '[a-z\-]+', 'gov' => '[a-z\-]+', 'page' => '[0-9]+'])
    ->name('sitemap.students-gov');

// 12. نتائج الطلاب (بدون محافظة)
Route::get('/sitemap/students/{year}/{country}/{examType}/{page}.xml', [SitemapController::class, 'students'])
    ->where(['year' => '[0-9\-]+', 'country' => '[a-z]+', 'examType' => '[a-z\-]+', 'page' => '[0-9]+'])
    ->name('sitemap.students');

// 12. المدارس
Route::get('/sitemap-schools-{page}.xml', [SitemapController::class, 'schools'])
    ->where('page', '[0-9]+')
    ->name('sitemap.schools');

// 13. الإدارات
Route::get('/sitemap-administrations-{page}.xml', [SitemapController::class, 'administrations'])
    ->where('page', '[0-9]+')
    ->name('sitemap.administrations');

// 14. مسح الكاش
Route::get('/sitemap/clear-cache', [SitemapController::class, 'clearCache'])->middleware('auth');

// Unified Country Routes (For all other countries: Libya, Iraq, Sudan, etc.)
// Examples: /iraq/prep, /libya/prep, /palestine/secondary
Route::prefix('{country:slug}')->name('country.')->middleware(['cache.response'])->group(function () {
    Route::get('/', [CountryResultsController::class, 'index'])->name('index');
    Route::get('/{slug}/student/{seat_number}', [CountryResultsController::class, 'studentResult'])->name('student');
    Route::get('/{slug}', [CountryResultsController::class, 'examType'])->name('exam')
        ->where('slug', '[a-z0-9\-\/]+'); // Allows slugs like prep/6th, prep/9th, secondary
});
