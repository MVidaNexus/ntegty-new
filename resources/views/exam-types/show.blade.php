@extends('layouts.layout')

@php
    // SEO من إعدادات الشهادة
    $pageTitle = $examType->seo_title ?: "نتيجة {$examType->name_ar} - {$examType->country->name_ar} | نتيجتي";
    $pageDescription = $examType->seo_description ?: "نتيجة {$examType->name_ar} في {$examType->country->name_ar} - البحث بالاسم ورقم الجلوس. منصة نتيجتي لعرض النتائج فور اعتمادها.";
    $pageKeywords = $examType->seo_keywords ?: "نتيجة {$examType->name_ar}, {$examType->country->name_ar}, رقم الجلوس, نتائج الامتحانات";
@endphp

@section('structured_data')
@if(isset($structuredData))
{!! $structuredData !!}
@endif
@endsection

@section('meta')
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $pageKeywords }}">
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8 text-gray-500 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 space-x-reverse">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center hover:text-blue-600">
                    <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    الرئيسية
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('country.show', $examType->country->code) }}" class="mr-1 hover:text-blue-600">{{ $examType->country->name_ar }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="mr-1 text-gray-800 font-semibold">{{ $examType->name_ar }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Title -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">
            نتائج {{ $examType->name_ar }}
        </h1>
        <p class="text-lg text-gray-600">
            اختر المحافظة لعرض النتيجة
        </p>
    </div>

    <!-- Governorates Grid -->
    <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($examType->country->governorates as $governorate)
        <a href="{{ $examType->country->code === 'EG' && $examType->code === 'eg_preparatory' ? route('egypt.governorate.results', $governorate) : ($examType->country->code === 'IQ' ? route('iraq.province.results', $governorate) : '#') }}" 
           class="group block bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 p-6 border border-gray-100 hover:border-emerald-400 text-center">
            
            <!-- Governorate Logo/Icon -->
            <div class="h-24 mx-auto mb-4 flex items-center justify-center">
                @if($governorate->logo_path)
                    <img src="{{ asset('uploads/' . $governorate->logo_path) }}" 
                         alt="{{ $governorate->name_ar }}" 
                         class="max-h-full max-w-full object-contain"
                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center group-hover:bg-emerald-50 transition-colors\'><span class=\'text-2xl font-bold text-emerald-600\'>{{ mb_substr($governorate->name_ar, 0, 2) }}</span></div>';">
                @else
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center group-hover:bg-emerald-50 transition-colors">
                        <span class="text-2xl font-bold text-emerald-600">{{ mb_substr($governorate->name_ar, 0, 2) }}</span>
                    </div>
                @endif
            </div>

            <h3 class="text-lg font-bold text-gray-800 group-hover:text-emerald-600 transition mb-1">
                {{ $governorate->name_ar }}
            </h3>
            
            <span class="text-xs text-gray-500 group-hover:text-emerald-500">
                عرض النتيجة
            </span>
        </a>
        @empty
        <div class="col-span-full text-center py-12 bg-white rounded-xl shadow-sm">
            <p class="text-gray-500 text-lg">لا توجد محافظات مسجلة حالياً</p>
        </div>
        @endforelse
    </div>

    <!-- Content Box from Exam Type Settings -->
    @if($examType->show_content_section && ($examType->content_title || $examType->content_body))
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
</div>
@endsection
