@extends('layouts.layout')

@section('structured_data')
@if(isset($structuredData))
{!! $structuredData !!}
@endif
@endsection

@section('content')
<div class="w-full bg-gradient-to-b from-slate-50 to-white py-6">
    <div class="w-full px-3 max-w-[2000px] mx-auto">
        <!-- Breadcrumbs -->
        @if(isset($breadcrumbs))
        <nav class="mb-4 text-sm">
            <ol class="flex items-center gap-2 text-gray-600">
                @foreach($breadcrumbs as $index => $crumb)
                    @if($index > 0)
                        <li><i class="fa-solid fa-chevron-left text-xs mx-1"></i></li>
                    @endif
                    <li>
                        @if(isset($crumb['url']))
                            <a href="{{ $crumb['url'] }}" class="hover:text-emerald-600 transition-colors">{{ $crumb['name'] }}</a>
                        @else
                            <span class="text-gray-800 font-semibold">{{ $crumb['name'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
        @endif

        <!-- Page Title -->
        <div class="text-center mb-6">
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-black text-gray-800 mb-2 leading-tight">
                {{ $title }}
            </h1>
            <p class="text-base md:text-lg text-gray-600">
                اختر المحافظة لاستعراض النتيجة
            </p>
        </div>

        <!-- Search & Grid Container -->
        <div x-data="{ search: '' }">
            <!-- Search Bar -->
            <div class="max-w-xl mx-auto mb-6">
                <div class="relative group">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400 group-focus-within:text-emerald-500 transition-colors"></i>
                    </div>
                    <input type="text" 
                           x-model="search"
                           placeholder="ابحث عن محافظة..." 
                           class="block w-full pr-12 pl-4 py-3 bg-white border-2 border-gray-200 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-sm"
                    >
                </div>
            </div>

            @php
                $declaredGovernorates = $governorates->where('is_declared', true)->values();
                $pendingGovernorates = $governorates->where('is_declared', false)->values();
            @endphp

            {{-- Declared Governorates Section --}}
            @if($declaredGovernorates->count() > 0)
            <div class="mb-8">
                <!-- Section Header -->
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl shadow-md">
                        <i class="fa-solid fa-circle-check"></i>
                        <span class="font-bold">محافظات تم اعتمادها رسمياً</span>
                        <span class="bg-white/20 px-2 py-0.5 rounded-full text-sm">{{ $declaredGovernorates->count() }}</span>
                    </div>
                    <div class="flex-1 h-px bg-gradient-to-l from-emerald-200 to-transparent"></div>
                </div>

                <!-- Declared Governorates Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5 md:gap-6">
                    @foreach($declaredGovernorates as $governorate)
                        @php
                            $hasLogo = !empty($governorate->logo_path);
                        @endphp
                    <a href="{{ route('egypt.governorate.results', $governorate) }}"
                       x-show="search === '' || '{{ $governorate->name_ar }}'.includes(search)"
                       x-transition:enter="transition ease-out duration-200"
                       x-transition:enter-start="opacity-0 scale-90"
                       x-transition:enter-end="opacity-100 scale-100"
                       class="group relative bg-gradient-to-br from-emerald-50 to-white rounded-2xl p-5 md:p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 overflow-hidden border-2 border-emerald-100 hover:border-emerald-300">
                        
                        <!-- Background Logo Watermark -->
                        @if($hasLogo)
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden">
                            <img 
                                src="{{ asset('uploads/' . $governorate->logo_path) }}"
                                alt=""
                                class="w-full h-full object-cover opacity-[0.06] scale-150"
                            >
                        </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-3 left-3 z-10">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-500 text-white rounded-full text-[10px] font-bold shadow-md">
                                <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                                متاحة
                            </span>
                        </div>

                        <!-- Logo Circle -->
                        <div class="relative z-10 flex justify-center mb-4">
                            <div class="w-20 h-20 md:w-24 md:h-24 rounded-full shadow-lg border-4 border-emerald-200 overflow-hidden group-hover:shadow-xl group-hover:scale-105 transition-all duration-300 bg-white flex items-center justify-center">
                                @if($hasLogo)
                                    <img 
                                        src="{{ asset('uploads/' . $governorate->logo_path) }}" 
                                        alt="شعار {{ $governorate->name_ar }}"
                                        class="w-full h-full object-contain p-2"
                                        loading="lazy"
                                        onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-2xl font-bold text-emerald-600\'>{{ mb_substr($governorate->name_ar, 0, 2) }}</span>';"
                                    >
                                @else
                                    <span class="text-2xl font-bold text-emerald-600">{{ mb_substr($governorate->name_ar, 0, 2) }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Governorate Name -->
                        <h2 class="relative z-10 text-lg md:text-xl font-bold text-slate-800 text-center mb-2 group-hover:text-emerald-600 transition-colors">
                            {{ $governorate->name_ar }}
                        </h2>
                        
                        <!-- View Results Button -->
                        <div class="relative z-10 flex items-center justify-center gap-1.5 text-sm text-emerald-600 font-medium">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>عرض النتيجة</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Pending Governorates Section --}}
            @if($pendingGovernorates->count() > 0)
            <div>
                <!-- Section Header -->
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-gray-400 to-gray-500 text-white rounded-xl shadow-md">
                        <i class="fa-solid fa-hourglass-half"></i>
                        <span class="font-bold">ما زلنا في الانتظار</span>
                        <span class="bg-white/20 px-2 py-0.5 rounded-full text-sm">{{ $pendingGovernorates->count() }}</span>
                    </div>
                    <div class="flex-1 h-px bg-gradient-to-l from-gray-200 to-transparent"></div>
                </div>

                <!-- Pending Governorates Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5 md:gap-6">
                    @foreach($pendingGovernorates as $governorate)
                        @php
                            $hasLogo = !empty($governorate->logo_path);
                        @endphp
                    <a href="{{ route('egypt.governorate.results', $governorate) }}"
                       x-show="search === '' || '{{ $governorate->name_ar }}'.includes(search)"
                       x-transition:enter="transition ease-out duration-200"
                       x-transition:enter-start="opacity-0 scale-90"
                       x-transition:enter-end="opacity-100 scale-100"
                       class="group relative bg-slate-50/80 rounded-2xl p-5 md:p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 overflow-hidden opacity-75 hover:opacity-100">
                        
                        <!-- Background Logo Watermark -->
                        @if($hasLogo)
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden">
                            <img 
                                src="{{ asset('uploads/' . $governorate->logo_path) }}"
                                alt=""
                                class="w-full h-full object-cover opacity-[0.04] scale-150"
                            >
                        </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-3 left-3 z-10">
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-500 text-white rounded-full text-[10px] font-bold shadow-md">
                                <i class="fa-solid fa-clock text-[8px]"></i>
                                انتظار
                            </span>
                        </div>

                        <!-- Logo Circle -->
                        <div class="relative z-10 flex justify-center mb-4">
                            <div class="w-20 h-20 md:w-24 md:h-24 rounded-full shadow-lg border-4 border-white overflow-hidden group-hover:shadow-xl group-hover:scale-105 transition-all duration-300 bg-white flex items-center justify-center">
                                @if($hasLogo)
                                    <img 
                                        src="{{ asset('uploads/' . $governorate->logo_path) }}" 
                                        alt="شعار {{ $governorate->name_ar }}"
                                        class="w-full h-full object-contain p-2 grayscale group-hover:grayscale-0 transition-all"
                                        loading="lazy"
                                        onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-2xl font-bold text-gray-400\'>{{ mb_substr($governorate->name_ar, 0, 2) }}</span>';"
                                    >
                                @else
                                    <span class="text-2xl font-bold text-gray-400">{{ mb_substr($governorate->name_ar, 0, 2) }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Governorate Name -->
                        <h2 class="relative z-10 text-lg md:text-xl font-bold text-slate-600 text-center mb-2 group-hover:text-slate-800 transition-colors">
                            {{ $governorate->name_ar }}
                        </h2>
                        
                        <!-- View Results Button -->
                        <div class="relative z-10 flex items-center justify-center gap-1.5 text-sm text-slate-400">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>قريباً</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- No Results -->
            <div x-show="search !== '' && $el.querySelectorAll('a[style*=\'display: none\']').length === {{ count($governorates) }}" 
                 x-cloak
                 class="text-center py-12 mt-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 rounded-full mb-3">
                    <i class="fa-solid fa-search text-slate-400 text-xl"></i>
                </div>
                <p class="text-slate-500 font-medium">لا توجد محافظة بهذا الاسم</p>
            </div>
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

        <!-- كلمات البحث الشائعة -->
        <div class="max-w-4xl mx-auto">
            @include('partials.popular-keywords')
        </div>
    </div>
</div>
@endsection
