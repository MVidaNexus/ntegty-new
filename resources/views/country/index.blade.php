@extends('layouts.layout')

@php
    // SEO من إعدادات الدولة
    $pageTitle = $country->seo_title ?: "نتائج شهادات {$country->name_ar} | نتيجتي";
    $pageDescription = $country->seo_description ?: "نتائج امتحانات الشهادات في {$country->name_ar} - البحث بالاسم ورقم الجلوس. منصة نتيجتي لعرض النتائج فور اعتمادها.";
    $pageKeywords = $country->seo_keywords ?: "نتائج {$country->name_ar}, امتحانات {$country->name_ar}, شهادات {$country->name_ar}";
@endphp

@section('meta')
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $pageKeywords }}">
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumbs -->
    @if(isset($breadcrumbs))
    <nav class="mb-6 text-sm">
        <ol class="flex items-center gap-2 text-gray-600">
            @foreach($breadcrumbs as $index => $crumb)
                @if($index > 0)
                    <li><i class="fa-solid fa-chevron-left text-xs mx-2"></i></li>
                @endif
                <li>
                    @if(isset($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="hover:text-blue-600">{{ $crumb['name'] }}</a>
                    @else
                        <span class="text-gray-800 font-semibold">{{ $crumb['name'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
    @endif

    <!-- Page Header -->
    <div class="text-center mb-12">
        <div class="flex items-center justify-center gap-4 mb-4">
            @if($country->flag_path)
                <img src="{{ asset('uploads/' . $country->flag_path) }}" 
                     alt="{{ $country->name_ar }}" 
                     class="w-20 h-16 object-cover rounded-lg shadow-lg">
            @endif
            <h1 class="text-4xl md:text-5xl font-black text-gray-800 leading-tight">
                {{ $title ?? "نتائج شهادات {$country->name_ar}" }}
            </h1>
        </div>
        <p class="text-lg md:text-xl text-gray-600 font-medium">
            اختر نوع الشهادة للاستعلام عن النتيجة
        </p>
    </div>

    <!-- Exam Types Flex Grid -->
    <div class="w-full max-w-6xl mx-auto px-3 flex flex-wrap justify-center gap-6">
        @foreach($examTypes as $examType)
        <a href="{{ route('country.exam', [$country, $examType->slug]) }}" 
           class="group block bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-blue-100 hover:border-blue-400 transform hover:-translate-y-1 w-full md:w-[calc(50%-12px)] lg:max-w-sm">
            <div class="p-8">
                <!-- Icon -->
                <div class="flex items-center justify-center mb-6">
                    @if($examType->level === 'preparatory')
                        <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform">
                            <i class="fa-solid fa-user-graduate text-3xl text-white"></i>
                        </div>
                    @else
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform">
                            <i class="fa-solid fa-certificate text-3xl text-white"></i>
                        </div>
                    @endif
                </div>

                <!-- Title -->
                <h2 class="text-2xl font-bold text-gray-800 text-center mb-3 group-hover:text-blue-600 transition">
                    {{ $examType->name_ar }}
                </h2>

                <!-- Description -->
                <p class="text-gray-600 text-center text-sm mb-4">
                    @if($examType->level === 'preparatory')
                        عرض جميع المحافظات وتحميل النتائج
                    @else
                        البحث الموحد عن النتيجة
                    @endif
                </p>

                <!-- Arrow Icon -->
                <div class="flex justify-center mt-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fa-solid fa-arrow-left text-blue-600 text-xl transform group-hover:-translate-x-2 transition-transform"></i>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <!-- Info Section -->
    <div class="w-full max-w-6xl mx-auto px-3 mt-12">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-r-4 border-blue-500 rounded-xl p-6 shadow-md">
            <div class="flex items-start gap-4">
                <svg class="w-8 h-8 text-blue-600 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">معلومات هامة</h3>
                    <ul class="text-blue-800 space-y-2">
                        @foreach($examTypes as $type)
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-circle-check text-blue-600 mt-1.5 text-xs"></i>
                            <span>
                                @if($type->level === 'preparatory')
                                    يمكنك تحميل نتائج <strong>{{ $type->name_ar }}</strong> كملفات PDF أو البحث في قاعدة البيانات المتاحة.
                                @else
                                    الاستعلام عن نتائج <strong>{{ $type->name_ar }}</strong> متاح الآن باستخدام الاسم أو رقم الجلوس وبدون أي رسوم.
                                @endif
                            </span>
                        </li>
                        @endforeach
                        
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-circle-check text-blue-600 mt-1.5 text-xs"></i>
                            <span>
                                يتم رفع النتائج وتحديث الروابط فور اعتمادها رسمياً من 
                                @if(Str::contains($country->name_ar, ['العراق', 'الكويت']))
                                    <strong>وزارة التربية {{ $country->name_ar == 'العراق' ? 'العراقية' : '' }}</strong>.
                                @else
                                    <strong>وزارة التربية والتعليم</strong>.
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Box from Country Settings -->
    @if($country->show_content_section && ($country->content_title || $country->content_body))
    <div class="w-full max-w-6xl mx-auto mt-12 px-3">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg p-6 md:p-10 border border-blue-100">
            @if($country->content_title)
            <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-blue-800 mb-5 pb-3 border-b-2 border-blue-200 flex items-center gap-3">
                <i class="fa-solid fa-globe text-blue-600"></i>
                {{ $country->content_title }}
            </h2>
            @endif
            @if($country->content_intro)
            <p class="text-blue-700 mb-6 text-base md:text-lg leading-relaxed">{{ $country->content_intro }}</p>
            @endif
            @if($country->content_body)
            <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                        prose-headings:font-bold prose-headings:text-blue-800 prose-headings:mt-6 prose-headings:mb-3
                        prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-blue-500 prose-h2:pr-4 prose-h2:py-1
                        prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-blue-700
                        prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                        prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                        prose-a:text-blue-600 prose-a:hover:text-blue-700">
                {!! $country->content_body !!}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Dynamic Educational Guide & Results Inquiry Guide (GEO/SEO 500+ words) -->
    <div class="w-full max-w-6xl mx-auto mt-12 px-3 no-print">
        <div class="bg-white rounded-3xl p-6 md:p-10 border border-slate-200 shadow-xl">
            <h2 class="text-xl md:text-2xl font-black text-slate-800 mb-6 pb-3 border-b border-slate-100 flex items-center gap-3">
                <i class="fa-solid fa-graduation-cap text-emerald-600"></i>
                دليل الاستعلام عن نتائج الامتحانات في {{ $country->name_ar }} لعام 2026
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-slate-600 text-sm md:text-base leading-relaxed">
                <!-- Col 1 -->
                <div class="space-y-6">
                    <div>
                        <h3 class="font-bold text-slate-800 mb-2 text-base flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            كيفية الاستعلام عن النتيجة خطوة بخطوة
                        </h3>
                        <p class="font-medium text-slate-500">
                            يمكن لجميع الطلاب وأولياء الأمور في {{ $country->name_ar }} الحصول على نتائج الامتحانات الفصلية والنهائية لعام 2026 إلكترونياً من خلال إدخال البيانات المخصصة للبحث. إذا كانت النتيجة مفعلة بالبحث المباشر، ما عليك سوى كتابة رقم الجلوس أو الرقم الامتحاني الخاص بك في حقل البحث بالأعلى والضغط على زر الاستعلام. ستقوم المنصة بعرض بيان الدرجات التفصيلي مباشرة مع المجموع والنسبة المئوية.
                        </p>
                    </div>

                    <div>
                        <h3 class="font-bold text-slate-800 mb-2 text-base flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            نظام توزيع الدرجات والتقديرات المعتمد
                        </h3>
                        <p class="font-medium text-slate-500">
                            يخضع نظام الامتحانات في {{ $country->name_ar }} لتعليمات وزارة التربية والتعليم الرسمية. يتم احتساب النسبة المئوية بقسمة المجموع الكلي للدرجات التي حصل عليها الطالب على المجموع الأقصى للشهادة مضروباً في 100. تصنف التقديرات العامة للطلاب الناجحين إلى ممتاز (من 85% فما فوق)، جيد جداً (من 75% إلى أقل من 85%)، جيد (من 65% إلى أقل من 75%)، ومقبول (من 50% إلى أقل من 65%)، بينما يعتبر الطالب راسباً إذا حصل على أقل من 50% في المواد الأساسية.
                        </p>
                    </div>
                </div>

                <!-- Col 2 -->
                <div class="space-y-6">
                    <div>
                        <h3 class="font-bold text-slate-800 mb-2 text-base flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            طرق تقديم الاعتراضات والتظلمات
                        </h3>
                        <p class="font-medium text-slate-500">
                            بعد إعلان نتائج الشهادات العامة في {{ $country->name_ar }} رسمياً، تفتح وزارة التربية والتعليم باب تقديم الاعتراضات وإعادة تصحيح الدفاتر لفترة محددة (عادة تتراوح بين أسبوع إلى أسبوعين). يمكن للطلبة تقديم طلب التظلم إلكترونياً أو في مقر الإدارات التعليمية والمراقبات التابعين لها بعد سداد الرسوم المقررة لكل مادة، ويتم مراجعة جمع الدرجات والتحقق من عدم وجود أخطاء في رصد الدرجات لإعادة الحقوق لأصحابها.
                        </p>
                    </div>

                    <div>
                        <h3 class="font-bold text-slate-800 mb-2 text-base flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            تطبيق نتيجتي ودعم مسيرة الطالب الأكاديمية
                        </h3>
                        <p class="font-medium text-slate-500">
                            تسعى منصة نتيجتي لتسهيل وصول الطلاب في {{ $country->name_ar }} لنتائجهم بيسر وسرعة فائقة دون عناء الانتظار أو مواجهة مشاكل توقف السيرفرات الرسمية أثناء ضغط الزيارات. كما توفر المنصة خدمات إضافية كطباعة بيان الدرجات بصيغة أنيقة وتوليد شهادات التقدير التكريمية للمتفوقين دعماً لمسيرتهم الأكاديمية وتحفيزهم على تحقيق أرفع المراتب العلمية في المستقبل.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4 text-xs md:text-sm text-slate-500 font-medium">
                <span class="flex items-center gap-1.5 text-emerald-600">
                    <i class="fa-solid fa-circle-check"></i>
                    البيانات مطابقة تماماً للمصدر الرسمي بوزارة التربية والتعليم لعام 2026.
                </span>
                <span>تاريخ التحديث: {{ date('Y-m-d') }}</span>
            </div>
        </div>
    </div>

    <!-- كلمات البحث الشائعة -->
    <div class="max-w-4xl mx-auto mt-12">
        @include('partials.popular-keywords')
    </div>
</div>
@endsection
