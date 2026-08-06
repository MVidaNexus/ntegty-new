@extends('layouts.layout')

@php
    $meta = [
        'title' => 'سياسة الخصوصية | نتيجتي',
        'description' => 'سياسة الخصوصية لموقع نتيجتي - نحن نحترم خصوصيتك ونحمي بياناتك',
        'og_title' => 'سياسة الخصوصية - نتيجتي',
        'og_description' => 'سياسة الخصوصية لموقع نتيجتي',
    ];
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50 py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-block mb-4">
                <span class="text-6xl"><i class="fa-solid fa-lock"></i></span>
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-gray-800 mb-4 leading-relaxed">
                <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent py-2 inline-block">سياسة الخصوصية</span>
            </h1>
            <p class="text-lg text-gray-600">نحن نحترم خصوصيتك ونحمي بياناتك</p>
            <p class="text-sm text-gray-500 mt-2">آخر تحديث: {{ date('Y/m/d') }}</p>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-2xl border-2 border-blue-100">
            <div class="prose prose-lg max-w-none">
                
                <div class="mb-8">
                    <h2 class="text-2xl font-black text-gray-800 mb-4 flex items-center gap-2">
                        <span class="text-blue-600"><i class="fa-solid fa-clipboard-list"></i></span>
                        <span>مقدمة</span>
                    </h2>
                    <p class="text-gray-700 leading-relaxed">
                        مرحباً بك في منصة نتيجتي (Ntegty.com). نحن ملتزمون بحماية خصوصيتك وبياناتك الشخصية. توضح سياسة الخصوصية هذه كيفية جمع واستخدام وحماية المعلومات التي تقدمها عند استخدام موقعنا.
                    </p>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-black text-gray-800 mb-4 flex items-center gap-2">
                        <span class="text-green-600"><i class="fa-solid fa-chart-simple"></i></span>
                        <span>المعلومات التي نجمعها</span>
                    </h2>
                    <div class="bg-green-50 rounded-xl p-6 mb-4">
                        <h3 class="font-bold text-lg text-gray-800 mb-3">1. معلومات البحث</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-700">
                            <li>رقم الجلوس أو اسم الطالب المستخدم في البحث</li>
                            <li>نوع الامتحان والمحافظة</li>
                            <li>تاريخ ووقت البحث</li>
                        </ul>
                    </div>

                    <div class="bg-blue-50 rounded-xl p-6 mb-4">
                        <h3 class="font-bold text-lg text-gray-800 mb-3">2. معلومات تقنية</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-700">
                            <li>عنوان IP الخاص بك</li>
                            <li>نوع المتصفح والجهاز المستخدم</li>
                            <li>نظام التشغيل</li>
                            <li>الصفحات التي تزورها على موقعنا</li>
                        </ul>
                    </div>

                    <div class="bg-purple-50 rounded-xl p-6">
                        <h3 class="font-bold text-lg text-gray-800 mb-3">3. ملفات تعريف الارتباط والإعلانات (Cookies & AdSense)</h3>
                        <p class="text-gray-700 mb-3">
                            نستخدم ملفات تعريف الارتباط (Cookies) لتحسين تجربة المستخدم وتحليل حركة المرور. كما نستخدم جهات خارجية لتقديم الإعلانات مثل **Google AdSense**.
                        </p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 text-sm">
                            <li>تستخدم شركة Google بصفتها مورداً خارجياً ملفات تعريف الارتباط لخدمة الإعلانات على موقعنا.</li>
                            <li>يسمح استخدام ملف تعريف الارتباط DART لشركة Google وشركائها بتقديم الإعلانات لمستخدمينا بناءً على زيارتهم لموقعنا أو مواقع أخرى على الإنترنت.</li>
                            <li>يمكن للمستخدمين اختيار تعطيل استخدام ملف تعريف الارتباط DART عن طريق زيارة سياسة الخصوصية الخاصة بشبكة Google الإعلانية والمحتوى.</li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-black text-gray-800 mb-4 flex items-center gap-2">
                        <span class="text-purple-600"><i class="fa-solid fa-bullseye"></i></span>
                        <span>كيف نستخدم معلوماتك</span>
                    </h2>
                    <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-6">
                        <ul class="space-y-3 text-gray-700">
                            <li class="flex items-start gap-2">
                                <span class="text-purple-600 font-bold"><i class="fa-solid fa-check"></i></span>
                                <span>توفير خدمة البحث عن النتائج بشكل سريع ودقيق</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-600 font-bold"><i class="fa-solid fa-check"></i></span>
                                <span>تحسين وتطوير خدماتنا وتجربة المستخدم</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-600 font-bold"><i class="fa-solid fa-check"></i></span>
                                <span>تحليل استخدام الموقع لفهم احتياجات المستخدمين</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-600 font-bold"><i class="fa-solid fa-check"></i></span>
                                <span>حماية الموقع من الاستخدام غير المشروع</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-600 font-bold"><i class="fa-solid fa-check"></i></span>
                                <span>الامتثال للمتطلبات القانونية والتنظيمية</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-black text-gray-800 mb-4 flex items-center gap-2">
                        <span class="text-red-600"><i class="fa-solid fa-shield-halved"></i></span>
                        <span>حماية بياناتك</span>
                    </h2>
                    <div class="bg-red-50 rounded-xl p-6">
                        <p class="text-gray-700 mb-4">
                            نتخذ إجراءات أمنية صارمة لحماية معلوماتك الشخصية من الوصول غير المصرح به أو التعديل أو الإفصاح أو الإتلاف:
                        </p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <span class="text-red-600"><i class="fa-solid fa-key"></i></span>
                                <span>تشفير SSL/HTTPS لجميع البيانات المنقولة</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-600"><i class="fa-solid fa-key"></i></span>
                                <span>خوادم آمنة ومحمية بجدران نارية</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-600"><i class="fa-solid fa-key"></i></span>
                                <span>مراقبة مستمرة للأنشطة المشبوهة</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-600"><i class="fa-solid fa-key"></i></span>
                                <span>عدم مشاركة بياناتك مع أطراف ثالثة</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-black text-gray-800 mb-4 flex items-center gap-2">
                        <span class="text-orange-600"><i class="fa-solid fa-users"></i></span>
                        <span>مشاركة المعلومات</span>
                    </h2>
                    <div class="bg-orange-50 rounded-xl p-6">
                        <p class="text-gray-700 font-bold mb-3">نحن لا نبيع أو نؤجر أو نشارك معلوماتك الشخصية مع أطراف ثالثة إلا في الحالات التالية:</p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700">
                            <li>عندما يكون ذلك مطلوباً بموجب القانون</li>
                            <li>لحماية حقوقنا وممتلكاتنا</li>
                            <li>للامتثال لأمر قضائي أو إجراء قانوني</li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-black text-gray-800 mb-4 flex items-center gap-2">
                        <span class="text-teal-600"><i class="fa-solid fa-scale-balanced"></i></span>
                        <span>حقوقك</span>
                    </h2>
                    <div class="bg-teal-50 rounded-xl p-6">
                        <p class="text-gray-700 mb-4">لديك الحق في:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start gap-2">
                                <span class="text-teal-600 font-bold"><i class="fa-solid fa-circle-dot text-[6px]"></i></span>
                                <span>الوصول إلى معلوماتك الشخصية</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-teal-600 font-bold"><i class="fa-solid fa-circle-dot text-[6px]"></i></span>
                                <span>تصحيح أي معلومات غير دقيقة</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-teal-600 font-bold"><i class="fa-solid fa-circle-dot text-[6px]"></i></span>
                                <span>طلب حذف بياناتك</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-teal-600 font-bold"><i class="fa-solid fa-circle-dot text-[6px]"></i></span>
                                <span>الاعتراض على معالجة بياناتك</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-black text-gray-800 mb-4 flex items-center gap-2">
                        <span class="text-indigo-600"><i class="fa-solid fa-rotate"></i></span>
                        <span>التحديثات على سياسة الخصوصية</span>
                    </h2>
                    <div class="bg-indigo-50 rounded-xl p-6">
                        <p class="text-gray-700">
                            قد نقوم بتحديث سياسة الخصوصية هذه من وقت لآخر. سنقوم بإخطارك بأي تغييرات من خلال نشر السياسة الجديدة على هذه الصفحة مع تحديث تاريخ "آخر تحديث" في الأعلى.
                        </p>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl p-8 text-center">
                    <h2 class="text-2xl font-black mb-4"><i class="fa-solid fa-envelope ml-2"></i> اتصل بنا</h2>
                    <p class="mb-4">إذا كان لديك أي أسئلة حول سياسة الخصوصية، يرجى التواصل معنا:</p>
                    <a href="{{ route('contact') }}" class="inline-block bg-white text-blue-600 font-bold px-8 py-3 rounded-full hover:bg-gray-100 transition-all">
                        صفحة اتصل بنا
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
