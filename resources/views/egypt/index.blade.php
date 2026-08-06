@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Title -->
    <div class="text-center mb-12">
        <h1 class="text-2xl md:text-4xl lg:text-5xl font-black text-gray-800 mb-4 leading-normal px-2">
            {{ $title }}
        </h1>
        <p class="text-lg text-gray-600">
            اختر نوع الشهادة
        </p>
    </div>

    <!-- Exam Types Grid -->
    <div class="max-w-5xl mx-auto grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Preparatory -->
        <a href="{{ route('egypt.preparatory') }}" 
           class="group block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border-2 border-transparent hover:border-egypt-400">
            <div class="text-5xl mb-4 text-center"><i class="fa-solid fa-book-open text-blue-500"></i></div>
            <h3 class="text-2xl font-bold text-center text-gray-800 group-hover:text-egypt-600 transition mb-2">
                الشهادة الإعدادية
            </h3>
            <p class="text-center text-gray-600">
                جميع المحافظات
            </p>
        </a>

        <!-- Secondary -->
        <a href="{{ route('egypt.secondary') }}" 
           class="group block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border-2 border-transparent hover:border-egypt-400">
            <div class="text-5xl mb-4 text-center"><i class="fa-solid fa-graduation-cap text-red-500"></i></div>
            <h3 class="text-2xl font-bold text-center text-gray-800 group-hover:text-egypt-600 transition mb-2">
                الثانوية العامة
            </h3>
            <p class="text-center text-gray-600">
                بحث موحد
            </p>
        </a>

        <!-- Technical Diplomas -->
        <a href="{{ route('egypt.diplomas.index') }}" class="group block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border-2 border-transparent hover:border-emerald-400">
            <div class="text-5xl mb-4 text-center"><i class="fa-solid fa-helmet-safety text-orange-500"></i></div>
            <h3 class="text-2xl font-bold text-center text-gray-800 group-hover:text-emerald-600 transition mb-2">
                الدبلومات الفنية
            </h3>
            <p class="text-center text-gray-600">
                بحث موحد لجميع الشعب
            </p>
        </a>

        <!-- Azhar Secondary -->
        <a href="{{ route('egypt.azhar.secondary') }}" 
           class="group block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border-2 border-transparent hover:border-amber-400">
            <div class="text-5xl mb-4 text-center"><i class="fa-solid fa-mosque text-amber-600"></i></div>
            <h3 class="text-2xl font-bold text-center text-gray-800 group-hover:text-amber-600 transition mb-2">
                الثانوية الأزهرية
            </h3>
            <p class="text-center text-gray-600">
                بحث موحد
            </p>
        </a>

        <!-- Azhar Preparatory -->
        <a href="{{ route('egypt.azhar.preparatory') }}" 
           class="group block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border-2 border-transparent hover:border-amber-400">
            <div class="text-5xl mb-4 text-center"><i class="fa-solid fa-book-quran text-amber-500"></i></div>
            <h3 class="text-2xl font-bold text-center text-gray-800 group-hover:text-amber-600 transition mb-2">
                الإعدادية الأزهرية
            </h3>
            <p class="text-center text-gray-600">
                بحث موحد
            </p>
        </a>

        <!-- Azhar Primary -->
        <a href="{{ route('egypt.azhar.primary') }}" 
           class="group block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border-2 border-transparent hover:border-amber-400">
            <div class="text-5xl mb-4 text-center"><i class="fa-solid fa-star-and-crescent text-amber-400"></i></div>
            <h3 class="text-2xl font-bold text-center text-gray-800 group-hover:text-amber-600 transition mb-2">
                الابتدائية الأزهرية
            </h3>
            <p class="text-center text-gray-600">
                بحث موحد
            </p>
        </a>
    </div>

    <!-- Country Content Section -->
    @php
        $country = \App\Models\Country::where('code', 'EG')->first();
    @endphp
    
    @if(isset($country) && $country->show_content_section && ($country->content_title || $country->content_body))
    <div class="w-full max-w-6xl mx-auto mt-12 px-3">
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-10 border border-gray-100">
            @if($country->content_title)
            <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-5 pb-3 border-b-2 border-gray-100">
                {{ $country->content_title }}
            </h2>
            @endif
            
            <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                        prose-headings:font-bold prose-headings:text-gray-800 prose-headings:mt-6 prose-headings:mb-3
                        prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-emerald-500 prose-h2:pr-4 prose-h2:py-1
                        prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-emerald-700
                        prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                        prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                        prose-a:text-emerald-600 prose-a:hover:text-emerald-700">
                @if($country->content_intro)
                <div class="text-lg md:text-xl font-medium text-gray-600 mb-6 leading-relaxed">
                    {!! $country->content_intro !!}
                </div>
                @endif
                
                @if($country->content_body)
                {!! $country->content_body !!}
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Rich SEO Article — Egypt Results 700+ words — Updated July 2026 -->
    <div class="max-w-4xl mx-auto mt-16 px-3">
        <article class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 md:p-12">
            <h2 class="text-2xl md:text-3xl font-black text-gray-800 mb-4 border-r-4 border-emerald-500 pr-4">
                نتائج امتحانات مصر 2026 — كل ما تحتاج معرفته قبل ظهور النتيجة
            </h2>
            <p class="text-gray-600 text-base md:text-lg leading-relaxed mb-5">
                يوليو 2026 — الشهر الذي تتوقف عنده قلوب ملايين الأسر المصرية. كل عام، يجلس أكثر من <strong>3.5 مليون طالب وطالبة</strong> أمام شاشاتهم ينتظرون اللحظة التي تظهر فيها النتيجة، تلك الأرقام الصغيرة التي تحمل في طياتها سنوات من الجهد والسهر والتعب. في نتيجتي، نفهم هذه اللحظة تماماً، لذلك نحرص على تقديم النتائج فور إعلانها الرسمي، بشكل واضح وفوري ومجاني.
            </p>

            <div class="bg-gradient-to-r from-emerald-50 to-blue-50 rounded-2xl p-5 mb-7 border border-emerald-100">
                <p class="text-emerald-800 font-bold text-sm mb-1"><i class="fa-solid fa-bullhorn text-emerald-500"></i> آخر تحديث — يوليو 2026</p>
                <p class="text-gray-700 text-sm leading-relaxed">أعلنت وزارة التربية والتعليم عن اكتمال عملية تصحيح امتحانات الشهادة الإعدادية وبدء رصد الدرجات إلكترونياً. النتائج تُعلَن تباعاً على مستوى المحافظات فور اعتمادها من كل كنترول.</p>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-3">الشهادة الإعدادية 2026 — نقطة التحول الأولى</h3>
            <p class="text-gray-600 leading-relaxed mb-4">
                يُقبل للشهادة الإعدادية هذا العام أكثر من مليونَي طالب في <strong>27 محافظة</strong>. اعتمدت الوزارة هذا العام بشكل كامل على التصحيح الإلكتروني عبر الماسح الضوئي بدلاً من التصحيح اليدوي في كثير من المحافظات، مما أسهم في تقليص هامش الخطأ ورفع مستوى الشفافية. الجديد هذا العام أيضاً هو رفع الحد الأدنى لدرجات اللغة العربية والرياضيات، ضمن خطة الوزارة لتطوير المخرجات التعليمية.
            </p>
            <p class="text-gray-600 leading-relaxed mb-6">
                الطالب الناجح أمامه طريق الانتقال لمرحلة الثانوية بثلاثة مسارات: <strong>الثانوية العامة</strong> نحو الجامعة، أو <strong>التعليم الفني</strong> للدبلومات المهنية، أو <strong>الأزهر الشريف</strong> للراغبين في المسار الديني الرفيع.
            </p>

            <h3 class="text-xl font-bold text-gray-800 mb-3">الثانوية العامة 2026 — ماذا تغيّر؟</h3>
            <p class="text-gray-600 leading-relaxed mb-4">
                شهد نظام الثانوية العامة في مصر تحولاً جذرياً منذ 2019 مع تطبيق نظام <strong>التقييم الشامل</strong> المعتمد على امتحانات الفصلين الدراسيين. في 2026، واصلت الوزارة العمل بهذا النظام مع تحسينات جوهرية في بنك الأسئلة؛ إذ باتت أسئلة التفكير الناقد والتطبيقي تحتل نسبة أكبر من الورقة الامتحانية. والجدير بالذكر أن <strong>نسبة النجاح في الثانوية</strong> ارتفعت إلى 76% في 2025، وهو مؤشر إيجابي تسعى الوزارة للتوسع فيه.
            </p>
            <p class="text-gray-600 leading-relaxed mb-6">
                يبحث كثير من الطلاب عن نتيجتهم بالرقم القومي، وهو ما تدعمه منصة نتيجتي بالكامل إلى جانب البحث برقم الجلوس والاسم. تظهر النتيجة كاملةً مع درجات كل مادة على حدة وإجمالي الدرجات والتقدير والشعبة.
            </p>

            <h3 class="text-xl font-bold text-gray-800 mb-3">الدبلومات الفنية — مسار سوق العمل المباشر</h3>
            <p class="text-gray-600 leading-relaxed mb-4">
                يختار قرابة <strong>60% من خريجي الإعدادية</strong> الالتحاق بالتعليم الفني سنوياً. في 2026، أطلقت وزارة التربية منظومة "الدبلوم المزدوج" التي تمنح الطالب شهادة مهنية معتمدة من اتحادات أصحاب الأعمال بجانب الدبلوم الحكومي، مما يرفع فرص توظيفه بشكل ملموس. الدبلومات المتاحة: الصناعي، التجاري، الزراعي، الفندقي، الاجتماعي، وتقنية المعلومات.
            </p>

            <h3 class="text-xl font-bold text-gray-800 mb-3">نتائج الأزهر الشريف 2026</h3>
            <p class="text-gray-600 leading-relaxed mb-4">
                يضم الأزهر الشريف أكثر من <strong>مليوني طالب</strong> في مراحله الدراسية الثلاث (الابتدائية والإعدادية والثانوية) الموزعين على المعاهد الأزهرية في جميع محافظات مصر. يختلف نظام الامتحانات في الأزهر عن التعليم الوزاري؛ إذ يشمل مادتَي التفسير والحديث اللتين تتميزان بطابع حفظي تطبيقي. تُتيح نتيجتي الاستعلام عن نتائج الأزهر للمراحل الثلاث بسهولة ويُسر.
            </p>

            <h3 class="text-xl font-bold text-gray-800 mb-3">نصائح عملية بعد ظهور النتيجة</h3>
            <div class="grid sm:grid-cols-2 gap-4 mb-6">
                <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100">
                    <h4 class="font-bold text-gray-800 mb-2 text-sm flex items-center gap-2"><span><i class="fa-solid fa-circle-check text-emerald-500"></i></span> للطالب الناجح</h4>
                    <ul class="text-gray-600 text-sm space-y-1 leading-relaxed">
                        <li>• سجّل في بوابة التنسيق فور فتحها</li>
                        <li>• احتفظ بنسخة مطبوعة من النتيجة</li>
                        <li>• صمّم شهادة تقدير مجانية من نتيجتي</li>
                        <li>• ابحث عن الدراسة الصيفية للمادة التالية</li>
                    </ul>
                </div>
                <div class="bg-orange-50 rounded-2xl p-4 border border-orange-100">
                    <h4 class="font-bold text-gray-800 mb-2 text-sm flex items-center gap-2"><span><i class="fa-solid fa-thumbtack text-red-500"></i></span> للطالب الراسب</h4>
                    <ul class="text-gray-600 text-sm space-y-1 leading-relaxed">
                        <li>• الدور الثاني يبدأ عادةً في سبتمبر</li>
                        <li>• تقديم الاعتراض خلال 15 يوماً</li>
                        <li>• راجع مواد الرسوب مع مدرس متخصص</li>
                        <li>• لا تيأس — كثيرون تفوقوا في الدور الثاني</li>
                    </ul>
                </div>
            </div>

            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-5 text-white text-center">
                <p class="font-black text-base mb-1"><i class="fa-solid fa-graduation-cap text-blue-600"></i> اختر الشهادة أعلاه وابحث عن نتيجتك الآن</p>
                <p class="text-emerald-100 text-sm">نتيجتي — مجانية | فورية | دقيقة | 24/7</p>
            </div>
        </article>
    </div>

    <!-- كلمات البحث الشائعة -->
    <div class="max-w-4xl mx-auto">
        @include('partials.popular-keywords')
    </div>
</div>
@endsection

