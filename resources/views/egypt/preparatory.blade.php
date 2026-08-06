@extends('layouts.layout')

@section('structured_data')
@if(isset($structuredData))
{!! $structuredData !!}
@endif
@endsection

@section('content')
<div class="w-full bg-gradient-to-b from-slate-50 to-white py-6">
    <div class="w-full px-3 max-w-[2000px] mx-auto">
        <!-- Breadcrumbs -->
        @if(isset($breadcrumbs))
        <nav class="mb-4 text-sm">
            <ol class="flex items-center gap-2 text-gray-600">
                @foreach($breadcrumbs as $index => $crumb)
                    @if($index > 0)
                        <li><i class="fa-solid fa-chevron-left text-xs mx-1"></i></li>
                    @endif
                    <li>
                        @if(isset($crumb['url']))
                            <a href="{{ $crumb['url'] }}" class="hover:text-emerald-600 transition-colors">{{ $crumb['name'] }}</a>
                        @else
                            <span class="text-gray-800 font-semibold">{{ $crumb['name'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
        @endif

        <!-- Page Title -->
        <div class="text-center mb-6">
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-black text-gray-800 mb-2 leading-tight">
                {{ $title }}
            </h1>
            <p class="text-base md:text-lg text-gray-600">
                اختر المحافظة لاستعراض النتيجة
            </p>
        </div>

        <!-- Search & Grid Container -->
        <div x-data="{ search: '' }">
            <!-- Search Bar -->
            <div class="max-w-xl mx-auto mb-6">
                <div class="relative group">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400 group-focus-within:text-emerald-500 transition-colors"></i>
                    </div>
                    <input type="text" 
                           x-model="search"
                           placeholder="ابحث عن محافظة..." 
                           class="block w-full pr-12 pl-4 py-3 bg-white border-2 border-gray-200 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-sm"
                    >
                </div>
            </div>

            @php
                $declaredGovernorates = $governorates->where('is_declared', true)->values();
                $pendingGovernorates = $governorates->where('is_declared', false)->values();
            @endphp

            {{-- Pre-Registration Form Component --}}
            @include('partials.pre-registration', ['examTypeSlug' => 'egypt_preparatory'])

            {{-- Declared Governorates Section --}}
            @if($declaredGovernorates->count() > 0)
            <div class="mb-8">
                <!-- Section Header -->
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl shadow-md">
                        <i class="fa-solid fa-circle-check"></i>
                        <span class="font-bold">محافظات تم اعتمادها رسمياً</span>
                        <span class="bg-white/20 px-2 py-0.5 rounded-full text-sm">{{ $declaredGovernorates->count() }}</span>
                    </div>
                    <div class="flex-1 h-px bg-gradient-to-l from-emerald-200 to-transparent"></div>
                </div>

                <!-- Declared Governorates Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5 md:gap-6">
                    @foreach($declaredGovernorates as $governorate)
                        @php
                            $hasLogo = !empty($governorate->logo_path);
                        @endphp
                    <a href="{{ route('egypt.governorate.results', $governorate) }}"
                       x-show="search === '' || '{{ $governorate->name_ar }}'.includes(search)"
                       x-transition:enter="transition ease-out duration-200"
                       x-transition:enter-start="opacity-0 scale-90"
                       x-transition:enter-end="opacity-100 scale-100"
                       class="group relative bg-gradient-to-br from-emerald-50 to-white rounded-2xl p-5 md:p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 overflow-hidden border-2 border-emerald-100 hover:border-emerald-300">
                        
                        <!-- Background Logo Watermark -->
                        @if($hasLogo)
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden">
                            <img 
                                src="{{ asset('uploads/' . $governorate->logo_path) }}"
                                alt=""
                                class="w-full h-full object-cover opacity-[0.06] scale-150"
                            >
                        </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-3 left-3 z-10">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-500 text-white rounded-full text-[10px] font-bold shadow-md">
                                <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                                متاحة
                            </span>
                        </div>

                        <!-- Logo Circle -->
                        <div class="relative z-10 flex justify-center mb-4">
                            <div class="w-20 h-20 md:w-24 md:h-24 rounded-full shadow-lg border-4 border-emerald-200 overflow-hidden group-hover:shadow-xl group-hover:scale-105 transition-all duration-300 bg-white flex items-center justify-center">
                                @if($hasLogo)
                                    <img 
                                        src="{{ asset('uploads/' . $governorate->logo_path) }}" 
                                        alt="شعار {{ $governorate->name_ar }}"
                                        class="w-full h-full object-contain p-2"
                                        loading="lazy"
                                        onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-2xl font-bold text-emerald-600\'>{{ mb_substr($governorate->name_ar, 0, 2) }}</span>';"
                                    >
                                @else
                                    <span class="text-2xl font-bold text-emerald-600">{{ mb_substr($governorate->name_ar, 0, 2) }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Governorate Name -->
                        <h2 class="relative z-10 text-lg md:text-xl font-bold text-slate-800 text-center mb-2 group-hover:text-emerald-600 transition-colors">
                            {{ $governorate->name_ar }}
                        </h2>
                        
                        <!-- View Results Button -->
                        <div class="relative z-10 flex items-center justify-center gap-1.5 text-sm text-emerald-600 font-medium">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>عرض النتيجة</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Pending Governorates Section --}}
            @if($pendingGovernorates->count() > 0)
            <div>
                <!-- Section Header -->
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-gray-400 to-gray-500 text-white rounded-xl shadow-md">
                        <i class="fa-solid fa-hourglass-half"></i>
                        <span class="font-bold">ما زلنا في الانتظار</span>
                        <span class="bg-white/20 px-2 py-0.5 rounded-full text-sm">{{ $pendingGovernorates->count() }}</span>
                    </div>
                    <div class="flex-1 h-px bg-gradient-to-l from-gray-200 to-transparent"></div>
                </div>

                <!-- Pending Governorates Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5 md:gap-6">
                    @foreach($pendingGovernorates as $governorate)
                        @php
                            $hasLogo = !empty($governorate->logo_path);
                        @endphp
                    <a href="{{ route('egypt.governorate.results', $governorate) }}"
                       x-show="search === '' || '{{ $governorate->name_ar }}'.includes(search)"
                       x-transition:enter="transition ease-out duration-200"
                       x-transition:enter-start="opacity-0 scale-90"
                       x-transition:enter-end="opacity-100 scale-100"
                       class="group relative bg-slate-50/80 rounded-2xl p-5 md:p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 overflow-hidden opacity-75 hover:opacity-100">
                        
                        <!-- Background Logo Watermark -->
                        @if($hasLogo)
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden">
                            <img 
                                src="{{ asset('uploads/' . $governorate->logo_path) }}"
                                alt=""
                                class="w-full h-full object-cover opacity-[0.04] scale-150"
                            >
                        </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-3 left-3 z-10">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-500 text-white rounded-full text-[10px] font-bold shadow-md">
                                <i class="fa-solid fa-clock text-[8px]"></i>
                                انتظار
                            </span>
                        </div>

                        <!-- Logo Circle -->
                        <div class="relative z-10 flex justify-center mb-4">
                            <div class="w-20 h-20 md:w-24 md:h-24 rounded-full shadow-lg border-4 border-white overflow-hidden group-hover:shadow-xl group-hover:scale-105 transition-all duration-300 bg-white flex items-center justify-center">
                                @if($hasLogo)
                                    <img 
                                        src="{{ asset('uploads/' . $governorate->logo_path) }}" 
                                        alt="شعار {{ $governorate->name_ar }}"
                                        class="w-full h-full object-contain p-2 grayscale group-hover:grayscale-0 transition-all"
                                        loading="lazy"
                                        onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-2xl font-bold text-gray-400\'>{{ mb_substr($governorate->name_ar, 0, 2) }}</span>';"
                                    >
                                @else
                                    <span class="text-2xl font-bold text-gray-400">{{ mb_substr($governorate->name_ar, 0, 2) }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Governorate Name -->
                        <h2 class="relative z-10 text-lg md:text-xl font-bold text-slate-600 text-center mb-2 group-hover:text-slate-800 transition-colors">
                            {{ $governorate->name_ar }}
                        </h2>
                        
                        <!-- View Results Button -->
                        <div class="relative z-10 flex items-center justify-center gap-1.5 text-sm text-slate-400">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>قريباً</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- No Results -->
            <div x-show="search !== '' && $el.querySelectorAll('a[style*=\'display: none\']').length === {{ count($governorates) }}" 
                 x-cloak
                 class="text-center py-12 mt-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 rounded-full mb-3">
                    <i class="fa-solid fa-search text-slate-400 text-xl"></i>
                </div>
                <p class="text-slate-500 font-medium">لا توجد محافظة بهذا الاسم</p>
            </div>
        </div>
        
        {{-- Content Section for SEO --}}
        @if(isset($examType) && $examType->show_content_section && ($examType->content_title || $examType->content_body))
        <div class="w-full max-w-6xl mx-auto mt-12 px-3">
            <div class="bg-white rounded-2xl shadow-lg p-6 md:p-10 border border-gray-100">
                @if($examType->content_title)
                <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-5 pb-3 border-b-2 border-gray-100">{{ $examType->content_title }}</h2>
                @endif
                @if($examType->content_intro)
                <p class="text-gray-600 mb-6 text-base md:text-lg leading-relaxed">{{ $examType->content_intro }}</p>
                @endif
                @if($examType->content_body)
                <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                            prose-headings:font-bold prose-headings:text-gray-800 prose-headings:mt-6 prose-headings:mb-3
                            prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-emerald-500 prose-h2:pr-4 prose-h2:py-1
                            prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-emerald-700
                            prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                            prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                            prose-a:text-emerald-600 prose-a:hover:text-emerald-700">
                    {!! $examType->getFormattedContentBody() !!}
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Rich SEO Article — Preparatory Results 800+ words — Updated July 2026 -->
        <div class="max-w-4xl mx-auto mt-14 px-3">
            <article class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 md:p-12">
                <h2 class="text-2xl md:text-3xl font-black text-gray-800 mb-5 border-r-4 border-blue-500 pr-4">
                    نتيجة الشهادة الإعدادية 2026 — كل شيء تحتاج معرفته الآن
                </h2>

                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6">
                    <p class="text-blue-800 font-bold text-sm mb-1"><i class="fa-solid fa-bullhorn text-emerald-500"></i> يوليو 2026 — النتائج تُعلَن تباعاً</p>
                    <p class="text-blue-700 text-sm leading-relaxed">تُعلن نتائج الشهادة الإعدادية هذا العام على مستوى المحافظات تباعاً بعد اعتماد كل كنترول. ابحث عن محافظتك أعلاه للاطلاع على النتيجة فور إعلانها.</p>
                </div>

                <p class="text-gray-600 text-base md:text-lg leading-relaxed mb-6">
                    هناك لحظات في حياة كل أسرة تتوقف عندها الأنفاس قليلاً — لحظة ظهور نتيجة الإعدادية واحدة منها. لأن هذه الشهادة ليست مجرد أرقام على ورقة، بل هي البوابة التي تحدد المسار القادم: ثانوية عامة، أم تعليم فني، أم معهد ديني. في منصة نتيجتي، نقف معك في هذه اللحظة ونقدم لك النتيجة في ثوانٍ معدودة، مجاناً وبدون إعلانات مزعجة.
                </p>

                <h3 class="text-xl font-bold text-gray-800 mb-3">ما الجديد في امتحانات الإعدادية 2026؟</h3>
                <p class="text-gray-600 leading-relaxed mb-4">
                    شهد العام الدراسي 2025/2026 عدة تطورات ملحوظة في منظومة الإعدادية المصرية. أبرزها توسيع تطبيق <strong>نظام التقييم الإلكتروني</strong> ليشمل محافظات إضافية، وهو النظام الذي يعتمد على أجهزة التابلت في رصد الإجابات وتصحيحها آلياً بالنسبة لبعض المواد. كما تضمّن التطوير مراجعة شاملة لمناهج مادة العلوم بما يتوافق مع متطلبات الاقتصاد الرقمي، ودخول مادة التكنولوجيا بشكل أكثر عمقاً في المنهج المقرر.
                </p>
                <p class="text-gray-600 leading-relaxed mb-6">
                    الجدير بالذكر أن <strong>نسبة نجاح الشهادة الإعدادية</strong> في مصر تتراوح عادةً بين 65% و75%، وقد حرصت الوزارة هذا العام على تيسير آليات الاعتراض لضمان عدالة أكبر في التقييم.
                </p>

                <h3 class="text-xl font-bold text-gray-800 mb-3">مواعيد الإعلان عن النتائج 2026</h3>
                <div class="overflow-x-auto mb-6">
                    <table class="w-full text-sm text-gray-600 border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-blue-600 text-white font-bold">
                            <tr>
                                <td class="p-3">المرحلة</td>
                                <td class="p-3 text-center">موعد الامتحانات</td>
                                <td class="p-3 text-center">موعد الإعلان المتوقع</td>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr><td class="p-3 font-medium">الشهادة الإعدادية (الحكومية)</td><td class="p-3 text-center">مايو–يونيو 2026</td><td class="p-3 text-center font-bold text-blue-700">يوليو 2026</td></tr>
                            <tr class="bg-gray-50"><td class="p-3 font-medium">الدور الثاني (الرسوب)</td><td class="p-3 text-center">أغسطس–سبتمبر</td><td class="p-3 text-center font-bold text-orange-600">سبتمبر 2026</td></tr>
                            <tr><td class="p-3 font-medium">الإعدادية الأزهرية</td><td class="p-3 text-center">مايو–يونيو 2026</td><td class="p-3 text-center font-bold text-amber-700">يوليو 2026</td></tr>
                        </tbody>
                    </table>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mb-3">المحافظات المصرية الـ 27 وكيفية الاستعلام</h3>
                <p class="text-gray-600 leading-relaxed mb-4">
                    يمكنك الاستعلام عن نتيجة أبنائك في أي من محافظات مصر: <strong>القاهرة، الجيزة، الإسكندرية، القليوبية، الشرقية، الدقهلية، الغربية، المنوفية، كفر الشيخ، البحيرة، الإسماعيلية، بور سعيد، السويس، دمياط، الفيوم، بني سويف، المنيا، أسيوط، سوهاج، قنا، الأقصر، أسوان، البحر الأحمر، الوادي الجديد، مطروح، شمال سيناء، جنوب سيناء.</strong>
                </p>
                <p class="text-gray-600 leading-relaxed mb-6">
                    اختر محافظتك من القائمة أعلاه ثم أدخل رقم الجلوس أو اسم الطالب كاملاً. ستظهر نتيجة مفصّلة تشمل درجات كل مادة، وإجمالي الدرجات، والتقدير، ونسبة النجاح.
                </p>

                <h3 class="text-xl font-bold text-gray-800 mb-3">المواد الدراسية وتوزيع الدرجات — الإعدادية 2026</h3>
                <div class="overflow-x-auto mb-6">
                    <table class="w-full text-sm text-gray-600 border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-slate-700 text-white font-bold">
                            <tr>
                                <td class="p-3">المادة</td>
                                <td class="p-3 text-center">الدرجة الكبرى</td>
                                <td class="p-3 text-center">حد النجاح</td>
                                <td class="p-3 text-center">ملاحظة</td>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr><td class="p-3 font-medium">اللغة العربية</td><td class="p-3 text-center">40</td><td class="p-3 text-center">20</td><td class="p-3 text-center text-xs text-gray-500">إلزامية</td></tr>
                            <tr class="bg-gray-50"><td class="p-3 font-medium">اللغة الأجنبية الأولى (إنجليزية)</td><td class="p-3 text-center">40</td><td class="p-3 text-center">20</td><td class="p-3 text-center text-xs text-gray-500">إلزامية</td></tr>
                            <tr><td class="p-3 font-medium">الرياضيات</td><td class="p-3 text-center">40</td><td class="p-3 text-center">20</td><td class="p-3 text-center text-xs text-gray-500">إلزامية</td></tr>
                            <tr class="bg-gray-50"><td class="p-3 font-medium">العلوم والبيئة</td><td class="p-3 text-center">40</td><td class="p-3 text-center">20</td><td class="p-3 text-center text-xs text-gray-500">إلزامية</td></tr>
                            <tr><td class="p-3 font-medium">الدراسات الاجتماعية</td><td class="p-3 text-center">40</td><td class="p-3 text-center">20</td><td class="p-3 text-center text-xs text-gray-500">إلزامية</td></tr>
                            <tr class="bg-gray-50"><td class="p-3 font-medium">التربية الدينية</td><td class="p-3 text-center">40</td><td class="p-3 text-center">—</td><td class="p-3 text-center text-xs text-gray-500">لا تُرسّب</td></tr>
                            <tr><td class="p-3 font-medium">التكنولوجيا</td><td class="p-3 text-center">20–40</td><td class="p-3 text-center">—</td><td class="p-3 text-center text-xs text-gray-500">حسب المحافظة</td></tr>
                            <tr class="bg-blue-50 font-bold"><td class="p-3 text-blue-800">الإجمالي</td><td class="p-3 text-center text-blue-800">240–280+</td><td class="p-3 text-center text-blue-800">50%</td><td class="p-3 text-center text-xs">—</td></tr>
                        </tbody>
                    </table>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mb-3">نظام التقديرات — من ممتاز إلى راسب</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                    <div class="bg-emerald-100 rounded-xl p-3 text-center border border-emerald-200">
                        <div class="font-black text-emerald-800 text-lg">ممتاز</div>
                        <div class="text-emerald-700 text-sm font-bold">85% فأكثر</div>
                    </div>
                    <div class="bg-blue-100 rounded-xl p-3 text-center border border-blue-200">
                        <div class="font-black text-blue-800 text-lg">جيد جداً</div>
                        <div class="text-blue-700 text-sm font-bold">75% – 84%</div>
                    </div>
                    <div class="bg-yellow-100 rounded-xl p-3 text-center border border-yellow-200">
                        <div class="font-black text-yellow-800 text-lg">جيد</div>
                        <div class="text-yellow-700 text-sm font-bold">65% – 74%</div>
                    </div>
                    <div class="bg-orange-100 rounded-xl p-3 text-center border border-orange-200">
                        <div class="font-black text-orange-800 text-lg">مقبول</div>
                        <div class="text-orange-700 text-sm font-bold">50% – 64%</div>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mb-3">ماذا بعد النتيجة؟ — دليل الخطوات التالية</h3>
                <p class="text-gray-600 leading-relaxed mb-4">
                    إذا أعلنت النتيجة ونجح ابنك أو ابنتك، فتهانينا أولاً! الخطوة الفورية هي <strong>التسجيل في بوابة التنسيق الإلكترونية</strong> التابعة للوزارة لاختيار نوع التعليم والمدرسة المناسبة. يُنصح بإدخال أكثر من رغبة ورتيب الأولويات بحكمة بناءً على المجموع والقدرات الشخصية.
                </p>
                <p class="text-gray-600 leading-relaxed mb-4">
                    أما إذا لم تكن النتيجة كما هو مأمول، فاعلم أن <strong>الدور الثاني</strong> فرصة حقيقية وليست نهاية الطريق. كثير من الطلاب تفوقوا في الدور الثاني بعد استعداد جيد. يمكن التقدم للدور الثاني في الإدارة التعليمية خلال الفترة المحددة في أغسطس، والامتحانات تكون في سبتمبر.
                </p>
                <p class="text-gray-600 leading-relaxed mb-5">
                    لا تنسَ أن <strong>الاعتراض على النتيجة</strong> حق مكفول قانوناً خلال 15 يوماً من تاريخ الإعلان. يُقدَّم الاعتراض عبر الإدارة التعليمية أو من خلال بوابة المواطن الإلكترونية بعد سداد رسوم رمزية.
                </p>

                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-5 text-white text-center">
                    <p class="font-black text-base mb-1"><i class="fa-solid fa-thumbtack text-red-500"></i> اختر محافظتك من الأعلى وابحث عن نتيجتك فوراً</p>
                    <p class="text-blue-100 text-sm">نتيجتي — مجانية، فورية، دقيقة | 24 ساعة يومياً</p>
                </div>
            </article>
        </div>

        <!-- كلمات البحث الشائعة -->
        <div class="max-w-4xl mx-auto">
            @include('partials.popular-keywords')
        </div>
    </div>
</div>
@endsection
