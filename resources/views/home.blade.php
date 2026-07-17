@extends('layouts.layout')

@section('structured_data')
{!! $structuredData !!}
@endsection

@section('content')
<div class="container mx-auto px-4 py-12">
    <!-- Hero Section -->
    @php
        $heroTitle = $settings['hero_title'] ?? 'نتيجتي';
        $heroSubtitle = $settings['hero_subtitle'] ?? 'المنصة الأولى لنتائج الشهادات العامة والأزهرية 2026 في الوطن العربي';
    @endphp
    <div class="text-center mb-12 mt-4">
        <h1 class="text-2xl md:text-4xl font-black text-slate-800 mb-6 leading-relaxed flex flex-col md:flex-row items-center justify-center gap-2 md:gap-3">
            <span class="text-emerald-600">{{ $heroTitle }}</span>
            <span class="hidden md:inline text-slate-300">|</span>
            <span class="text-xl md:text-3xl text-slate-700">{{ $heroSubtitle }}</span>
        </h1>
        <p class="max-w-2xl mx-auto text-slate-500 font-medium text-sm md:text-base px-4">
            تابع نتائج الشهادة الإعدادية، الثانوية العامة، والدبلومات الفنية لعام 2026 لحظة بلحظة. استعلام سريع برقم الجلوس والاسم مع تقارير تحليلية شاملة.
        </p>
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
                    <h3 class="text-xl font-bold text-slate-800 mb-2">بحث ذكي 2026</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        تقنيات بحث متطورة تتيح لك الوصول لنتيجتك برقم الجلوس أو الاسم الرباعي في ثوانٍ معدودة.
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

    <!-- Blog / Latest News Section -->
    @if(isset($latestPosts) && $latestPosts->count() > 0)
    <div class="mt-16 max-w-7xl mx-auto px-4 font-tajawal">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-10 pb-4 border-b border-slate-100">
            <div class="text-center sm:text-right space-y-1">
                <h2 class="text-2xl md:text-3xl font-black text-slate-800">
                    آخر الأخبار و <span class="text-blue-600">المستجدات التعليمية</span>
                </h2>
                <p class="text-sm text-slate-400 font-semibold">تغطية حصرية لحظة بلحظة لأهم القرارات والنتائج</p>
            </div>
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 bg-blue-50 text-blue-600 hover:bg-blue-100 font-black text-sm px-6 py-3 rounded-2xl transition-all shadow-sm">
                <span>تصفح المدونة بالكامل</span>
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($latestPosts as $post)
                <article class="group bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col h-full">
                    <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                        @if($post->image_path)
                            <img src="{{ asset($post->image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 text-white">
                                <i class="fa-solid fa-graduation-cap text-4xl opacity-30"></i>
                            </div>
                        @endif
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1.5 rounded-xl text-[10px] font-black text-white shadow-md {{ match($post->category) {
                                'results' => 'bg-blue-600',
                                'alternatives' => 'bg-emerald-600',
                                'capabilities' => 'bg-amber-600',
                                'grades' => 'bg-purple-600',
                                default => 'bg-slate-600'
                            } }}">
                                {{ $post->category_name_ar }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="text-[10px] text-slate-400 font-bold flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar-check text-blue-500"></i>
                                <span>{{ $post->published_at ? $post->published_at->format('Y-m-d') : $post->created_at->format('Y-m-d') }}</span>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-800 leading-snug group-hover:text-blue-600 transition-colors duration-200">
                                <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                            </h3>
                            <p class="text-xs text-slate-500 font-medium leading-relaxed line-clamp-3">
                                {{ $post->summary }}
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-slate-50">
                            <a href="{{ route('blog.show', $post) }}" class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 font-black text-xs group/btn transition-colors">
                                <span>اقرأ المزيد</span>
                                <i class="fa-solid fa-arrow-left text-[10px] group-hover/btn:-translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- About Section - من الإعدادات -->
    @php
        $aboutActive = ($settings['about_section_active'] ?? '1') === '1';
        $aboutTitle = $settings['about_section_title'] ?? 'لماذا نتيجتي؟';
        $aboutContent = $settings['about_section_content'] ?? 'موقع نتيجتي هو المصدر الأكثر موثوقية لنتائج الامتحانات في مصر والشرق الأوسط. نخدم أكثر من 10 مليون طالب سنوياً عبر تغطية شاملة لنتائج الشهادة الإعدادية والثانوية العامة. نتميز بالسرعة الفائقة والدقة المتناهية، حيث يتم تحديث قواعد البيانات مباشرة من المصادر الرسمية فور اعتمادها. نوفر لك أدوات حصرية مثل حساب المجموع المئوي، طباعة شهادات التقدير، والبحث المتقدم بالاسم.';
    @endphp
    
    @if($aboutActive && $aboutContent)
    <div class="mt-16 mb-12">
        <div class="max-w-4xl mx-auto bg-gradient-to-br from-slate-50 to-emerald-50 rounded-3xl p-6 md:p-10 border border-emerald-100 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center justify-center gap-3 mb-6">
                    <h2 class="text-xl md:text-2xl font-black text-emerald-800">{{ $aboutTitle }}</h2>
                </div>
                <p class="text-base md:text-lg text-slate-700 leading-loose text-center font-medium">
                    {!! nl2br(e($aboutContent)) !!}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Regional Coverage Section -->
    <div class="mt-16 max-w-7xl mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-black text-slate-800 text-center mb-4 flex items-center justify-center gap-3">
            <i class="fa-solid fa-earth-americas text-emerald-600"></i>
            تغطية نتائج الامتحانات في الوطن العربي لعام 2026
        </h2>
        <p class="text-slate-500 font-semibold text-center mb-10 max-w-2xl mx-auto text-sm md:text-base">
            نغطي كبرى بوابات نتائج الطلاب في الشرق الأوسط ونقدم روابط مباشرة للاستعلام فور إقرارها من الهيئات الرسمية.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Egypt -->
            <div class="bg-gradient-to-br from-white to-slate-50 rounded-3xl p-6 border border-slate-200/60 shadow-md relative overflow-hidden group">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-lg shadow-sm">
                        <i class="fa-solid fa-flag"></i>
                    </span>
                    <h3 class="text-lg font-black text-slate-800">جمهورية مصر العربية</h3>
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-4 font-medium">
                    تابع نتائج الامتحانات في مصر أولاً بأول. نوفر وصولاً مباشراً للاستعلام عن نتيجة الشهادة الإعدادية لجميع المحافظات (مثل القاهرة، الجيزة، الإسكندرية، الدقهلية، الشرقية، الغربية)، بالإضافة إلى نتيجة الثانوية العامة، والشهادة الثانوية الأزهرية، ونتائج الدبلومات الفنية (التجارية، الصناعية، الزراعية، الفندقية) فور اعتمادها رسمياً.
                </p>
                <a href="{{ route('egypt.index') }}" class="text-emerald-600 font-bold text-xs flex items-center gap-1 hover:text-emerald-700">
                    <span>تصفح نتائج مصر</span>
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>

            <!-- Card 2: Iraq -->
            <div class="bg-gradient-to-br from-white to-slate-50 rounded-3xl p-6 border border-slate-200/60 shadow-md relative overflow-hidden group">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shadow-sm">
                        <i class="fa-solid fa-flag"></i>
                    </span>
                    <h3 class="text-lg font-black text-slate-800">جمهورية العراق</h3>
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-4 font-medium">
                    المنصة الموثوقة لعرض نتائج الامتحانات الوزارية في العراق. تابع إعلان نتائج الصف الثالث المتوسط ونتائج السادس الإعدادي بجميع فروعه (العلمي والأدبي والمهني) لكافة المحافظات العراقية (مثل بغداد، نينوى، البصرة، أربيل، ذي قار، كربلاء، النجف) فور صدورها من وزارة التربية العراقية.
                </p>
                <a href="{{ route('country.index', ['country' => 'iraq']) }}" class="text-emerald-600 font-bold text-xs flex items-center gap-1 hover:text-emerald-700">
                    <span>تصفح نتائج العراق</span>
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>

            <!-- Card 3: Libya -->
            <div class="bg-gradient-to-br from-white to-slate-50 rounded-3xl p-6 border border-slate-200/60 shadow-md relative overflow-hidden group">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-sm">
                        <i class="fa-solid fa-flag"></i>
                    </span>
                    <h3 class="text-lg font-black text-slate-800">دولة ليبيا</h3>
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-4 font-medium">
                    بوابة منظومة الامتحانات الوطنية في ليبيا للاستعلام الفوري عن النتائج الدراسية. استعلم مباشرة بالاسم ورقم الجلوس عن نتائج شهادة إتمام مرحلة التعليم الأساسي (الإعدادية) ونتائج الشهادة الثانوية لجميع مراقبات التربية والتعليم والبلديات الليبية.
                </p>
                <a href="{{ route('country.index', ['country' => 'libya']) }}" class="text-emerald-600 font-bold text-xs flex items-center gap-1 hover:text-emerald-700">
                    <span>تصفح نتائج ليبيا</span>
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>

            <!-- Card 4: Other Countries -->
            <div class="bg-gradient-to-br from-white to-slate-50 rounded-3xl p-6 border border-slate-200/60 shadow-md relative overflow-hidden group">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shadow-sm">
                        <i class="fa-solid fa-flag"></i>
                    </span>
                    <h3 class="text-lg font-black text-slate-800">الشام واليمن والسودان</h3>
                </div>
                <p class="text-slate-600 text-sm leading-relaxed mb-4 font-medium">
                    تغطية متكاملة لنتائج الطلاب والشهادات العامة في مختلف الدول العربية. استعلم فوراً عن نتائج الشهادة السودانية، والشهادة الثانوية العامة والأساسية في اليمن، ونتائج امتحانات التوجيهي في الأردن، والشهادات التعليمية في سوريا وفلسطين.
                </p>
                <a href="/" class="text-emerald-600 font-bold text-xs flex items-center gap-1 hover:text-emerald-700">
                    <span>تصفح باقي الدول</span>
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Collapsible FAQ Section -->
    <div class="mt-16 max-w-4xl mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-black text-slate-800 text-center mb-8 flex items-center justify-center gap-3">
            <i class="fa-solid fa-circle-question text-emerald-600"></i>
            الأسئلة الشائعة حول نتائج الامتحانات 2026
        </h2>
        
        <div class="space-y-4">
            <!-- FAQ 1 -->
            <details class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden transition-all duration-300 [&_summary::-webkit-details-marker]:hidden">
                <summary class="flex items-center justify-between p-6 cursor-pointer select-none">
                    <h3 class="text-base md:text-lg font-bold text-slate-800 pr-2 group-open:text-emerald-600 transition-colors">
                        كيف يمكنني الاستعلام عن نتيجة الامتحانات بالاسم ورقم الجلوس؟
                    </h3>
                    <span class="flex-shrink-0 ml-1.5 p-1.5 text-slate-400 bg-slate-50 rounded-lg group-open:bg-emerald-50 group-open:text-emerald-600 transition-all duration-300">
                        <i class="fa-solid fa-chevron-down text-xs group-open:rotate-180 transition-transform duration-300"></i>
                    </span>
                </summary>
                <div class="px-6 pb-6 pt-2 border-t border-slate-100">
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed font-medium">
                        للاستعلام عن نتيجتك الدراسية مجاناً وسريعاً عبر منصة نتيجتي، اتبع الخطوات التالية: أولاً، اختر بلدك من القائمة الرئيسية لنتائج الدول (مثل مصر، العراق، أو ليبيا). ثانياً، حدد الشهادة التعليمية التي تدرس بها. ثالثاً، أدخل رقم جلوسك أو اسمك الرباعي بدقة في مربع البحث، ثم اضغط على زر "عرض النتيجة" لتظهر لك تفاصيل الدرجات والمجموع الإجمالي والنسبة المئوية فوراً.
                    </p>
                </div>
            </details>

            <!-- FAQ 2 -->
            <details class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden transition-all duration-300 [&_summary::-webkit-details-marker]:hidden">
                <summary class="flex items-center justify-between p-6 cursor-pointer select-none">
                    <h3 class="text-base md:text-lg font-bold text-slate-800 pr-2 group-open:text-emerald-600 transition-colors">
                        ما هي الشهادات التعليمية والدول المتاحة على بوابة نتيجتي؟
                    </h3>
                    <span class="flex-shrink-0 ml-1.5 p-1.5 text-slate-400 bg-slate-50 rounded-lg group-open:bg-emerald-50 group-open:text-emerald-600 transition-all duration-300">
                        <i class="fa-solid fa-chevron-down text-xs group-open:rotate-180 transition-transform duration-300"></i>
                    </span>
                </summary>
                <div class="px-6 pb-6 pt-2 border-t border-slate-100">
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed font-medium">
                        تغطي بوابة نتيجتي كافة الشهادات العامة والأزهرية والوزارية في كبرى الدول العربية. تشمل التغطية نتائج امتحانات الشهادة الإعدادية والثانوية العامة والأزهرية والدبلومات الفنية في مصر، ونتائج الصف الثالث المتوسط والسادس الإعدادي في العراق، بالإضافة إلى شهادات مرحلة التعليم الأساسي والثانوي في ليبيا، والشهادة السودانية، والشهادة الثانوية والأساسية في اليمن، والتوجيهي في الأردن، والشهادات الرسمية في سوريا وفلسطين.
                    </p>
                </div>
            </details>

            <!-- FAQ 3 -->
            <details class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden transition-all duration-300 [&_summary::-webkit-details-marker]:hidden">
                <summary class="flex items-center justify-between p-6 cursor-pointer select-none">
                    <h3 class="text-base md:text-lg font-bold text-slate-800 pr-2 group-open:text-emerald-600 transition-colors">
                        هل نتائج الامتحانات المعروضة على المنصة رسمية ومطابقة للوزارات؟
                    </h3>
                    <span class="flex-shrink-0 ml-1.5 p-1.5 text-slate-400 bg-slate-50 rounded-lg group-open:bg-emerald-50 group-open:text-emerald-600 transition-all duration-300">
                        <i class="fa-solid fa-chevron-down text-xs group-open:rotate-180 transition-transform duration-300"></i>
                    </span>
                </summary>
                <div class="px-6 pb-6 pt-2 border-t border-slate-100">
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed font-medium">
                        نعم، جميع النتائج المنشورة على منصتنا رسمية بنسبة 100% ومطابقة للبيانات المعتمدة في قطاع الامتحانات بوزارة التربية والتعليم والتعليم الفني في مصر، ووزارة التربية العراقية، ووزارة التربية والتعليم الليبية، ومختلف الهيئات الرسمية. يتم تحديث ومزامنة قواعد البيانات ورفع لينكات الاستعلام بالتنسيق المباشر فور اعتماد النتائج وإعلان نسب النجاح.
                    </p>
                </div>
            </details>

            <!-- FAQ 4 -->
            <details class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden transition-all duration-300 [&_summary::-webkit-details-marker]:hidden">
                <summary class="flex items-center justify-between p-6 cursor-pointer select-none">
                    <h3 class="text-base md:text-lg font-bold text-slate-800 pr-2 group-open:text-emerald-600 transition-colors">
                        كيف يمكنني طباعة كشف الدرجات أو تصميم شهادة تقدير للناجحين؟
                    </h3>
                    <span class="flex-shrink-0 ml-1.5 p-1.5 text-slate-400 bg-slate-50 rounded-lg group-open:bg-emerald-50 group-open:text-emerald-600 transition-all duration-300">
                        <i class="fa-solid fa-chevron-down text-xs group-open:rotate-180 transition-transform duration-300"></i>
                    </span>
                </summary>
                <div class="px-6 pb-6 pt-2 border-t border-slate-100">
                    <p class="text-sm md:text-base text-slate-600 leading-relaxed font-medium">
                        توفر المنصة أدوات حصرية لمشاركة فرحة النجاح مجاناً. بمجرد ظهور نتيجتك، يمكنك النقر على زر "طباعة النتيجة" للحصول على كشف درجات منسق وصالح للطباعة. كما يمكنك الانتقال إلى خدمة "صمم شهادتك" وإدخل اسم الطالب، المدرسة، والمجموع الكلي، ليقوم النظام بتوليد شهادة تقدير احترافية ومتميزة وحفظها كصورة عالية الجودة لمشاركتها مع العائلة والأصدقاء.
                    </p>
                </div>
            </details>
        </div>
    </div>

    <!-- كلمات البحث الشائعة -->
    <div class="max-w-4xl mx-auto mt-6">
        @include('partials.popular-keywords')
    </div>

    <!-- Comprehensive Homepage SEO Article -->
    <div class="max-w-5xl mx-auto mt-16 px-3 pb-8">
        <article class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-12">
            <h2 class="text-2xl md:text-3xl font-black text-gray-800 mb-6 border-r-4 border-emerald-500 pr-4">
                منصة نتيجتي — المرجع الأشمل لنتائج الامتحانات العربية 2026
            </h2>
            <p class="text-gray-600 text-base md:text-lg leading-relaxed mb-6">
                في كل عام، يتطلع ملايين الطلاب وأولياء الأمور في أرجاء الوطن العربي إلى لحظة إعلان نتائج الامتحانات الرسمية بترقب وقلق. تُدرك منصة <strong>نتيجتي (Ntegty.com)</strong> أهمية هذه اللحظة، وتُتيح الاستعلام عن النتائج فوراً وبدون أي رسوم أو حواجز مدفوعة. تُقدّم المنصة خدماتها منذ سنوات متواصلة وأصبحت اليوم من أوسع المنصات تغطيةً لنتائج الامتحانات في المنطقة العربية.
            </p>

            <h3 class="text-xl font-bold text-gray-800 mb-4">الدول والمراحل التي تغطيها المنصة</h3>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-5 border border-emerald-100">
                    <div class="text-2xl mb-2">🇪🇬</div>
                    <h4 class="font-black text-gray-800 mb-2">مصر</h4>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li>✔ الشهادة الإعدادية (27 محافظة)</li>
                        <li>✔ الثانوية العامة</li>
                        <li>✔ الثانوية والإعدادية الأزهرية</li>
                        <li>✔ الابتدائية الأزهرية</li>
                        <li>✔ الدبلومات الفنية (5 شعب)</li>
                    </ul>
                </div>
                <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl p-5 border border-orange-100">
                    <div class="text-2xl mb-2">🇮🇶</div>
                    <h4 class="font-black text-gray-800 mb-2">العراق</h4>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li>✔ نتائج السادس الابتدائي</li>
                        <li>✔ نتائج الثالث المتوسط</li>
                        <li>✔ نتائج السادس الإعدادي</li>
                        <li>✔ الشعبة العلمية والأدبية</li>
                        <li>✔ جميع المحافظات العراقية</li>
                    </ul>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-2xl p-5 border border-teal-100">
                    <div class="text-2xl mb-2">🇱🇾</div>
                    <h4 class="font-black text-gray-800 mb-2">ليبيا</h4>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li>✔ الشهادة الإعدادية</li>
                        <li>✔ الشهادة الثانوية</li>
                        <li>✔ الشعبة العلمية والأدبية والزراعية</li>
                        <li>✔ جميع المناطق والبلديات</li>
                    </ul>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl p-5 border border-indigo-100">
                    <div class="text-2xl mb-2">🇸🇩</div>
                    <h4 class="font-black text-gray-800 mb-2">السودان</h4>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li>✔ نتائج الشهادة السودانية</li>
                        <li>✔ الأساس والثانوي</li>
                    </ul>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-sky-50 rounded-2xl p-5 border border-sky-100">
                    <div class="text-2xl mb-2">🇾🇪</div>
                    <h4 class="font-black text-gray-800 mb-2">اليمن</h4>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li>✔ نتائج الثانوية اليمنية</li>
                        <li>✔ الشعب العلمية والأدبية</li>
                    </ul>
                </div>
                <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-2xl p-5 border border-amber-100">
                    <div class="text-2xl mb-2">🌍</div>
                    <h4 class="font-black text-gray-800 mb-2">دول أخرى</h4>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li>✔ فلسطين، الأردن</li>
                        <li>✔ موريتانيا</li>
                        <li>✔ تحديثات مستمرة</li>
                    </ul>
                </div>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-3">كيف تعمل منصة نتيجتي؟</h3>
            <p class="text-gray-600 leading-relaxed mb-4">
                تعمل منصة نتيجتي من خلال ربط مباشر بقواعد البيانات الرسمية والمصادر المعتمدة لإعلان النتائج. حالما يُعلَن عن النتائج رسمياً من وزارة التربية والتعليم في كل دولة، يتم تحميلها وإتاحتها للبحث في غضون دقائق. النظام يعتمد خوارزميات بحث متطورة تُتيح الإيجاد بالاسم الثلاثي أو الرباعي بدقة عالية حتى في حالة وجود أخطاء إملائية بسيطة، كما يدعم البحث برقم الجلوس لنتائج فورية.
            </p>

            <h3 class="text-xl font-bold text-gray-800 mb-3">مميزات منصة نتيجتي الحصرية</h3>
            <div class="grid sm:grid-cols-2 gap-4 mb-6">
                <div class="flex items-start gap-3">
                    <span class="text-emerald-600 font-black text-lg mt-0.5">✓</span>
                    <div>
                        <strong class="text-gray-800 text-sm">بحث بالاسم أو الرقم:</strong>
                        <p class="text-gray-600 text-sm">ادخل اسم الطالب كاملاً أو رقم الجلوس للحصول على النتيجة فوراً</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-emerald-600 font-black text-lg mt-0.5">✓</span>
                    <div>
                        <strong class="text-gray-800 text-sm">طباعة كشف الدرجات:</strong>
                        <p class="text-gray-600 text-sm">طباعة النتيجة بتنسيق احترافي أو حفظها كـ PDF مجاناً</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-emerald-600 font-black text-lg mt-0.5">✓</span>
                    <div>
                        <strong class="text-gray-800 text-sm">شهادة تقدير مجانية:</strong>
                        <p class="text-gray-600 text-sm">توليد شهادة تقدير احترافية للناجحين بكبسة زر واحدة</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-emerald-600 font-black text-lg mt-0.5">✓</span>
                    <div>
                        <strong class="text-gray-800 text-sm">متاح 24/7 بلا انقطاع:</strong>
                        <p class="text-gray-600 text-sm">بنية تحتية قوية تتحمل الملايين من عمليات البحث المتزامنة</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-emerald-600 font-black text-lg mt-0.5">✓</span>
                    <div>
                        <strong class="text-gray-800 text-sm">أوائل المحافظات:</strong>
                        <p class="text-gray-600 text-sm">متابعة أوائل الطلاب على مستوى المحافظة والمرحلة</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-emerald-600 font-black text-lg mt-0.5">✓</span>
                    <div>
                        <strong class="text-gray-800 text-sm">مجاني تماماً:</strong>
                        <p class="text-gray-600 text-sm">لا رسوم، لا تسجيل، لا إعلانات مزعجة — خدمة نظيفة ومجانية</p>
                    </div>
                </div>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-3">أكثر الأسئلة شيوعاً عن موعد ظهور النتائج</h3>
            <p class="text-gray-600 leading-relaxed mb-3">
                يطرح كثير من الطلاب وأولياء الأمور سؤال "متى تظهر نتيجة الإعدادية 2026؟" — والإجابة تعتمد على إتمام عمليات التصحيح في لجان الكنترول التابعة للوزارة. الإعلان عادةً يكون في يوليو لنتائج مصر، وفي مواعيد متفاوتة لبقية الدول العربية. تتيح منصة نتيجتي رسائل تنبيه فور الإعلان عبر قنواتها على واتساب وتيليجرام وفيسبوك.
            </p>
            <p class="text-gray-600 leading-relaxed mb-4">
                يُقدّر عدد مستخدمي المنصة خلال موسم إعلان النتائج بالملايين من كل الدول العربية. لذلك، تستعد المنصة كل عام برفع طاقتها الاستيعابية لضمان تجربة سلسة وسريعة لجميع الزوار حتى في ذروة الاستعلام.
            </p>

            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-6 mt-6 text-white text-center">
                <p class="font-black text-lg mb-2">🎓 ابدأ الاستعلام الآن — اختر دولتك من القائمة أعلاه</p>
                <p class="text-emerald-100 text-sm">نتيجتي | المنصة العربية الأولى لنتائج الامتحانات الرسمية — مجاني ودقيق وسريع</p>
            </div>
        </article>
    </div>
</div>
@endsection
