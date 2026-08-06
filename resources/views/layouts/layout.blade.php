@php
    // Resolve country dynamically for SEO Geo-targeting
    $currentCountry = null;
    if (isset($country)) {
        $currentCountry = $country;
    } elseif (isset($examType) && $examType->country) {
        $currentCountry = $examType->country;
    } elseif (isset($governorate) && $governorate->country) {
        $currentCountry = $governorate->country;
    } elseif (isset($result) && $result->examType && $result->examType->country) {
        $currentCountry = $result->examType->country;
    }

    $geoRegion = 'AR'; 
    $geoPosition = '23.8859;45.0792'; 
    $geoPlacename = 'الوطن العربي';
    $ogLocale = 'ar_AR';

    if ($currentCountry) {
        $code = strtoupper($currentCountry->code);
        $geoRegion = $code;
        $ogLocale = 'ar_' . $code;

        switch ($code) {
            case 'EG':
                $geoPosition = '30.0444;31.2357';
                $geoPlacename = 'Egypt';
                break;
            case 'IQ':
                $geoPosition = '33.3152;44.3661';
                $geoPlacename = 'Iraq';
                break;
            case 'LY':
                $geoPosition = '32.8872;13.1913';
                $geoPlacename = 'Libya';
                break;
            case 'SD':
                $geoPosition = '15.5007;32.5599';
                $geoPlacename = 'Sudan';
                break;
            case 'PS':
                $geoPosition = '31.9522;35.2332';
                $geoPlacename = 'Palestine';
                break;
            case 'YE':
                $geoPosition = '15.3694;44.1910';
                $geoPlacename = 'Yemen';
                break;
            case 'JO':
                $geoPosition = '31.9454;35.9284';
                $geoPlacename = 'Jordan';
                break;
            case 'SY':
                $geoPosition = '33.5138;36.2765';
                $geoPlacename = 'Syria';
                break;
        }
    }

    // Dynamic publication and modification dates for SEO
    $datePublished = now()->toIso8601String();
    $dateModified = now()->toIso8601String();

    if (isset($result)) {
        $datePublished = $result->created_at ? $result->created_at->toIso8601String() : $datePublished;
        $dateModified = $result->updated_at ? $result->updated_at->toIso8601String() : $dateModified;
    } elseif (isset($examType)) {
        $datePublished = $examType->created_at ? $examType->created_at->toIso8601String() : $datePublished;
        $dateModified = $examType->updated_at ? $examType->updated_at->toIso8601String() : $dateModified;
    } elseif (isset($country)) {
        $datePublished = $country->created_at ? $country->created_at->toIso8601String() : $datePublished;
        $dateModified = $country->updated_at ? $country->updated_at->toIso8601String() : $dateModified;
    } elseif (isset($governorate)) {
        $datePublished = $governorate->created_at ? $governorate->created_at->toIso8601String() : $datePublished;
        $dateModified = $governorate->updated_at ? $governorate->updated_at->toIso8601String() : $dateModified;
    }
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl" x-data="{ darkMode: $persist(false) }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <script>
        try {
            const stored = localStorage.getItem('_x_darkMode');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (stored === 'true' || stored === '"true"' || (stored === null && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        } catch (e) {}
    </script>
    
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#10b981">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192x192.png') }}">
    
    <!-- SEO Meta Tags -->
    @hasSection('meta')
        @yield('meta')
    @else
        @php
            $seoTitle = $settings['seo_title'] ?? 'نتائج الطلاب في الوطن العربي | نتيجتي';
            $seoDescription = $settings['seo_description'] ?? 'منصة نتيجتي لعرض نتائج الشهادات الإعدادية والثانوية والدبلومات في الوطن العربي';
            $seoKeywords = $settings['seo_keywords'] ?? 'نتائج, امتحانات, شهادة إعدادية, شهادة ثانوية, دبلومات, مصر, الوطن العربي';
        @endphp
        <title>{{ $meta['title'] ?? $seoTitle }}</title>
        <meta name="description" content="{{ $meta['description'] ?? $seoDescription }}">
        <meta name="keywords" content="{{ $meta['keywords'] ?? $seoKeywords }}">
    @endif
    <meta name="robots" content="{{ $meta['robots'] ?? 'index, follow' }}">
    <link rel="canonical" href="{{ $meta['canonical'] ?? url()->current() }}">
    
    <!-- Publication & Modification Dates SEO -->
    <meta name="publish-date" content="{{ $datePublished }}" />
    <meta name="pubdate" content="{{ $datePublished }}" />
    <meta name="last-modified" content="{{ $dateModified }}" />
    <meta property="article:published_time" content="{{ $datePublished }}" />
    <meta property="article:modified_time" content="{{ $dateModified }}" />
    
    <link rel="icon" type="image/png" href="{{ isset($settings['favicon']) ? asset('uploads/' . $settings['favicon']) : asset('favicon.ico') }}">
    <!-- Geo-Targeting & Regional SEO -->
    <meta name="geo.region" content="{{ $geoRegion }}" />
    <meta name="geo.position" content="{{ $geoPosition }}" />
    <meta name="ICBM" content="{{ $geoPosition }}" />
    <meta name="geo.placename" content="{{ $geoPlacename }}" />
    <meta name="content-language" content="{{ strtolower($geoRegion) === 'ar' ? 'ar' : 'ar-' . strtolower($geoRegion) }}" />
    
    <!-- Dynamic Hreflang Tags -->
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}" />
    <link rel="alternate" hreflang="ar" href="{{ url()->current() }}" />
    @if($currentCountry)
    <link rel="alternate" hreflang="ar-{{ strtolower($currentCountry->code) }}" href="{{ url()->current() }}" />
    @endif
    
    <!-- Open Graph -->
    <meta property="og:site_name" content="{{ $settings['site_name'] ?? 'نتيجتي' }}">
    <meta property="og:title" content="{{ $meta['og_title'] ?? $meta['title'] ?? 'نتيجتي' }}">
    <meta property="og:description" content="{{ $meta['og_description'] ?? $meta['description'] ?? '' }}">
    <meta property="og:image" content="{{ $meta['og_image'] ?? asset('images/og-default.png') }}">
    <meta property="og:image:width" content="{{ $meta['og_image_width'] ?? 1200 }}">
    <meta property="og:image:height" content="{{ $meta['og_image_height'] ?? 630 }}">
    <meta property="og:image:alt" content="نتيجتي - بوابة النتائج التعليمية">
    <meta property="og:type" content="{{ $meta['og_type'] ?? 'website' }}">
    <meta property="og:url" content="{{ $meta['og_url'] ?? url()->current() }}">
    <meta property="og:locale" content="{{ $ogLocale }}">
    @if(!empty($settings['fb_app_id']))
    <meta property="fb:app_id" content="{{ $settings['fb_app_id'] }}">
    @endif
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="{{ $meta['twitter_card'] ?? 'summary_large_image' }}">
    <meta name="twitter:site" content="{{ $meta['twitter_site'] ?? '@ntegty' }}">
    <meta name="twitter:creator" content="{{ $meta['twitter_creator'] ?? '@ntegty' }}">
    <meta name="twitter:title" content="{{ $meta['twitter_title'] ?? $meta['title'] ?? '' }}">
    <meta name="twitter:description" content="{{ $meta['twitter_description'] ?? $meta['description'] ?? '' }}">
    <meta name="twitter:image" content="{{ $meta['twitter_image'] ?? asset('images/og-default.png') }}">
    <meta name="twitter:image:alt" content="نتيجتي - بوابة النتائج التعليمية">
    
    <!-- Additional Meta -->
    <meta name="theme-color" content="#1e3a8a">
    <meta name="apple-mobile-web-app-title" content="نتيجتي">
    <meta name="application-name" content="نتيجتي">
    
    <!-- Fonts - Preconnect + Preload for better CWV -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Tajawal:wght@400;500;700;900&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Compiled CSS/JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.13.3/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    
    <style>
        body { font-family: 'Tajawal', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Rich Content Prose Styles - للمحتوى من Rich Editor */
        .prose { color: #374151; line-height: 1.8; }
        .prose h1 { font-size: 2rem; font-weight: 800; color: #1f2937; margin-top: 1.5rem; margin-bottom: 1rem; }
        .prose h2 { font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-top: 1.5rem; margin-bottom: 0.75rem; border-right: 4px solid #10b981; padding-right: 1rem; padding-top: 0.25rem; padding-bottom: 0.25rem; }
        .prose h3 { font-size: 1.25rem; font-weight: 700; color: #047857; margin-top: 1.25rem; margin-bottom: 0.5rem; }
        .prose h4 { font-size: 1.125rem; font-weight: 600; color: #1f2937; margin-top: 1rem; margin-bottom: 0.5rem; }
        .prose p { margin-bottom: 1rem; font-size: 1rem; line-height: 1.8; }
        .prose ul, .prose ol { margin: 1rem 0; padding-right: 1.5rem; }
        .prose ul { list-style-type: disc; }
        .prose ol { list-style-type: decimal; }
        .prose li { margin-bottom: 0.5rem; font-size: 1rem; line-height: 1.7; }
        .prose a { color: #059669; text-decoration: underline; }
        .prose a:hover { color: #047857; }
        .prose strong { font-weight: 700; color: #111827; }
        .prose em { font-style: italic; }
        .prose blockquote { border-right: 4px solid #d1d5db; padding-right: 1rem; margin: 1rem 0; color: #6b7280; font-style: italic; }
        .prose code { background-color: #f3f4f6; padding: 0.125rem 0.25rem; border-radius: 0.25rem; font-size: 0.875rem; }
        .prose pre { background-color: #1f2937; color: #f9fafb; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin: 1rem 0; }
        .prose pre code { background: none; padding: 0; }
        .prose hr { border: none; border-top: 2px solid #e5e7eb; margin: 2rem 0; }
        .prose img { max-width: 100%; height: auto; border-radius: 0.5rem; margin: 1rem auto; display: block; }
        
        /* Table Styles for Rich Content */
        .prose table, .content-section table { 
            display: block;
            width: 100%; 
            border-collapse: collapse; 
            margin: 1.5rem 0; 
            font-size: 0.95rem; 
            border-radius: 0.5rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        @media (min-width: 768px) {
            .prose table, .content-section table {
                display: table;
            }
        }
        .prose table th, .prose table td,
        .content-section table th, .content-section table td { 
            border: 1px solid #e5e7eb; 
            padding: 0.75rem 1rem; 
            text-align: right; 
        }
        .prose table th, .content-section table th { 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
            color: white; 
            font-weight: 700; 
            font-size: 1rem;
        }
        .prose table thead, .content-section table thead {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .prose table thead th, .content-section table thead th {
            color: white;
            font-weight: 700;
            padding: 1rem;
        }
        .prose table tbody tr:nth-child(even), 
        .content-section table tbody tr:nth-child(even) { 
            background-color: #f9fafb; 
        }
        .prose table tbody tr:hover, 
        .content-section table tbody tr:hover { 
            background-color: #ecfdf5; 
        }
        .prose table tbody td, .content-section table tbody td {
            padding: 0.75rem 1rem;
        }
        
        /* Responsive adjustments */
        @media (min-width: 768px) {
            .prose h1 { font-size: 2.25rem; }
            .prose h2 { font-size: 1.75rem; }
            .prose h3 { font-size: 1.375rem; }
            .prose p, .prose li { font-size: 1.125rem; }
        }
        
        /* Print Styles - Global */
        @media print {
            .no-print,
            header,
            footer,
            .fixed,
            .sticky,
            nav.mb-4,
            .bg-slate-900,
            .bg-emerald-600,
            .ad-container,
            [class*="sticky-"],
            [class*="fixed-"] {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                width: 0 !important;
                overflow: hidden !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            body {
                margin: 0 !important;
                padding: 0 !important;
            }
            
            main {
                margin: 0 !important;
                padding: 0 !important;
            }
        }
        
        /* Unified site-wide dark mode overrides */
        .dark .bg-white {
            background-color: rgb(30, 41, 59) !important; /* bg-slate-800 */
            color: rgb(241, 245, 249) !important;
        }
        .dark .bg-gray-50 {
            background-color: rgb(15, 23, 42) !important; /* bg-slate-900 */
        }
        .dark .text-gray-800, .dark .text-slate-800 {
            color: rgb(241, 245, 249) !important;
        }
        .dark .text-gray-700, .dark .text-slate-700 {
            color: rgb(203, 213, 225) !important;
        }
        .dark .text-gray-600, .dark .text-slate-600 {
            color: rgb(148, 163, 184) !important;
        }
        .dark .text-gray-500, .dark .text-slate-500 {
            color: rgb(148, 163, 184) !important;
        }
        .dark .text-gray-900 {
            color: rgb(248, 250, 252) !important;
        }
        .dark .border-blue-100, .dark .border-gray-100, .dark .border-slate-100 {
            border-color: rgb(51, 65, 85) !important; /* border-slate-700 */
        }
        .dark .bg-blue-50 {
            background-color: rgba(30, 41, 59, 0.6) !important;
        }
        .dark .from-blue-50 {
            --tw-gradient-from: rgb(30, 41, 59) !important;
            --tw-gradient-to: rgb(15, 23, 42) !important;
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to) !important;
        }
        .dark .from-white {
            --tw-gradient-from: rgb(30, 41, 59) !important;
            --tw-gradient-to: rgb(15, 23, 42) !important;
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to) !important;
        }
        .dark .from-blue-500 {
            --tw-gradient-from: #3b82f6 !important;
        }
        .dark .to-blue-50 {
            --tw-gradient-to: rgb(15, 23, 42) !important;
        }
        .dark .border-gray-200, .dark .border-slate-200, .dark .border-slate-200\/60 {
            border-color: rgb(51, 65, 85) !important;
        }
        .dark .bg-emerald-50 {
            background-color: rgba(6, 78, 59, 0.4) !important;
            color: rgb(52, 211, 153) !important;
        }
        .dark .text-emerald-700 {
            color: rgb(52, 211, 153) !important;
        }
        .dark .bg-purple-50 {
            background-color: rgba(88, 28, 135, 0.4) !important;
            color: rgb(192, 132, 252) !important;
        }
        .dark .text-purple-700 {
            color: rgb(192, 132, 252) !important;
        }
        .dark .bg-cyan-50 {
            background-color: rgba(21, 94, 117, 0.4) !important;
            color: rgb(34, 211, 238) !important;
        }
        .dark .text-cyan-700 {
            color: rgb(34, 211, 238) !important;
        }
        .dark select, .dark input[type="text"], .dark input[type="number"], .dark select option {
            background-color: rgb(15, 23, 42) !important;
            border-color: rgb(51, 65, 85) !important;
            color: rgb(241, 245, 249) !important;
        }
        .dark .divide-gray-100 > * + * {
            border-color: rgb(51, 65, 85) !important;
        }
        /* Dynamic content and prose classes in dark mode */
        .dark .gov-content-body {
            color: rgb(203, 213, 225) !important;
        }
        .dark .gov-content-body h1, .dark .gov-content-body h2, .dark .gov-content-body h3, .dark .gov-content-body h4 {
            color: rgb(241, 245, 249) !important;
        }
        .dark .gov-content-body table th, .dark .gov-content-body table td {
            border-color: rgb(51, 65, 85) !important;
        }
        .dark .gov-content-body table tbody tr:nth-child(even) {
            background-color: rgb(30, 41, 59) !important;
        }
        .dark .gov-content-body table tbody tr:hover {
            background-color: rgb(15, 23, 42) !important;
        }
        .dark .prose {
            color: rgb(203, 213, 225) !important;
        }
        .dark .prose h2, .dark .prose h3, .dark .prose h4 {
            color: rgb(241, 245, 249) !important;
        }
    </style>
    
    {{-- Automated Schema Fallback Generator --}}
    @php
        if (!isset($structuredData)) {
            $crumbs = [['name' => 'الرئيسية', 'url' => url('/')]];
            if (isset($breadcrumbs)) {
                $crumbs = [];
                foreach ($breadcrumbs as $crumb) {
                    $crumbs[] = [
                        'name' => $crumb['name'],
                        'url' => $crumb['url'] ?? null
                    ];
                }
            }
            $schemaGraph = [
                '@context' => 'https://schema.org',
                '@graph' => [
                    \App\Services\SchemaService::organization(),
                    \App\Services\SchemaService::website(),
                    \App\Services\SchemaService::webPage(
                        $meta['title'] ?? $settings['seo_title'] ?? 'نتيجتي',
                        $meta['description'] ?? $settings['seo_description'] ?? '',
                        url()->current(),
                        $datePublished,
                        $dateModified
                    ),
                    \App\Services\SchemaService::breadcrumb($crumbs)
                ]
            ];
            $structuredData = json_encode($schemaGraph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    @endphp

    {{-- Structured Data / JSON-LD --}}
    @hasSection('structured_data')
    <script type="application/ld+json">
        @yield('structured_data')
    </script>
    @elseif(isset($structuredData))
    <script type="application/ld+json">
        {!! $structuredData !!}
    </script>
    @endif
    
    {{-- Google Analytics --}}
    @php $gaId = $settings['google_analytics_id'] ?? null; @endphp
    @if($gaId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $gaId }}');
    </script>
    @endif
    
    {{-- Meta Verification Tags --}}
    {!! $settings['meta_verification'] ?? '' !!}
    
    {{-- AdSense Script - New System --}}
    @php
        $adsenseEnabled = ($settings['adsense_enabled'] ?? '0') === '1';
        $publisherId = trim($settings['adsense_publisher_id'] ?? '');
        $loadScript = ($settings['load_adsense_script'] ?? '1') === '1';
        $autoAds = ($settings['adsense_auto_ads'] ?? '0') === '1';
        
        // Ensure publisher ID starts with ca-pub-
        if (!empty($publisherId) && !str_starts_with($publisherId, 'ca-pub-') && !str_starts_with($publisherId, 'pub-')) {
            $publisherId = 'ca-pub-' . $publisherId;
        }
    @endphp
    @if($adsenseEnabled && !empty($publisherId) && $loadScript)
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $publisherId }}"
         crossorigin="anonymous"></script>
    @if($autoAds)
    <script>
        window.adsbygoogle = window.adsbygoogle || [];
        window.adsbygoogle.push({
            google_ad_client: "{{ $publisherId }}",
            enable_page_level_ads: true
        });
    </script>
    @endif
    @endif
    
    {{-- Custom AdSense Script --}}
    {!! $settings['adsense_custom_script'] ?? '' !!}
    
    {{-- Custom Header Scripts --}}
    {!! $settings['header_scripts'] ?? '' !!}
    
    {{-- Ad Container CSS --}}
    <style>
        .ad-container { text-align: center; overflow: hidden; }
        .ad-container ins { margin: 0 auto; }
        @media (max-width: 639px) { .hidden-mobile { display: none !important; } }
        @media (min-width: 640px) { .hidden-desktop { display: none !important; } }
    </style>
    
    {{-- Custom CSS --}}
    @php $customCss = $settings['custom_css'] ?? null; @endphp
    @if($customCss)
    <style>{!! $customCss !!}</style>
    @endif

    {{-- Speculation Rules API for instant page loads --}}
    <script type="speculationrules">
    {
      "prerender": [{
        "source": "document",
        "where": {
          "and": [
            { "href_matches": "/*" },
            { "not": { "href_matches": ["/admin*", "/nova*", "/dashboard*", "/login*", "/logout*"] } }
          ]
        },
        "eagerness": "moderate"
      }]
    }
    </script>
</head>
<body class="bg-slate-50 text-slate-900 dark:bg-slate-900 dark:text-slate-100 flex flex-col min-h-screen transition-colors duration-300">
    
    <!-- Header -->
    <!-- Top Bar -->
    @php
        $topBarText = $settings['hero_badge'] ?? 'بوابة نتائج الامتحانات الرسمية';
    @endphp
    <div class="bg-slate-900 text-slate-300 text-xs py-2 no-print">
        <div class="w-full px-4 lg:px-8 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <span>{{ date('Y-m-d') }}</span>
                <span class="hidden md:inline">|</span>
                <span class="hidden md:inline">{{ $topBarText }}</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('contact') }}" class="hover:text-white transition">اتصل بنا</a>
            </div>
        </div>
    </div>

    <!-- Top Bar (Optional, for announcements) -->
    @php
        $headerAnnouncementActive = ($settings['header_announcement_active'] ?? '1') === '1';
        $headerAnnouncementText = $settings['header_announcement_text'] ?? 'حصرياً: نتائج الشهادات العامة فور اعتمادها! تابعونا لحظة بلحظة.';
    @endphp
    @if($headerAnnouncementActive && $headerAnnouncementText)
    <div class="bg-emerald-600 text-white py-2 text-center text-xs font-bold hidden sm:block no-print">
        <p><i class="fa-solid fa-bullhorn text-yellow-300 ml-2"></i> {{ $headerAnnouncementText }}</p>
    </div>
    @endif

    <!-- Main Header -->
    <header class="bg-white dark:bg-slate-800 shadow-lg sticky top-0 z-50 no-print border-b-4 border-emerald-600 transition-colors duration-300" x-data="{ mobileMenuOpen: false }">
        <div class="w-full px-4 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo Section -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 md:gap-4 group">
                    @if(isset($settings['logo']))
                        <img src="{{ asset('uploads/' . $settings['logo']) }}" alt="{{ $settings['site_name'] ?? 'نتيجتي' }}" class="h-10 md:h-14 w-auto object-contain transition-all">
                    @else
                        <div class="flex flex-col">
                            <span class="text-xl md:text-2xl font-black text-slate-800 leading-none group-hover:text-emerald-600 transition duration-300">
                                {{ $settings['site_name'] ?? 'نتيجتي' }}
                            </span>
                            <span class="text-[10px] md:text-xs text-slate-500 font-bold tracking-widest mt-1">
                                المنصة الرسمية للنتائج
                            </span>
                        </div>
                    @endif
                </a>


                
                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center gap-6">
                    <!-- Home -->
                    <a href="{{ route('home') }}" class="text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 font-extrabold text-sm transition duration-200">
                        الرئيسية
                    </a>

                    <!-- Blog -->
                    <a href="{{ route('blog.index') }}" class="text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 font-extrabold text-sm transition duration-200 flex items-center gap-1.5">
                        <i class="fa-regular fa-newspaper text-emerald-500"></i>
                        <span>الأخبار التعليمية</span>
                    </a>

                    <!-- Results Dropdown -->
                    <div class="relative" x-data="{ open: false, timeout: null }" 
                         @mouseenter="clearTimeout(timeout); open = true" 
                         @mouseleave="timeout = setTimeout(() => open = false, 250)">
                        <button class="flex items-center gap-1.5 text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 font-extrabold text-sm transition duration-200">
                            <i class="fa-solid fa-graduation-cap text-emerald-500"></i>
                            <span>نتائج الامتحانات</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             style="display: none;"
                             class="absolute top-full right-0 mt-0 pt-2 w-72 z-50">
                            <div class="bg-white dark:bg-slate-800 shadow-xl rounded-2xl py-2 border border-slate-100 dark:border-slate-700/80">
                                <a href="{{ route('egypt.preparatory') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-emerald-50 dark:hover:bg-slate-700 transition">
                                    <img src="https://flagcdn.com/w20/eg.png" class="w-5 h-auto rounded-sm" alt="Egypt">
                                    <span class="text-slate-700 dark:text-slate-200 font-bold text-xs">مصر - الشهادة الإعدادية</span>
                                </a>
                                <a href="{{ route('egypt.secondary') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-emerald-50 dark:hover:bg-slate-700 transition">
                                    <img src="https://flagcdn.com/w20/eg.png" class="w-5 h-auto rounded-sm" alt="Egypt">
                                    <span class="text-slate-700 dark:text-slate-200 font-bold text-xs">مصر - الثانوية العامة</span>
                                </a>
                                <a href="{{ route('egypt.diplomas.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-emerald-50 dark:hover:bg-slate-700 transition">
                                    <img src="https://flagcdn.com/w20/eg.png" class="w-5 h-auto rounded-sm" alt="Egypt">
                                    <span class="text-slate-700 dark:text-slate-200 font-bold text-xs">مصر - الدبلومات الفنية</span>
                                </a>
                                <div class="border-t border-slate-100 dark:border-slate-700 my-1.5"></div>
                                <a href="{{ route('country.exam', ['country' => 'iraq', 'slug' => 'prep']) }}" class="flex items-center gap-3 px-4 py-2 hover:bg-emerald-50 dark:hover:bg-slate-700 transition">
                                    <img src="https://flagcdn.com/w20/iq.png" class="w-5 h-auto rounded-sm" alt="Iraq">
                                    <span class="text-slate-700 dark:text-slate-200 font-bold text-xs">العراق - السادس الإعدادي</span>
                                </a>
                                <a href="{{ route('country.exam', ['country' => 'libya', 'slug' => 'prep']) }}" class="flex items-center gap-3 px-4 py-2 hover:bg-emerald-50 dark:hover:bg-slate-700 transition">
                                    <img src="https://flagcdn.com/w20/ly.png" class="w-5 h-auto rounded-sm" alt="Libya">
                                    <span class="text-slate-700 dark:text-slate-200 font-bold text-xs">ليبيا - الشهادة الإعدادية</span>
                                </a>
                                <a href="{{ route('country.exam', ['country' => 'palestine', 'slug' => 'secondary']) }}" class="flex items-center gap-3 px-4 py-2 hover:bg-emerald-50 dark:hover:bg-slate-700 transition">
                                    <img src="https://flagcdn.com/w20/ps.png" class="w-5 h-auto rounded-sm" alt="Palestine">
                                    <span class="text-slate-700 dark:text-slate-200 font-bold text-xs">فلسطين - التوجيهي</span>
                                </a>
                                <a href="{{ route('country.exam', ['country' => 'jordan', 'slug' => 'secondary']) }}" class="flex items-center gap-3 px-4 py-2 hover:bg-emerald-50 dark:hover:bg-slate-700 transition">
                                    <img src="https://flagcdn.com/w20/jo.png" class="w-5 h-auto rounded-sm" alt="Jordan">
                                    <span class="text-slate-700 dark:text-slate-200 font-bold text-xs">الأردن - التوجيهي</span>
                                </a>
                                <div class="border-t border-slate-100 dark:border-slate-700 my-1.5"></div>
                                <a href="{{ route('country.exam', ['country' => 'syria', 'slug' => 'baccalaureate']) }}" class="flex items-center gap-3 px-4 py-2 hover:bg-emerald-50 dark:hover:bg-slate-700 transition">
                                    <img src="https://flagcdn.com/w20/sy.png" class="w-5 h-auto rounded-sm" alt="Syria">
                                    <span class="text-slate-700 dark:text-slate-200 font-bold text-xs">سوريا - البكالوريا</span>
                                </a>
                                <a href="{{ route('country.exam', ['country' => 'tunisia', 'slug' => 'baccalaureate']) }}" class="flex items-center gap-3 px-4 py-2 hover:bg-emerald-50 dark:hover:bg-slate-700 transition">
                                    <img src="https://flagcdn.com/w20/tn.png" class="w-5 h-auto rounded-sm" alt="Tunisia">
                                    <span class="text-slate-700 dark:text-slate-200 font-bold text-xs">تونس - البكالوريا</span>
                                </a>
                                <a href="{{ route('country.exam', ['country' => 'algeria', 'slug' => 'baccalaureate']) }}" class="flex items-center gap-3 px-4 py-2 hover:bg-emerald-50 dark:hover:bg-slate-700 transition">
                                    <img src="https://flagcdn.com/w20/dz.png" class="w-5 h-auto rounded-sm" alt="Algeria">
                                    <span class="text-slate-700 dark:text-slate-200 font-bold text-xs">الجزائر - البكالوريا</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Certificate -->
                    <a href="{{ route('certificate.index') }}" class="text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 font-extrabold text-sm transition duration-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-trophy text-amber-500"></i>
                        <span>شهادة تقدير</span>
                    </a>

                    <!-- Contact -->
                    <a href="{{ route('contact') }}" class="text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 font-extrabold text-sm transition duration-200 flex items-center gap-1.5">
                        <i class="fa-regular fa-envelope text-blue-500"></i>
                        <span>اتصل بنا</span>
                    </a>
                </nav>

                <!-- Actions Container -->
                <div class="flex items-center gap-3">
                    <!-- Install App PWA Button (Hidden by default, shown via JS if supported) -->
                    <button id="pwa-install-btn" class="hidden text-xs bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 px-3 py-1.5 rounded-full font-bold hover:bg-emerald-200 transition">
                        <i class="fa-solid fa-download ml-1"></i> تثبيت التطبيق
                    </button>
                    
                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" class="text-slate-600 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 p-2 rounded-full focus:outline-none transition">
                        <i class="fa-solid text-xl" :class="darkMode ? 'fa-sun text-yellow-400' : 'fa-moon'"></i>
                    </button>
                    
                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-slate-600 hover:text-emerald-600 dark:text-slate-300">
                        <svg x-show="!mobileMenuOpen" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                        <svg x-show="mobileMenuOpen" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 style="display: none;"
                 class="lg:hidden border-t border-gray-200 dark:border-slate-700 py-4 max-h-[80vh] overflow-y-auto">
                <nav class="space-y-2">
                    <!-- Home -->
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <span class="text-2xl w-5 flex justify-center text-emerald-500"><i class="fa-solid fa-house"></i></span>
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200">الرئيسية</div>
                        </div>
                    </a>

                    <!-- Blog -->
                    <a href="{{ route('blog.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <span class="text-2xl w-5 flex justify-center text-emerald-500"><i class="fa-regular fa-newspaper"></i></span>
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200">الأخبار التعليمية</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">تغطية شاملة لحظة بلحظة</div>
                        </div>
                    </a>

                    <!-- Egypt Links -->
                    <a href="{{ route('egypt.preparatory') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <img src="https://flagcdn.com/w20/eg.png" class="w-5 h-auto" alt="Egypt">
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200">الشهادة الإعدادية</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">جمهورية مصر العربية</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('egypt.secondary') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <img src="https://flagcdn.com/w20/eg.png" class="w-5 h-auto" alt="Egypt">
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200">الثانوية العامة</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">جمهورية مصر العربية</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('egypt.diplomas.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <img src="https://flagcdn.com/w20/eg.png" class="w-5 h-auto" alt="Egypt">
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200">الدبلومات الفنية</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">جمهورية مصر العربية</div>
                        </div>
                    </a>
                    
                    <!-- Iraq -->
                    <a href="{{ route('country.exam', ['country' => 'iraq', 'slug' => 'prep']) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <img src="https://flagcdn.com/w20/iq.png" class="w-5 h-auto" alt="Iraq">
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200">السادس الاعدادي</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">جمهورية العراق</div>
                        </div>
                    </a>
                    
                    <!-- Libya -->
                    <a href="{{ route('country.exam', ['country' => 'libya', 'slug' => 'prep']) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <img src="https://flagcdn.com/w20/ly.png" class="w-5 h-auto" alt="Libya">
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200">الشهادة الإعدادية</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">دولة ليبيا</div>
                        </div>
                    </a>
                    
                    <!-- Palestine -->
                    <a href="{{ route('country.exam', ['country' => 'palestine', 'slug' => 'secondary']) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <img src="https://flagcdn.com/w20/ps.png" class="w-5 h-auto" alt="Palestine">
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200">التوجيهي</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">دولة فلسطين</div>
                        </div>
                    </a>
                    
                    <!-- Jordan -->
                    <a href="{{ route('country.exam', ['country' => 'jordan', 'slug' => 'secondary']) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <img src="https://flagcdn.com/w20/jo.png" class="w-5 h-auto" alt="Jordan">
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200">التوجيهي</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">المملكة الأردنية</div>
                        </div>
                    </a>

                    <!-- Syria -->
                    <a href="{{ route('country.exam', ['country' => 'syria', 'slug' => 'baccalaureate']) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <img src="https://flagcdn.com/w20/sy.png" class="w-5 h-auto" alt="Syria">
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200">سوريا - البكالوريا</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">الجمهورية العربية السورية</div>
                        </div>
                    </a>

                    <!-- Tunisia -->
                    <a href="{{ route('country.exam', ['country' => 'tunisia', 'slug' => 'baccalaureate']) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <img src="https://flagcdn.com/w20/tn.png" class="w-5 h-auto" alt="Tunisia">
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200">تونس - البكالوريا</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">الجمهورية التونسية</div>
                        </div>
                    </a>

                    <!-- Algeria -->
                    <a href="{{ route('country.exam', ['country' => 'algeria', 'slug' => 'baccalaureate']) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <img src="https://flagcdn.com/w20/dz.png" class="w-5 h-auto" alt="Algeria">
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200">الجزائر - البكالوريا</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">الجمهورية الجزائرية</div>
                        </div>
                    </a>

                    <!-- Morocco -->
                    <a href="{{ route('country.exam', ['country' => 'morocco', 'slug' => 'baccalaureate']) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <img src="https://flagcdn.com/w20/ma.png" class="w-5 h-auto" alt="Morocco">
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200">المغرب - البكالوريا</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">المملكة المغربية</div>
                        </div>
                    </a>

                    <!-- Lebanon -->
                    <a href="{{ route('country.exam', ['country' => 'lebanon', 'slug' => 'baccalaureate']) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <img src="https://flagcdn.com/w20/lb.png" class="w-5 h-auto" alt="Lebanon">
                        <div>
                            <div class="text-sm font-bold text-gray-800 dark:text-slate-200">لبنان - البكالوريا</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400">الجمهورية اللبنانية</div>
                        </div>
                    </a>
                    
                    <!-- Divider -->
                    <div class="border-t border-gray-200 dark:border-slate-700 my-2"></div>
                    
                    <!-- Certificate -->
                    <a href="{{ route('certificate.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-amber-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <span class="text-2xl text-amber-500"><i class="fa-solid fa-trophy"></i></span>
                        <div>
                            <div class="text-sm font-bold text-amber-700 dark:text-amber-500">شهادة تقدير</div>
                            <div class="text-xs text-amber-600 dark:text-amber-400">اصنع شهادتك مجاناً</div>
                        </div>
                    </a>
                    
                    <!-- Contact -->
                    <a href="{{ route('contact') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 dark:hover:bg-slate-700 transition rounded-lg">
                        <span class="text-2xl text-blue-500"><i class="fa-solid fa-envelope"></i></span>
                        <div>
                            <div class="text-sm font-bold text-blue-700 dark:text-blue-500">اتصل بنا</div>
                            <div class="text-xs text-blue-600 dark:text-blue-400">نسعد بتواصلك</div>
                        </div>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Global Ad: Header Bottom -->
    <x-ad-unit slug="global-header-bottom" />

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Global Ad: Footer Top -->
    <x-ad-unit slug="global-footer-top" />

    <!-- Footer -->
    <footer class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white py-12 mt-auto no-print">
        <div class="w-full px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8 text-center md:text-right">
                <!-- About -->
                @php
                    $footerAboutTitle = $settings['footer_about_title'] ?? 'نتيجتي';
                    $footerAboutText = $settings['footer_about_text'] ?? 'منصة نتائج الطلاب الأولى في الوطن العربي. نوفر لك الوصول السريع والمجاني لنتائج الامتحانات في مصر والدول العربية.';
                    $footerCopyright = $settings['footer_copyright'] ?? 'نتيجتي - جميع الحقوق محفوظة';
                    $footerSlogan = $settings['footer_slogan'] ?? 'صنع بحب في الوطن العربي';
                    $footerFacebookUrl = $settings['footer_facebook_url'] ?? '';
                    $footerTelegramUrl = $settings['footer_telegram_url'] ?? '';
                    $footerWhatsappUrl = $settings['footer_whatsapp_url'] ?? '';
                @endphp
                <div>
                    <h3 class="text-2xl font-black mb-4 text-emerald-400">{{ $footerAboutTitle }}</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        {{ $footerAboutText }}
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-bold mb-4 text-emerald-400">روابط سريعة</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('home') }}" class="text-slate-400 hover:text-white transition flex items-center justify-center md:justify-start gap-2"><i class="fa-solid fa-house text-emerald-500/50"></i> الصفحة الرئيسية</a></li>
                        <li><a href="{{ route('certificate.index') }}" class="text-slate-400 hover:text-white transition flex items-center justify-center md:justify-start gap-2"><i class="fa-solid fa-certificate text-emerald-500/50"></i> شهادة تقدير</a></li>
                        <li><a href="{{ route('contact') }}" class="text-slate-400 hover:text-white transition flex items-center justify-center md:justify-start gap-2"><i class="fa-solid fa-envelope text-emerald-500/50"></i> اتصل بنا</a></li>
                    </ul>
                </div>
                
                <!-- Legal -->
                <div>
                    <h4 class="text-lg font-bold mb-4 text-blue-400">سياسات الموقع</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('privacy') }}" class="text-slate-400 hover:text-white transition flex items-center justify-center md:justify-start gap-2"><i class="fa-solid fa-shield-halved text-blue-500/50"></i> سياسة الخصوصية</a></li>
                        <li><a href="{{ route('terms') }}" class="text-slate-400 hover:text-white transition flex items-center justify-center md:justify-start gap-2"><i class="fa-solid fa-scale-balanced text-blue-500/50"></i> الشروط والأحكام</a></li>
                        <li><a href="{{ route('sitemap.html') }}" class="text-slate-400 hover:text-white transition flex items-center justify-center md:justify-start gap-2"><i class="fa-solid fa-sitemap text-blue-500/50"></i> خريطة الموقع</a></li>
                    </ul>
                </div>
                
                <!-- Social Media -->
                <div>
                    <h4 class="text-lg font-bold mb-4 text-purple-400">تابعنا</h4>
                    <div class="flex justify-center md:justify-start gap-4">
                        @if($footerFacebookUrl)
                        <a href="{{ $footerFacebookUrl }}" target="_blank" class="w-10 h-10 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center transition-all hover:scale-110 shadow-lg group">
                            <i class="fa-brands fa-facebook-f text-white text-lg group-hover:rotate-12 transition-transform"></i>
                        </a>
                        @endif
                        @if($footerTelegramUrl)
                        <a href="{{ $footerTelegramUrl }}" target="_blank" class="w-10 h-10 bg-sky-500 hover:bg-sky-600 rounded-full flex items-center justify-center transition-all hover:scale-110 shadow-lg group">
                            <i class="fa-brands fa-telegram text-white text-lg group-hover:-rotate-12 transition-transform"></i>
                        </a>
                        @endif
                        @if($footerWhatsappUrl)
                        <a href="{{ $footerWhatsappUrl }}" target="_blank" class="w-10 h-10 bg-green-500 hover:bg-green-600 rounded-full flex items-center justify-center transition-all hover:scale-110 shadow-lg group">
                            <i class="fa-brands fa-whatsapp text-white text-lg group-hover:rotate-12 transition-transform"></i>
                        </a>
                        @endif
                        @if(!$footerFacebookUrl && !$footerTelegramUrl && !$footerWhatsappUrl)
                        <p class="text-slate-500 text-sm">لم يتم إضافة روابط تواصل بعد</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="border-t border-slate-700/50 pt-6 text-center">
                <p class="text-slate-400 text-sm font-medium">&copy; {{ date('Y') }} {{ $footerCopyright }}</p>
                @if($footerSlogan)
                <p class="text-slate-500 text-xs mt-2 flex items-center justify-center gap-1">
                    {{ $footerSlogan }} <i class="fa-solid fa-heart text-red-500 animate-pulse"></i>
                </p>
                @endif
            </div>
        </div>
    </footer>

    <!-- Floating Social Buttons -->
    @php
        $examTypeId = isset($examType) ? $examType->id : null;
        $countryId = isset($country) ? $country->id : (isset($egypt) ? 1 : null);
        $socialLinks = \App\Models\SocialLink::getForContext($examTypeId, $countryId);
    @endphp

    @if($socialLinks->isNotEmpty())
    <div class="fixed bottom-6 right-6 z-50 no-print" x-data="{ expanded: false }">
        @if($socialLinks->count() > 1)
            <button @click="expanded = !expanded" 
                    class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110 relative group"
                    :class="expanded ? 'rotate-45' : ''" title="روابط التواصل">
                <span class="absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75 animate-ping" x-show="!expanded"></span>
                <i class="fa-solid fa-plus text-2xl md:text-3xl relative transition-transform duration-300"></i>
            </button>
            <div x-show="expanded" x-transition class="absolute bottom-full right-0 mb-3 flex flex-col-reverse gap-2">
                @foreach($socialLinks as $link)
                    @php $info = $link->getPlatformInfo(); @endphp
                    <a href="{{ $link->url }}" target="_blank" class="w-12 h-12 {{ $info['color'] }} text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110" title="{{ $link->getDisplayLabel() }}">
                        <i class="{{ $info['icon'] }} text-xl"></i>
                    </a>
                @endforeach
            </div>
        @else
            @php $link = $socialLinks->first(); $info = $link->getPlatformInfo(); @endphp
            <a href="{{ $link->url }}" target="_blank" class="w-14 h-14 md:w-16 md:h-16 {{ $info['color'] }} text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110 group animate-bounce-slow" title="{{ $link->getDisplayLabel() }}">
                <span class="absolute inline-flex h-full w-full rounded-full opacity-75 animate-ping" style="background: inherit;"></span>
                <i class="{{ $info['icon'] }} text-2xl md:text-3xl relative"></i>
            </a>
        @endif
    </div>
    @else
    @php
        $telegramUrl = 'https://t.me/ntegty';
        if(isset($settings['telegram_url'])) { $telegramUrl = $settings['telegram_url']; }
        if(isset($country) && !empty($country->telegram_url)) { $telegramUrl = $country->telegram_url; }
        elseif(isset($egypt) && !empty($egypt->telegram_url)) { $telegramUrl = $egypt->telegram_url; }
    @endphp
    <a href="{{ $telegramUrl }}" target="_blank" class="fixed bottom-6 right-6 z-50 flex items-center justify-center w-14 h-14 bg-sky-500 hover:bg-sky-600 text-white rounded-full shadow-lg hover:scale-110 transition-all duration-300 md:w-16 md:h-16 group no-print animate-bounce-slow" title="اشترك في قناة التيليجرام">
        <span class="absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75 animate-ping"></span>
        <i class="fa-brands fa-telegram text-2xl md:text-3xl relative"></i>
        <span class="absolute right-full mr-3 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">قناة التيليجرام</span>
    </a>
    @endif

    {{-- Sticky Bottom Ad --}}
    <x-sticky-ad />

    @stack('scripts')
    
    {{-- Custom Footer Scripts --}}
    {!! $settings['footer_scripts'] ?? '' !!}
    
    <script>
        // PWA Service Worker Registration & Install Logic
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(error => {
                    console.log('SW registration failed: ', error);
                });
            });
        }

        let deferredPrompt;
        const installBtn = document.getElementById('pwa-install-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;
            // Update UI to notify the user they can add to home screen
            if(installBtn) {
                installBtn.classList.remove('hidden');
            }
        });

        if(installBtn) {
            installBtn.addEventListener('click', (e) => {
                // hide our user interface that shows our A2HS button
                installBtn.classList.add('hidden');
                // Show the prompt
                if(deferredPrompt) {
                    deferredPrompt.prompt();
                    // Wait for the user to respond to the prompt
                    deferredPrompt.userChoice.then((choiceResult) => {
                        deferredPrompt = null;
                    });
                }
            });
        }
    </script>
</body>
</html>
