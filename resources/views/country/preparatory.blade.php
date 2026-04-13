@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumbs -->
    @if(isset($breadcrumbs))
    <nav class="mb-6 text-sm">
        <ol class="flex items-center gap-2 text-gray-600">
            @foreach($breadcrumbs as $index => $crumb)
                @if($index > 0)
                    <li><i class="fa-solid fa-chevron-left text-xs mx-2"></i></li>
                @endif
                <li>
                    @if(isset($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="hover:text-blue-600">{{ $crumb['name'] }}</a>
                    @else
                        <span class="text-gray-800 font-semibold">{{ $crumb['name'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
    @endif

    <!-- Page Title -->
    <div class="text-center mb-12">
        <div class="flex items-center justify-center gap-4 mb-4">
            @if($country->flag_path)
                <img src="{{ asset('uploads/' . $country->flag_path) }}" 
                     alt="{{ $country->name_ar }}" 
                     class="w-16 h-12 object-cover rounded shadow-md">
            @endif
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-black text-gray-800 leading-normal px-2">
                {{ $title ?? "نتيجة {$examType->name_ar} في {$country->name_ar}" }}
            </h1>
        </div>
        <p class="text-lg text-gray-600 font-medium mt-2">
            اختر المحافظة لاستعراض النتيجة وتحميل الملفات
        </p>
    </div>

    <!-- Result Timer -->
    <div class="w-full max-w-6xl mx-auto px-3 mb-8">
        <x-result-timer :country="$country->slug" type="preparatory" />
    </div>

    <!-- Governorates Table -->
    <div class="w-full max-w-6xl mx-auto px-3">
        <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-3 py-3 md:px-6 md:py-4 text-right text-xs md:text-sm font-bold text-slate-700">المحافظة</th>
                            <th class="px-3 py-3 md:px-6 md:py-4 text-right text-xs md:text-sm font-bold text-slate-700">الحالة</th>
                            <th class="px-3 py-3 md:px-6 md:py-4 text-center text-xs md:text-sm font-bold text-slate-700">النتيجة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($governorates as $index => $governorate)
                        <tr class="hover:bg-blue-50 transition-colors duration-200 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="px-3 py-3 md:px-6 md:py-5">
                                <div class="flex items-center gap-4">
                                    @if($governorate->logo_path)
                                        <img src="{{ asset('uploads/' . $governorate->logo_path) }}" 
                                             alt="{{ $governorate->name_ar }}" 
                                             class="w-12 h-12 object-contain rounded">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded flex items-center justify-center">
                                            <span class="text-xl font-bold text-blue-600">
                                                {{ mb_substr($governorate->name_ar, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">{{ $governorate->name_ar }}</h3>
                                        <p class="text-sm text-gray-500">{{ $governorate->name_en }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 md:px-6 md:py-5 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $governorate->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }} shadow-sm">
                                    @if($governorate->is_active)
                                        <i class="fa-solid fa-check-circle ml-1"></i> متاحة الآن
                                    @else
                                        <span class="relative flex h-2.5 w-2.5 ml-1">
                                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                                        </span>
                                        انتظار
                                    @endif
                                </span>
                            </td>
                            <td class="px-3 py-3 md:px-6 md:py-5 text-center">
                                @if($governorate->is_active)
                                    <a href="{{ ($country->code === 'EG') ? route('egypt.governorate.results', $governorate->slug) : '#' }}" 
                                       class="inline-flex items-center gap-2 px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span>عرض النتيجة</span>
                                    </a>
                                @else
                                    <button disabled class="inline-flex items-center gap-2 px-6 py-2 bg-gray-100 text-gray-400 font-bold rounded-lg cursor-not-allowed border border-gray-200 shadow-sm opacity-75">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>قريباً</span>
                                    </button>
                                @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-semibold">لا توجد محافظات متاحة حالياً</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-8 bg-blue-50 border-r-4 border-blue-500 rounded-lg p-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h4 class="font-bold text-blue-900 mb-2">ملاحظة هامة</h4>
                    <p class="text-blue-800">
                        يمكنك تحميل نتيجة الشهادة الإعدادية لأي محافظة بصيغة PDF. النتائج متاحة فور إعلانها رسمياً.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Country Content Section for SEO --}}
    @if(isset($country) && $country->show_content_section && ($country->content_title || $country->content_body))
    <div class="w-full max-w-6xl mx-auto mt-8 px-3">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg p-6 md:p-10 border border-blue-100">
            @if($country->content_title)
            <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-5 pb-3 border-b-2 border-blue-200 flex items-center gap-3">
                @if($country->flag_path)
                <img src="{{ asset('uploads/' . $country->flag_path) }}" alt="{{ $country->name_ar }}" class="w-8 h-6 object-cover rounded shadow">
                @else
                <i class="fa-solid fa-flag text-blue-600"></i>
                @endif
                {{ $country->content_title }}
            </h2>
            @endif
            @if($country->content_intro)
            <p class="text-gray-600 mb-6 text-base md:text-lg leading-relaxed">{!! $country->content_intro !!}</p>
            @endif
            @if($country->content_body)
            <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                        prose-headings:font-bold prose-headings:text-gray-800 prose-headings:mt-6 prose-headings:mb-3
                        prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-blue-500 prose-h2:pr-4 prose-h2:py-1
                        prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-blue-700
                        prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                        prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                        prose-a:text-blue-600 prose-a:hover:text-blue-700">
                {!! $country->content_body !!}
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
