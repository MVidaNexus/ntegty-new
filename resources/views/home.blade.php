@extends('layouts.layout')

@section('structured_data')
{!! $structuredData !!}
@endsection

@section('content')
<div class="container mx-auto px-4 py-12">
    <!-- Hero Section -->
    @php
        $heroTitle = $settings['hero_title'] ?? 'نتيجتي';
        $heroSubtitle = $settings['hero_subtitle'] ?? 'بوابتك الرسمية لنتائج الامتحانات في الوطن العربي';
    @endphp
    <div class="text-center mb-12 mt-4">
        <h1 class="text-2xl md:text-4xl font-black text-slate-800 mb-6 leading-relaxed flex flex-col md:flex-row items-center justify-center gap-2 md:gap-3">
            <span class="text-emerald-600">{{ $heroTitle }}</span>
            <span class="hidden md:inline text-slate-300">|</span>
            <span class="text-xl md:text-3xl text-slate-700">{{ $heroSubtitle }}</span>
        </h1>
    </div>

    {{-- Ad: Header Bottom --}}
    <x-ad-unit slug="home-header-bottom" />

    <!-- Social Media Buttons -->
    @php
        $whatsappUrl = \App\Models\Setting::get('homepage_whatsapp_url');
        $whatsappLabel = \App\Models\Setting::get('homepage_whatsapp_label', 'جروب واتساب');
        $whatsappActive = \App\Models\Setting::get('homepage_whatsapp_active', '1');
        
        $telegramUrl = \App\Models\Setting::get('homepage_telegram_url');
        $telegramLabel = \App\Models\Setting::get('homepage_telegram_label', 'قناة تليجرام');
        $telegramActive = \App\Models\Setting::get('homepage_telegram_active', '1');
        
        $facebookPageUrl = \App\Models\Setting::get('homepage_facebook_url');
        $facebookPageLabel = \App\Models\Setting::get('homepage_facebook_label', 'صفحة فيسبوك');
        $facebookPageActive = \App\Models\Setting::get('homepage_facebook_active', '1');
        
        $facebookGroupUrl = \App\Models\Setting::get('homepage_facebook_group_url');
        $facebookGroupLabel = \App\Models\Setting::get('homepage_facebook_group_label', 'جروب فيسبوك');
        $facebookGroupActive = \App\Models\Setting::get('homepage_facebook_group_active', '1');
        
        $hasAnySocial = ($whatsappActive && $whatsappUrl) || ($telegramActive && $telegramUrl) || 
                        ($facebookPageActive && $facebookPageUrl) || ($facebookGroupActive && $facebookGroupUrl);
    @endphp

    @if($hasAnySocial)
    <div class="flex flex-wrap justify-center gap-3 mb-12">
        @if($whatsappActive && $whatsappUrl)
        <a href="{{ $whatsappUrl }}" target="_blank" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-full font-medium transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5">
            <i class="fab fa-whatsapp text-lg"></i>
            <span>{{ $whatsappLabel }}</span>
        </a>
        @endif
        
        @if($telegramActive && $telegramUrl)
        <a href="{{ $telegramUrl }}" target="_blank"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-full font-medium transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5">
            <i class="fab fa-telegram text-lg"></i>
            <span>{{ $telegramLabel }}</span>
        </a>
        @endif
        
        @if($facebookPageActive && $facebookPageUrl)
        <a href="{{ $facebookPageUrl }}" target="_blank"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full font-medium transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5">
            <i class="fab fa-facebook text-lg"></i>
            <span>{{ $facebookPageLabel }}</span>
        </a>
        @endif
        
        @if($facebookGroupActive && $facebookGroupUrl)
        <a href="{{ $facebookGroupUrl }}" target="_blank"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full font-medium transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5">
            <i class="fab fa-facebook text-lg"></i>
            <span>{{ $facebookGroupLabel }}</span>
        </a>
        @endif
    </div>
    @endif

    {{-- Ad: Before Grid --}}
    <x-ad-unit slug="home-before-search" />

    <!-- Country Selection - Card Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5 md:gap-6">
        @foreach($countries as $country)
            @php
                // Country code for flag images (ISO 3166-1 alpha-2)
                $flagCodes = [
                    'egypt' => 'eg',
                    'iraq' => 'iq',
                    'syria' => 'sy',
                    'libya' => 'ly',
                    'palestine' => 'ps',
                    'jordan' => 'jo',
                    'tunisia' => 'tn',
                    'algeria' => 'dz',
                    'lebanon' => 'lb',
                    'morocco' => 'ma',
                    'sudan' => 'sd',
                    'yemen' => 'ye',
                    'kuwait' => 'kw',
                    'saudi' => 'sa',
                    'uae' => 'ae',
                    'bahrain' => 'bh',
                    'qatar' => 'qa',
                    'oman' => 'om',
                ];
                $flagCode = $flagCodes[$country->slug] ?? 'un';
            @endphp
            <a href="{{ route('country.index', ['country' => $country->slug]) }}" 
               class="group relative bg-slate-50/80 rounded-2xl p-5 md:p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 overflow-hidden">
                
                <!-- Background Flag Watermark -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden">
                    <img 
                        src="https://flagcdn.com/w320/{{ $flagCode }}.png"
                        alt=""
                        class="w-full h-full object-cover opacity-[0.08] scale-150"
                    >
                </div>
                
                <!-- Flag Circle - Real Flag Image -->
                <div class="relative z-10 flex justify-center mb-4">
                    <div class="w-20 h-20 md:w-24 md:h-24 rounded-full shadow-lg border-4 border-white overflow-hidden group-hover:shadow-xl group-hover:scale-105 transition-all duration-300">
                        <img 
                            src="https://flagcdn.com/w160/{{ $flagCode }}.png" 
                            srcset="https://flagcdn.com/w320/{{ $flagCode }}.png 2x"
                            alt="علم {{ $country->name_ar }}"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                    </div>
                </div>
                
                <!-- Country Name -->
                <h2 class="relative z-10 text-lg md:text-xl font-bold text-slate-800 text-center mb-2 group-hover:text-emerald-600 transition-colors">
                    {{ $country->name_ar }}
                </h2>
                
                <!-- Certificates Count -->
                <div class="relative z-10 flex items-center justify-center gap-1.5 text-sm text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ $country->examTypes->count() }} شهادات متاحة</span>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Ad: After Grid --}}
    <x-ad-unit slug="home-after-search" />

    <!-- Features Section -->
    <div class="mt-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8 max-w-7xl mx-auto px-4">
            <!-- Search Card -->
            <div class="group relative bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden text-center">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-6 -mt-6 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <div class="mb-4 text-blue-600 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-magnifying-glass text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">بحث سهل</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        ابحث بالاسم أو رقم الجلوس بسهولة وسرعة فائقة
                    </p>
                </div>
            </div>

            <!-- Mobile Card -->
            <div class="group relative bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden text-center">
                <div class="absolute top-0 right-0 w-24 h-24 bg-purple-50 rounded-bl-full -mr-6 -mt-6 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <div class="mb-4 text-purple-600 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-mobile-screen text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">متوافق مع الجوال</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        استعرض النتائج بسلاسة من أي جهاز محمول
                    </p>
                </div>
            </div>

            <!-- Printing Card -->
            <div class="group relative bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden text-center">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-6 -mt-6 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <div class="mb-4 text-emerald-600 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-print text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">طباعة احترافية</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        اطبع كشف درجاتك بتصميم أنيق واحترافي
                    </p>
                </div>
            </div>
            
            <!-- Certificate Generator Card -->
            <a href="{{ route('certificate.index') }}" class="group relative text-center p-6 bg-gradient-to-br from-amber-400 via-orange-400 to-rose-500 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 hover:scale-105 overflow-hidden border-2 border-amber-300/50">
                <div class="absolute inset-0 bg-gradient-to-tr from-yellow-300/30 via-transparent to-pink-300/30 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                
                <div class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-black px-3 py-1 rounded-full shadow-lg transform rotate-12 group-hover:rotate-0 transition-transform duration-300">
                    جديد <i class="fa-solid fa-star text-[10px] mr-1 text-yellow-300"></i>
                </div>
                
                <div class="relative z-10">
                    <div class="mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-award text-5xl text-white drop-shadow-lg"></i>
                    </div>
                    
                    <h3 class="text-xl font-black mb-2 text-white drop-shadow-md">
                        شهادة تقدير
                    </h3>
                    <p class="text-white/90 font-semibold text-sm mb-3">
                        اصنع شهادتك بنفسك في ثوانٍ! <i class="fa-solid fa-wand-magic-sparkles text-yellow-300"></i>
                    </p>
                    <div class="flex items-center justify-center gap-2 text-white font-bold text-xs opacity-90 group-hover:opacity-100">
                        <span>ابدأ الآن</span>
                        <i class="fa-solid fa-arrow-left animate-bounce"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Ad: Footer Top --}}
    <x-ad-unit slug="home-footer-top" />
    
    <!-- About Section - من الإعدادات -->
    @php
        $aboutActive = ($settings['about_section_active'] ?? '1') === '1';
        $aboutTitle = $settings['about_section_title'] ?? 'عن نتيجتي';
        $aboutContent = $settings['about_section_content'] ?? 'موقع نتيجتي هو المنصة العربية الأكبر والأحدث المخصصة لعرض نتائج الشهادات العامة والأزهرية والدبلومات الفنية فور اعتمادها رسمياً. ننفرد بتغطية شاملة وحصرية لنتائج الامتحانات في مصر، العراق، ليبيا، فلسطين وغيرها من الدول. لا نكتفي بعرض النتيجة فحسب، بل نقدم أدوات ذكية تتيح لك البحث بالاسم أو رقم الجلوس، تصميم شهادات تقدير احترافية، وطباعة كشف الدرجات بضغطة زر. هدفنا هو توفير تجربة مستخدم سهلة، سريعة، وموثوقة لجميع الطلاب وأولياء الأمور.';
    @endphp
    
    @if($aboutActive && $aboutContent)
    <div class="mt-16 mb-8">
        <div class="max-w-4xl mx-auto bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl p-6 md:p-10 border-2 border-emerald-200 shadow-lg relative overflow-hidden">
            <!-- الديكور -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-200/30 rounded-full -mr-10 -mt-10"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-teal-200/30 rounded-full -ml-8 -mb-8"></div>
            
            <div class="relative z-10">
                <!-- العنوان -->
                <div class="flex items-center justify-center gap-3 mb-6">
                    <div class="h-px flex-1 bg-gradient-to-l from-emerald-400 to-transparent"></div>
                    <h2 class="text-xl md:text-2xl font-black text-emerald-700 flex items-center gap-2">
                        <i class="fa-solid fa-info-circle"></i>
                        {{ $aboutTitle }}
                    </h2>
                    <div class="h-px flex-1 bg-gradient-to-r from-emerald-400 to-transparent"></div>
                </div>
                
                <!-- المحتوى -->
                <p class="text-base md:text-lg text-slate-700 leading-loose text-justify md:text-center font-medium">
                    {!! nl2br(e($aboutContent)) !!}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- كلمات البحث الشائعة -->
    <div class="max-w-4xl mx-auto">
        @include('partials.popular-keywords')
    </div>
</div>
@endsection
