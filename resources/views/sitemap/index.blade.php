@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg mb-4">
            <i class="fa-solid fa-sitemap text-white text-4xl"></i>
        </div>
        <h1 class="text-3xl md:text-4xl font-black text-gray-800 mb-3">خريطة الموقع</h1>
        <p class="text-gray-600 text-lg max-w-2xl mx-auto">تصفح جميع صفحات موقع نتيجتي للوصول السريع إلى نتائج الامتحانات في الدول العربية</p>
    </div>

    <div class="max-w-6xl mx-auto">
        
        <!-- ===== القسم 1: الصفحات الرئيسية ===== -->
        <section class="mb-10">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-home text-white"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">الصفحات الرئيسية</h2>
            </div>
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <ul class="grid md:grid-cols-3 gap-4">
                    <li>
                        <a href="{{ url('/') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 transition-colors group">
                            <i class="fa-solid fa-house text-blue-500 group-hover:text-blue-600"></i>
                            <span class="text-gray-700 group-hover:text-blue-600 font-medium">الصفحة الرئيسية</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/certificate') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 transition-colors group">
                            <i class="fa-solid fa-certificate text-blue-500 group-hover:text-blue-600"></i>
                            <span class="text-gray-700 group-hover:text-blue-600 font-medium">شهادة التقدير</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/contact') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 transition-colors group">
                            <i class="fa-solid fa-envelope text-blue-500 group-hover:text-blue-600"></i>
                            <span class="text-gray-700 group-hover:text-blue-600 font-medium">اتصل بنا</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/privacy') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 transition-colors group">
                            <i class="fa-solid fa-shield-halved text-blue-500 group-hover:text-blue-600"></i>
                            <span class="text-gray-700 group-hover:text-blue-600 font-medium">سياسة الخصوصية</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/terms') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 transition-colors group">
                            <i class="fa-solid fa-file-contract text-blue-500 group-hover:text-blue-600"></i>
                            <span class="text-gray-700 group-hover:text-blue-600 font-medium">شروط الاستخدام</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/sitemap') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 transition-colors group">
                            <i class="fa-solid fa-sitemap text-blue-500 group-hover:text-blue-600"></i>
                            <span class="text-gray-700 group-hover:text-blue-600 font-medium">خريطة الموقع</span>
                        </a>
                    </li>
                </ul>
            </div>
        </section>

        <!-- ===== القسم 2: نتائج مصر ===== -->
        @php
            $egypt = $countries->firstWhere('code', 'EG');
        @endphp
        @if($egypt)
        <section class="mb-10">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                    <span class="text-xl">🇪🇬</span>
                </div>
                <h2 class="text-xl font-bold text-gray-800">نتائج الامتحانات في مصر</h2>
                <span class="bg-emerald-100 text-emerald-700 text-sm px-3 py-1 rounded-full font-medium">{{ $egypt->examTypes->count() }} شهادة</span>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <!-- صفحة مصر الرئيسية -->
                <div class="p-4 bg-gradient-to-l from-emerald-50 to-white border-b border-gray-100">
                    <a href="{{ route('egypt.index') }}" class="flex items-center gap-3 font-bold text-emerald-700 hover:text-emerald-800">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>جميع نتائج مصر</span>
                    </a>
                </div>
                
                <div class="p-6">
                    <!-- الشهادات الرئيسية -->
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        @foreach($egypt->examTypes as $examType)
                        <a href="{{ url('/egypt/' . $examType->slug) }}" 
                           class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50 transition-all group">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-xl flex items-center justify-center group-hover:from-emerald-200 group-hover:to-emerald-100">
                                <i class="fa-solid fa-graduation-cap text-emerald-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 group-hover:text-emerald-700">{{ $examType->name_ar }}</h3>
                                <span class="text-sm text-gray-500">نتائج {{ $examType->name_ar }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    
                    <!-- محافظات مصر -->
                    @if($egypt->governorates->count() > 0)
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="font-bold text-gray-700 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-map-marked-alt text-gray-400"></i>
                            نتائج الشهادة الإعدادية حسب المحافظات
                            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">{{ $egypt->governorates->count() }} محافظة</span>
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2">
                            @foreach($egypt->governorates as $gov)
                            <a href="{{ url('/egypt/preparatory/' . $gov->slug) }}" 
                               class="flex items-center gap-2 p-2 rounded-lg text-sm text-gray-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                <i class="fa-solid fa-location-dot text-xs text-gray-400"></i>
                                {{ $gov->name_ar }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </section>
        @endif

        <!-- ===== القسم 3: الدول الأخرى ===== -->
        @php
            $otherCountries = $countries->where('code', '!=', 'EG');
        @endphp
        @if($otherCountries->count() > 0)
        <section class="mb-10">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-globe-africa text-white"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">نتائج الدول العربية الأخرى</h2>
                <span class="bg-amber-100 text-amber-700 text-sm px-3 py-1 rounded-full font-medium">{{ $otherCountries->count() }} دولة</span>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($otherCountries as $country)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow">
                    <a href="{{ route('country.index', $country) }}" 
                       class="block p-5 bg-gradient-to-l from-amber-50 to-white border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            @if($country->flag_emoji)
                            <span class="text-2xl">{{ $country->flag_emoji }}</span>
                            @else
                            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-flag text-amber-600"></i>
                            </div>
                            @endif
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $country->name_ar }}</h3>
                                <span class="text-sm text-gray-500">{{ $country->examTypes->count() }} شهادة</span>
                            </div>
                        </div>
                    </a>
                    
                    @if($country->examTypes->count() > 0)
                    <div class="p-4">
                        <ul class="space-y-2">
                            @foreach($country->examTypes->take(5) as $examType)
                            <li>
                                <a href="{{ route('country.exam', [$country, $examType->slug]) }}" 
                                   class="flex items-center gap-2 text-sm text-gray-600 hover:text-amber-600 transition-colors">
                                    <i class="fa-solid fa-chevron-left text-xs text-gray-300"></i>
                                    {{ $examType->name_ar }}
                                </a>
                            </li>
                            @endforeach
                            @if($country->examTypes->count() > 5)
                            <li class="pt-1">
                                <a href="{{ route('country.index', $country) }}" class="text-sm text-amber-600 hover:text-amber-700 font-medium">
                                    عرض الكل ({{ $country->examTypes->count() }})
                                    <i class="fa-solid fa-arrow-left mr-1 text-xs"></i>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- ===== القسم 4: إحصائيات الموقع ===== -->
        <section class="mb-10">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-chart-pie text-white"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">إحصائيات الموقع</h2>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 text-center text-white shadow-lg">
                    <div class="text-3xl font-black mb-1">{{ $stats['countries'] ?? 0 }}</div>
                    <div class="text-blue-100 text-sm font-medium">دولة عربية</div>
                </div>
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-5 text-center text-white shadow-lg">
                    <div class="text-3xl font-black mb-1">{{ $stats['governorates'] ?? 0 }}</div>
                    <div class="text-emerald-100 text-sm font-medium">محافظة</div>
                </div>
                <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl p-5 text-center text-white shadow-lg">
                    <div class="text-3xl font-black mb-1">{{ $stats['exam_types'] ?? 0 }}</div>
                    <div class="text-amber-100 text-sm font-medium">نوع شهادة</div>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-5 text-center text-white shadow-lg">
                    <div class="text-3xl font-black mb-1">{{ number_format($stats['students'] ?? 0) }}</div>
                    <div class="text-purple-100 text-sm font-medium">نتيجة طالب</div>
                </div>
            </div>
        </section>

        <!-- ===== القسم 5: خرائط XML لمحركات البحث ===== -->
        <section class="mb-10">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-gradient-to-br from-gray-600 to-gray-700 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-code text-white"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">خرائط XML لمحركات البحث</h2>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <!-- Main Sitemap Index -->
                <div class="p-5 bg-gradient-to-l from-orange-50 to-white border-b border-gray-100">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-file-code text-orange-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">Sitemap Index</h3>
                                <p class="text-sm text-gray-500">الفهرس الرئيسي لجميع الخرائط</p>
                            </div>
                        </div>
                        <a href="{{ url('/sitemap.xml') }}" 
                           target="_blank"
                           class="inline-flex items-center gap-2 bg-orange-100 hover:bg-orange-200 text-orange-700 px-4 py-2 rounded-lg font-medium text-sm transition-colors">
                            <i class="fa-solid fa-external-link"></i>
                            sitemap.xml
                        </a>
                    </div>
                </div>
                
                <!-- Sitemaps List -->
                <div class="p-6">
                    <div class="grid md:grid-cols-2 gap-3">
                        @if(isset($sitemaps))
                        @foreach($sitemaps as $sitemap)
                        <a href="{{ $sitemap['url'] }}" 
                           target="_blank"
                           class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50 transition-all group">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-blue-100">
                                <i class="fa-solid {{ $sitemap['icon'] }} text-gray-500 group-hover:text-blue-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-700 group-hover:text-blue-700 truncate">{{ $sitemap['name'] }}</div>
                                <div class="text-xs text-gray-400">{{ number_format($sitemap['count']) }} رابط</div>
                            </div>
                            <i class="fa-solid fa-arrow-up-right-from-square text-gray-300 group-hover:text-blue-500"></i>
                        </a>
                        @endforeach
                        @endif
                    </div>
                </div>
                
                <!-- Info -->
            </div>
        </section>

        <!-- كلمات البحث الشائعة -->
        @include('partials.popular-keywords')
    </div>
</div>

{{-- Structured Data for SEO --}}
@push('scripts')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "خريطة الموقع - نتيجتي",
    "description": "خريطة موقع نتيجتي الشاملة - تصفح جميع صفحات نتائج الامتحانات في الدول العربية",
    "url": "{{ url('/sitemap') }}",
    "mainEntity": {
        "@type": "ItemList",
        "numberOfItems": {{ ($stats['countries'] ?? 0) + ($stats['exam_types'] ?? 0) }},
        "itemListElement": [
            @foreach($countries->take(5) as $index => $country)
            {
                "@type": "ListItem",
                "position": {{ $index + 1 }},
                "name": "نتائج {{ $country->name_ar }}",
                "url": "{{ route('country.index', $country) }}"
            }@if(!$loop->last),@endif
            @endforeach
        ]
    }
}
</script>
@endpush
@endsection
