@extends('layouts.layout')

@php
    $meta = [
        'title' => 'صيانة مؤقتة | نتيجتي',
        'description' => 'الموقع قيد الصيانة المؤقتة لتحسين الخدمة ورفع النتائج.',
        'robots' => 'noindex, nofollow'
    ];
@endphp

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-gradient-to-br from-slate-50 via-sky-50 to-blue-50 relative overflow-hidden py-12">
    <!-- Animated Background Shapes -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-sky-200/30 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-200/30 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
            
            <!-- Icon Animation -->
            <div class="mb-8 relative animate-float">
                <div class="inline-block relative">
                    <!-- Background Circle -->
                    <div class="absolute inset-0 bg-gradient-to-br from-sky-500 to-blue-600 rounded-full blur-2xl opacity-20 animate-pulse"></div>
                    
                    <!-- Main Icon Container -->
                    <div class="relative bg-white rounded-full p-8 shadow-2xl border-4 border-sky-100">
                        <div class="relative">
                            <!-- Rotating Circle -->
                            <div class="absolute inset-0 border-4 border-sky-500 border-t-transparent rounded-full animate-spin" style="animation-duration: 3s;"></div>
                            
                            <!-- Maintenance Icon -->
                            <div class="relative">
                                <i class="fa-solid fa-screwdriver-wrench text-7xl md:text-8xl text-sky-600"></i>
                                <div class="absolute -top-2 -right-2 bg-sky-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold animate-pulse">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Code -->
            <div class="mb-6">
                <h1 class="text-8xl md:text-9xl font-black text-transparent bg-clip-text bg-gradient-to-r from-sky-600 via-blue-600 to-indigo-600 mb-2 tracking-tighter leading-none">
                    503
                </h1>
                <div class="flex items-center justify-center gap-2 text-slate-400">
                    <div class="h-px w-12 bg-gradient-to-r from-transparent to-slate-300"></div>
                    <i class="fa-solid fa-gears text-sm"></i>
                    <div class="h-px w-12 bg-gradient-to-l from-transparent to-slate-300"></div>
                </div>
            </div>

            <!-- Error Message -->
            <h2 class="text-2xl md:text-4xl font-black text-slate-800 mb-4 leading-tight">
                الموقع قيد الصيانة المؤقتة!
            </h2>
            <p class="text-slate-600 text-base md:text-lg mb-10 leading-relaxed max-w-xl mx-auto">
                <i class="fa-solid fa-cloud-arrow-up text-sky-500 ml-2"></i>
                نقوم حالياً برفع وتحديث نتائج الامتحانات وتطوير خوادم البوابة لخدمتكم بشكل أفضل. سنعود للعمل خلال دقائق معدودة.
            </p>

            <!-- Telegram Call to Action -->
            <div class="max-w-md mx-auto bg-white/80 backdrop-blur-sm rounded-2xl p-6 md:p-8 shadow-xl border border-slate-200 mb-12">
                <div class="flex items-center justify-center gap-2 mb-4">
                    <i class="fa-solid fa-bullhorn text-sky-600 text-lg"></i>
                    <h3 class="text-lg font-bold text-slate-800">تابع تحديثات النتائج فوراً</h3>
                </div>
                <p class="text-slate-500 text-xs mb-6">
                    اشترك في قناتنا الرسمية على التيليجرام للحصول على إشعارات فور صدور النتائج وعودة الموقع للعمل.
                </p>
                <a href="https://t.me/ntegty" target="_blank"
                   class="group w-full px-8 py-4 bg-gradient-to-r from-sky-500 to-blue-600 text-white rounded-xl font-bold shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3">
                    <i class="fa-brands fa-telegram text-xl group-hover:scale-110 transition-transform"></i>
                    <span>قناة التيليجرام الرسمية</span>
                    <i class="fa-solid fa-chevron-left text-sm group-hover:-translate-x-1 transition-transform"></i>
                </a>
            </div>

        </div>
    </div>
</div>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}
</style>
@endsection
