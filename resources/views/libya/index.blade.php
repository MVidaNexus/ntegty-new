@extends('layouts.layout')

@section('structured_data')
@if(isset($structuredData))
{!! $structuredData !!}
@endif
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Title -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">
            نتائج ليبيا - الشهادة الإعدادية
        </h1>
        <p class="text-lg text-gray-600">
            اختر المنطقة للبحث عن النتيجة
        </p>
    </div>

    <!-- Provinces Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        @foreach($governorates as $governorate)
        <a href="{{ route('libya.governorate.results', $governorate) }}" 
           class="group block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border-2 border-transparent hover:border-emerald-400">
            <!-- Logo -->
            @if($governorate->logo_path)
            <div class="h-32 bg-gray-100 flex items-center justify-center p-4">
                <img src="{{ asset('uploads/' . $governorate->logo_path) }}" 
                     alt="{{ $governorate->name_ar }}" 
                     class="max-h-full max-w-full object-contain">
            </div>
            @else
            <div class="h-32 bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center">
                <span class="text-5xl font-bold text-emerald-600">
                    {{ mb_substr($governorate->name_ar, 0, 1) }}
                </span>
            </div>
            @endif
            
            <!-- Name -->
            <div class="p-4 text-center">
                <h3 class="text-lg font-bold text-gray-800 group-hover:text-emerald-600 transition">
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

    {{-- Country Content Section for SEO --}}
    @php
        $libya = \App\Models\Country::where('code', 'LY')->first();
    @endphp
    @if(isset($libya) && $libya->show_content_section && ($libya->content_title || $libya->content_body))
    <div class="w-full max-w-6xl mx-auto mt-8 px-3">
        <div class="bg-gradient-to-br from-red-50 to-green-50 rounded-2xl shadow-lg p-6 md:p-10 border border-red-100">
            @if($libya->content_title)
            <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-5 pb-3 border-b-2 border-red-200 flex items-center gap-3">
                <i class="fa-solid fa-flag text-red-600"></i>
                {{ $libya->content_title }}
            </h2>
            @endif
            @if($libya->content_intro)
            <p class="text-gray-600 mb-6 text-base md:text-lg leading-relaxed">{!! $libya->content_intro !!}</p>
            @endif
            @if($libya->content_body)
            <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                        prose-headings:font-bold prose-headings:text-gray-800 prose-headings:mt-6 prose-headings:mb-3
                        prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-red-500 prose-h2:pr-4 prose-h2:py-1
                        prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-red-700
                        prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                        prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                        prose-a:text-red-600 prose-a:hover:text-red-700">
                {!! $libya->content_body !!}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Rich SEO Article — Libya Exam Results 2026 -->
    <div class="max-w-5xl mx-auto mt-14 mb-8 px-3">
        <article class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-12">
            <h2 class="text-2xl md:text-3xl font-black text-gray-800 mb-6 border-r-4 border-green-600 pr-4">
                نتائج امتحانات ليبيا 2026 — الشهادة الإعدادية والثانوية
            </h2>
            <p class="text-gray-600 text-base md:text-lg leading-relaxed mb-6">
                تُمثّل امتحانات الشهادتين الإعدادية والثانوية في ليبيا حجر الأساس في المسيرة الدراسية للطالب الليبي. تُجرى هذه الامتحانات تحت إشراف الهيئة الوطنية للامتحانات والقياس التابعة لوزارة التربية والتعليم، وتُعلَن نتائجها بصورة رسمية عبر الموقع الرسمي للوزارة. تُسهّل منصة <strong>نتيجتي</strong> الوصول السريع لنتيجة الطالب الليبي من أي مكان وفي أي وقت.
            </p>

            <h3 class="text-xl font-bold text-gray-800 mb-4">مراحل الامتحانات الرسمية في ليبيا</h3>
            <div class="grid sm:grid-cols-2 gap-5 mb-7">
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
                    <div class="text-3xl mb-3"><i class="fa-solid fa-book text-blue-500"></i></div>
                    <h4 class="font-black text-gray-800 mb-2">الشهادة الإعدادية (المرحلة الإعدادية)</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">تُعقد في نهاية المرحلة الإعدادية وتُخوّل الطالب الانتقال إلى المرحلة الثانوية. تشمل الشعبتين العلمية والأدبية وتُجرى في جميع البلديات الليبية</p>
                </div>
                <div class="bg-gradient-to-br from-red-50 to-black/5 rounded-2xl p-6 border border-red-100">
                    <div class="text-3xl mb-3"><i class="fa-solid fa-medal text-amber-500"></i></div>
                    <h4 class="font-black text-gray-800 mb-2">الشهادة الثانوية (التوجيهي)</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">الشهادة الوطنية الليبية الموحدة للقبول الجامعي. تشمل عدة شعب: علمي، أدبي، زراعي، صناعي، ورياضة وتربية بدنية</p>
                </div>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-3">مناطق وبلديات ليبيا المشمولة بالخدمة</h3>
            <p class="text-gray-600 leading-relaxed mb-4">
                تغطي منصة نتيجتي جميع المناطق الليبية بما فيها: <strong>طرابلس، بنغازي، مصراتة، الزاوية، الجفرة، سرت، درنة، طبرق، الخمس، غريان، يفرن، الجبل الأخضر، إجدابيا، الكفرة، غدامس، المرج</strong> وسائر البلديات الليبية. يُمكّن محرك البحث الطلاب من الاستعلام بالاسم الثلاثي أو الرباعي أو برقم الجلوس للحصول على النتيجة فوراً.
            </p>

            <h3 class="text-xl font-bold text-gray-800 mb-3">نظام التقديرات في الشهادة الثانوية الليبية</h3>
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden">
                    <thead class="bg-green-700 text-white">
                        <tr>
                            <th class="py-3 px-4 text-right">النسبة المئوية</th>
                            <th class="py-3 px-4 text-right">التقدير</th>
                            <th class="py-3 px-4 text-right">المرتبة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-yellow-50 border-b border-gray-100">
                            <td class="py-2 px-4 font-bold">85% فأكثر</td>
                            <td class="py-2 px-4 font-bold text-yellow-700">ممتاز</td>
                            <td class="py-2 px-4 text-gray-600">أوائل الطلاب</td>
                        </tr>
                        <tr class="bg-green-50 border-b border-gray-100">
                            <td class="py-2 px-4 font-bold">75% — 84%</td>
                            <td class="py-2 px-4 font-bold text-green-700">جيد جداً</td>
                            <td class="py-2 px-4 text-gray-600">مستوى متميز</td>
                        </tr>
                        <tr class="bg-blue-50 border-b border-gray-100">
                            <td class="py-2 px-4 font-bold">65% — 74%</td>
                            <td class="py-2 px-4 font-bold text-blue-700">جيد</td>
                            <td class="py-2 px-4 text-gray-600">مستوى فوق المتوسط</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-2 px-4 font-bold">50% — 64%</td>
                            <td class="py-2 px-4 font-bold text-gray-700">مقبول</td>
                            <td class="py-2 px-4 text-gray-600">نجاح بالحد الأدنى</td>
                        </tr>
                        <tr class="bg-red-50">
                            <td class="py-2 px-4 font-bold">أقل من 50%</td>
                            <td class="py-2 px-4 font-bold text-red-700">راسب</td>
                            <td class="py-2 px-4 text-gray-600">يحق التقديم في الدور الثاني</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-3">مواعيد إعلان النتائج في ليبيا 2026</h3>
            <p class="text-gray-600 leading-relaxed mb-4">
                تُعلن الهيئة الوطنية للامتحانات نتائج الشهادة الإعدادية عادةً في شهر يوليو، بينما تصدر نتائج الشهادة الثانوية في أغسطس. تحرص منصة نتيجتي على توفير النتائج فور الإعلان الرسمي مع إشعارات فورية عبر قنواتها على واتساب وتيليجرام.
            </p>

            <h3 class="text-xl font-bold text-gray-800 mb-3">الاعتراضات وإعادة التصحيح في ليبيا</h3>
            <p class="text-gray-600 leading-relaxed mb-4">
                يحق لكل طالب تقديم اعتراض على نتيجته خلال الفترة المحددة (عادةً أسبوع إلى أسبوعين بعد الإعلان). تُقدَّم طلبات الاعتراض عبر المديريات التعليمية أو إلكترونياً عبر البوابة الرسمية بعد سداد الرسوم المقررة. تتضمن عملية الاعتراض مراجعة جمع الدرجات وإعادة قراءة الإجابات.
            </p>

            <div class="grid sm:grid-cols-3 gap-4 mb-6">
                <div class="text-center bg-green-50 rounded-2xl p-4 border border-green-100">
                    <div class="text-3xl mb-2"><i class="fa-solid fa-bolt text-yellow-500"></i></div>
                    <p class="font-bold text-gray-800 text-sm">نتائج فورية</p>
                    <p class="text-gray-600 text-xs mt-1">اطلع على نتيجتك في أقل من ثانية</p>
                </div>
                <div class="text-center bg-blue-50 rounded-2xl p-4 border border-blue-100">
                    <div class="text-3xl mb-2"><i class="fa-solid fa-lock text-slate-500"></i></div>
                    <p class="font-bold text-gray-800 text-sm">مصدر رسمي موثوق</p>
                    <p class="text-gray-600 text-xs mt-1">من وزارة التربية مباشرةً</p>
                </div>
                <div class="text-center bg-purple-50 rounded-2xl p-4 border border-purple-100">
                    <div class="text-3xl mb-2"><i class="fa-solid fa-award text-emerald-500"></i>️</div>
                    <p class="font-bold text-gray-800 text-sm">شهادة تقدير مجانية</p>
                    <p class="text-gray-600 text-xs mt-1">للمتفوقين بكبسة زر واحدة</p>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-700 to-black rounded-2xl p-5 mt-4 text-white text-center">
                <p class="font-black text-base mb-1"><i class="fa-solid fa-search text-emerald-500"></i> اختر منطقتك الآن وابحث عن نتيجتك</p>
                <p class="text-green-200 text-sm">نتيجتي.com — بوابة نتائج ليبيا الموثوقة والسريعة</p>
            </div>
        </article>
    </div>
</div>
@endsection
