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

    <!-- كلمات البحث الشائعة -->
    <div class="max-w-4xl mx-auto">
        @include('partials.popular-keywords')
    </div>
</div>
@endsection
