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
</div>
@endsection
