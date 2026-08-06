@extends('layouts.layout')

@section('structured_data')
@if(isset($structuredData))
{!! $structuredData !!}
@endif
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Title -->
    <div class="text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-black text-gray-800 mb-4 leading-tight">
            نتيجة السادس الإعدادي في العراق 2026
        </h1>
        <p class="text-lg text-gray-600 font-medium">
            اختر المحافظة للاستعلام عن النتيجة
        </p>
    </div>

    <!-- Result Timer -->
    <div class="w-full max-w-6xl mx-auto px-3">
        <x-result-timer country="iraq" type="preparatory" />
    </div>

    <!-- Provinces Flex Grid -->
    <div class="flex flex-wrap justify-center gap-6">
        @foreach($governorates as $governorate)
        <a href="{{ route('iraq.province.results', $governorate) }}" 
           class="group block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border-2 border-transparent hover:border-iraq-400 w-[calc(50%-12px)] md:w-48 lg:w-56 transform hover:-translate-y-1">
            <!-- Logo -->
            @if($governorate->logo_path)
            <div class="h-32 bg-gray-50 flex items-center justify-center p-4 group-hover:bg-iraq-50 transition-colors">
                <img src="{{ asset('uploads/' . $governorate->logo_path) }}" 
                     alt="{{ $governorate->name_ar }}" 
                     class="max-h-full max-w-full object-contain drop-shadow-sm group-hover:scale-110 transition-transform duration-500">
            </div>
            @else
            <div class="h-32 bg-gradient-to-br from-iraq-50 to-iraq-100 flex items-center justify-center group-hover:from-iraq-100 group-hover:to-iraq-200 transition-colors">
                <i class="fa-solid fa-map-location-dot text-4xl text-iraq-500 group-hover:scale-110 transition-transform duration-500"></i>
            </div>
            @endif
            
            <!-- Name -->
            <div class="p-4 text-center">
                <h3 class="text-lg font-bold text-gray-800 group-hover:text-iraq-600 transition">
                    {{ $governorate->name_ar }}
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $governorate->name_en }}
                </p>
            </div>
        </a>
        @endforeach
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
                        prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-green-500 prose-h2:pr-4 prose-h2:py-1
                        prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-green-700
                        prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                        prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                        prose-a:text-green-600 prose-a:hover:text-green-700">
                {!! $examType->getFormattedContentBody() !!}
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Country Content Section for SEO --}}
    @php
        $iraq = \App\Models\Country::where('code', 'IQ')->first();
    @endphp
    @if(isset($iraq) && $iraq->show_content_section && ($iraq->content_title || $iraq->content_body))
    <div class="w-full max-w-6xl mx-auto mt-8 px-3">
        <div class="bg-gradient-to-br from-red-50 to-white rounded-2xl shadow-lg p-6 md:p-10 border border-red-100">
            @if($iraq->content_title)
            <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-5 pb-3 border-b-2 border-red-200 flex items-center gap-3">
                <i class="fa-solid fa-flag text-red-600"></i>
                {{ $iraq->content_title }}
            </h2>
            @endif
            @if($iraq->content_intro)
            <p class="text-gray-600 mb-6 text-base md:text-lg leading-relaxed">{!! $iraq->content_intro !!}</p>
            @endif
            @if($iraq->content_body)
            <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                        prose-headings:font-bold prose-headings:text-gray-800 prose-headings:mt-6 prose-headings:mb-3
                        prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-red-500 prose-h2:pr-4 prose-h2:py-1
                        prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-red-700
                        prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                        prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                        prose-a:text-red-600 prose-a:hover:text-red-700">
                {!! $iraq->content_body !!}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Rich SEO Article — Iraq Exam Results 2026 -->
    <div class="max-w-5xl mx-auto mt-14 mb-8 px-3">
        <article class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-12">
            <h2 class="text-2xl md:text-3xl font-black text-gray-800 mb-6 border-r-4 border-red-500 pr-4">
                نتائج امتحانات العراق 2026 — دليل شامل للاستعلام
            </h2>
            <p class="text-gray-600 text-base md:text-lg leading-relaxed mb-6">
                تُعدّ امتحانات الشهادتين المتوسطة والإعدادية في العراق من أهم المحطات التعليمية في حياة الطالب العراقي. تُجرى هذه الامتحانات تحت إشراف مديرية الامتحانات العامة التابعة لوزارة التربية العراقية، وتُعلَن نتائجها رسمياً عبر القناة الرسمية للوزارة. تُسهّل منصة <strong>نتيجتي</strong> الوصول إلى نتيجة الطالب فور إعلانها من خلال محرك بحث ذكي يدعم البحث بالاسم ورقم الامتحان.
            </p>

            <h3 class="text-xl font-bold text-gray-800 mb-4">مراحل الامتحانات الرسمية في العراق</h3>
            <div class="grid sm:grid-cols-3 gap-4 mb-7">
                <div class="bg-red-50 rounded-2xl p-5 border border-red-100 text-center">
                    <div class="text-3xl mb-2"><i class="fa-solid fa-book-open text-emerald-600"></i></div>
                    <h4 class="font-black text-gray-800 mb-1">السادس الابتدائي</h4>
                    <p class="text-gray-600 text-sm">امتحان المرحلة الابتدائية — يُعقد في مايو/يونيو من كل عام لجميع محافظات العراق</p>
                </div>
                <div class="bg-orange-50 rounded-2xl p-5 border border-orange-100 text-center">
                    <div class="text-3xl mb-2"><i class="fa-solid fa-graduation-cap text-blue-600"></i></div>
                    <h4 class="font-black text-gray-800 mb-1">الثالث المتوسط</h4>
                    <p class="text-gray-600 text-sm">شهادة إتمام المرحلة المتوسطة — بوابة القبول في الثانوية العامة بالشعبتين العلمية والأدبية</p>
                </div>
                <div class="bg-yellow-50 rounded-2xl p-5 border border-yellow-100 text-center">
                    <div class="text-3xl mb-2"><i class="fa-solid fa-trophy text-yellow-500"></i></div>
                    <h4 class="font-black text-gray-800 mb-1">السادس الإعدادي</h4>
                    <p class="text-gray-600 text-sm">شهادة الدراسة الإعدادية — مفتاح القبول في الجامعات والكليات العراقية والعربية</p>
                </div>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-3">كيفية الاستعلام عن نتيجة الطالب العراقي</h3>
            <ol class="space-y-3 text-gray-600 mb-6 pr-4">
                <li class="flex gap-3 items-start">
                    <span class="bg-red-600 text-white rounded-full w-7 h-7 flex-shrink-0 flex items-center justify-center font-bold text-sm">1</span>
                    <div><strong class="text-gray-800">اختر المحافظة:</strong> انقر على محافظة الطالب من الشبكة أعلاه. تشمل جميع المحافظات من بغداد إلى البصرة والأنبار وكردستان وغيرها.</div>
                </li>
                <li class="flex gap-3 items-start">
                    <span class="bg-red-600 text-white rounded-full w-7 h-7 flex-shrink-0 flex items-center justify-center font-bold text-sm">2</span>
                    <div><strong class="text-gray-800">ابحث بالاسم أو الرقم:</strong> بعد اختيار المحافظة، أدخل اسم الطالب كاملاً أو رقم الامتحان في خانة البحث.</div>
                </li>
                <li class="flex gap-3 items-start">
                    <span class="bg-red-600 text-white rounded-full w-7 h-7 flex-shrink-0 flex items-center justify-center font-bold text-sm">3</span>
                    <div><strong class="text-gray-800">اطلع على التفاصيل:</strong> تظهر النتيجة كاملة مع درجات كل مادة والمجموع الكلي والتقدير النهائي.</div>
                </li>
                <li class="flex gap-3 items-start">
                    <span class="bg-red-600 text-white rounded-full w-7 h-7 flex-shrink-0 flex items-center justify-center font-bold text-sm">4</span>
                    <div><strong class="text-gray-800">احتفل واحفظ النتيجة:</strong> يمكنك طباعة نتيجتك أو تحميل شهادة تقدير احترافية مجانية عبر منصة نتيجتي.</div>
                </li>
            </ol>

            <h3 class="text-xl font-bold text-gray-800 mb-3">مواعيد إعلان النتائج في العراق 2026</h3>
            <p class="text-gray-600 leading-relaxed mb-4">
                تُعلن وزارة التربية العراقية عن نتائج السادس الابتدائي في أواخر شهر مايو أو بداية يونيو، بينما تصدر نتائج الثالث المتوسط والسادس الإعدادي في يونيو ويوليو على التوالي. تُتيح منصة نتيجتي الاطلاع الفوري على النتائج فور إعلانها الرسمي، مع بث مباشر للإعلانات عبر قنوات التواصل الاجتماعي للمنصة على واتساب وتيليجرام.
            </p>

            <h3 class="text-xl font-bold text-gray-800 mb-3">نظام الدرجات والتقديرات في العراق</h3>
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden">
                    <thead class="bg-red-600 text-white">
                        <tr>
                            <th class="py-3 px-4 text-right">النسبة المئوية</th>
                            <th class="py-3 px-4 text-right">التقدير</th>
                            <th class="py-3 px-4 text-right">الوصف</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-yellow-50 border-b border-gray-100">
                            <td class="py-2 px-4 font-bold">90% فأكثر</td>
                            <td class="py-2 px-4 font-bold text-yellow-700">امتياز</td>
                            <td class="py-2 px-4 text-gray-600">مرتبة الشرف الأولى</td>
                        </tr>
                        <tr class="bg-green-50 border-b border-gray-100">
                            <td class="py-2 px-4 font-bold">80% — 89%</td>
                            <td class="py-2 px-4 font-bold text-green-700">جيد جداً</td>
                            <td class="py-2 px-4 text-gray-600">مرتبة الشرف الثانية</td>
                        </tr>
                        <tr class="bg-blue-50 border-b border-gray-100">
                            <td class="py-2 px-4 font-bold">70% — 79%</td>
                            <td class="py-2 px-4 font-bold text-blue-700">جيد</td>
                            <td class="py-2 px-4 text-gray-600">نجاح بتفوق</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-2 px-4 font-bold">50% — 69%</td>
                            <td class="py-2 px-4 font-bold text-gray-700">مقبول</td>
                            <td class="py-2 px-4 text-gray-600">نجاح عادي</td>
                        </tr>
                        <tr class="bg-red-50">
                            <td class="py-2 px-4 font-bold">أقل من 50%</td>
                            <td class="py-2 px-4 font-bold text-red-700">راسب</td>
                            <td class="py-2 px-4 text-gray-600">يحق له التقديم في الدور الثاني</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-3">أهمية نتيجة السادس الإعدادي للقبول الجامعي</h3>
            <p class="text-gray-600 leading-relaxed mb-4">
                تُعتبر نتيجة السادس الإعدادي المعيار الأساسي للقبول في الجامعات العراقية عبر نظام القبول المركزي التابع لوزارة التعليم العالي والبحث العلمي. تُحدد النسبة التجميعية مدى إمكانية القبول في الكليات المرغوبة مثل الطب والهندسة والصيدلة والعلوم الإدارية. يسعى الطلاب المتفوقون للالتحاق ببعثات الدراسة الخارجية عبر هيئة الرعاية الأكاديمية للطلبة الموهوبين.
            </p>

            <div class="bg-gradient-to-r from-red-600 to-orange-600 rounded-2xl p-5 mt-6 text-white text-center">
                <p class="font-black text-base mb-1"><i class="fa-solid fa-graduation-cap text-blue-600"></i> اختر محافظتك الآن وابحث عن نتيجتك</p>
                <p class="text-red-100 text-sm">نتيجتي.com — بوابة نتائج العراق الرسمية الموثوقة</p>
            </div>
        </article>
    </div>
</div>
@endsection
