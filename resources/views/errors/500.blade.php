@extends('layouts.layout')

@php
    $meta = [
        'title' => 'خطأ في الخادم | نتيجتي',
        'description' => 'عذراً، حدث خطأ غير متوقع في الخادم.',
        'robots' => 'noindex, nofollow'
    ];
@endphp

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-gradient-to-br from-slate-50 via-red-50 to-orange-50 relative overflow-hidden py-12">
    <!-- Animated Background Shapes -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-red-200/30 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-orange-200/30 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
            
            <!-- Icon Animation -->
            <div class="mb-8 relative animate-float">
                <div class="inline-block relative">
                    <!-- Background Circle -->
                    <div class="absolute inset-0 bg-gradient-to-br from-red-500 to-orange-600 rounded-full blur-2xl opacity-20 animate-pulse"></div>
                    
                    <!-- Main Icon Container -->
                    <div class="relative bg-white rounded-full p-8 shadow-2xl border-4 border-red-100">
                        <div class="relative">
                            <!-- Rotating Circle -->
                            <div class="absolute inset-0 border-4 border-red-500 border-t-transparent rounded-full animate-spin" style="animation-duration: 4s;"></div>
                            
                            <!-- 500 Icon -->
                            <div class="relative">
                                <i class="fa-solid fa-server text-7xl md:text-8xl text-red-600"></i>
                                <div class="absolute -top-2 -right-2 bg-amber-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold animate-bounce">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Code -->
            <div class="mb-6">
                <h1 class="text-8xl md:text-9xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-600 via-orange-600 to-amber-600 mb-2 tracking-tighter leading-none">
                    500
                </h1>
                <div class="flex items-center justify-center gap-2 text-slate-400">
                    <div class="h-px w-12 bg-gradient-to-r from-transparent to-slate-300"></div>
                    <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                    <div class="h-px w-12 bg-gradient-to-l from-transparent to-slate-300"></div>
                </div>
            </div>

            <!-- Error Message -->
            <h2 class="text-2xl md:text-4xl font-black text-slate-800 mb-4 leading-tight">
                عذراً، حدث خطأ غير متوقع في السيرفر!
            </h2>
            <p class="text-slate-600 text-base md:text-lg mb-10 leading-relaxed max-w-xl mx-auto">
                <i class="fa-solid fa-circle-info text-blue-500 ml-2"></i>
                يواجه السيرفر ضغطاً حالياً بسبب أعداد الزوار الكبيرة، أو هناك عطل فني مؤقت. يرجى محاولة تحديث الصفحة بعد قليل.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12">
                <a href="{{ route('home') }}" 
                   class="group w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3">
                    <i class="fa-solid fa-house text-lg group-hover:scale-110 transition-transform"></i>
                    <span>العودة للرئيسية</span>
                    <i class="fa-solid fa-arrow-left text-sm group-hover:translate-x-1 transition-transform"></i>
                </a>
                
                <a href="javascript:location.reload()" 
                   class="group w-full sm:w-auto px-8 py-4 bg-white text-slate-700 border-2 border-slate-200 rounded-xl font-bold hover:bg-slate-50 hover:border-red-300 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3 shadow-md">
                    <i class="fa-solid fa-arrow-rotate-right text-lg group-hover:spin transition-transform duration-500"></i>
                    <span>إعادة المحاولة</span>
                </a>
            </div>

            <!-- Quick Links -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 md:p-8 shadow-xl border border-slate-200">
                <div class="flex items-center justify-center gap-2 mb-6">
                    <i class="fa-solid fa-compass text-blue-600 text-xl"></i>
                    <h3 class="text-lg md:text-xl font-bold text-slate-800">روابط سريعة</h3>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ route('certificate.index') }}" 
                       class="group flex items-center gap-3 p-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl border-2 border-amber-200 hover:border-amber-400 hover:shadow-lg transition-all duration-300">
                        <div class="w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-certificate text-white text-xl"></i>
                        </div>
                        <div class="text-right flex-1">
                            <p class="font-bold text-slate-800 text-sm">صمم شهادتك</p>
                            <p class="text-xs text-slate-500">مجاناً وفوراً</p>
                        </div>
                        <i class="fa-solid fa-chevron-left text-amber-600 group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <a href="https://t.me/ntegty" target="_blank"
                       class="group flex items-center gap-3 p-4 bg-gradient-to-br from-sky-50 to-blue-50 rounded-xl border-2 border-sky-200 hover:border-sky-400 hover:shadow-lg transition-all duration-300">
                        <div class="w-12 h-12 bg-sky-500 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fa-brands fa-telegram text-white text-xl"></i>
                        </div>
                        <div class="text-right flex-1">
                            <p class="font-bold text-slate-800 text-sm">قناة التيليجرام</p>
                            <p class="text-xs text-slate-500">تابع أحدث النتائج</p>
                        </div>
                        <i class="fa-solid fa-chevron-left text-sky-600 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
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
