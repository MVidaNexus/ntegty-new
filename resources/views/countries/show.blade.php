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
            نتائج {{ $country->name_ar }}
        </h1>
        <p class="text-lg text-gray-600">
            اختر نوع الشهادة
        </p>
    </div>

    <!-- Exam Types Grid -->
    <div class="w-full max-w-6xl mx-auto px-3 grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($country->examTypes as $examType)
            @php
                // Dynamic Emoji Mapping
                $emoji = match(true) {
                    str_contains($examType->name_ar, 'إعدادي') => '📚',
                    str_contains($examType->name_ar, 'ثانوي') => '🎓',
                    str_contains($examType->name_ar, 'تجاري') => '💼',
                    str_contains($examType->name_ar, 'صناعي') => '⚙️',
                    str_contains($examType->name_ar, 'زراعي') => '🌾',
                    str_contains($examType->name_ar, 'فندقي') => '🏨',
                    str_contains($examType->name_ar, 'ابتدائي') => '🎒',
                    str_contains($examType->name_ar, 'جامع') => '🏛️',
                    default => '<i class="fa-solid fa-pen-to-square"></i>',
                };
                
                // Dynamic Color Theme based on Country
                $borderColor = match($country->code) {
                    'EG' => 'hover:border-red-600 group-hover:text-red-600',
                    'IQ' => 'hover:border-green-600 group-hover:text-green-600',
                    'SY' => 'hover:border-red-700 group-hover:text-red-700',
                    'SA' => 'hover:border-emerald-600 group-hover:text-emerald-600',
                    default => 'hover:border-blue-600 group-hover:text-blue-600',
                };
            @endphp

        <a href="{{ 
            match(true) {
                ($country->code === 'EG' && $examType->code === 'eg_preparatory') => route('egypt.preparatory'),
                ($country->code === 'EG' && $examType->code === 'eg_secondary') => route('egypt.secondary'),
                ($country->code === 'EG' && str_contains($examType->code, 'diploma')) => route('egypt.diplomas', str_replace('eg_diploma_', '', $examType->code)),
                ($country->code === 'IQ') => route('iraq.index'),
                default => route('exam-type.show', $examType->id)
            }
         }}" 
           class="group block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border-2 border-transparent {{ explode(' ', $borderColor)[0] }}">
            <div class="text-5xl mb-4 text-center">{{ $emoji }}</div>
            <h3 class="text-2xl font-bold text-center text-gray-800 {{ explode(' ', $borderColor)[1] }} transition mb-2">
                {{ $examType->name_ar }}
            </h3>
            <p class="text-center text-gray-600">
                عرض النتائج
            </p>
        </a>
        @empty
        <div class="col-span-full text-center py-12 bg-white rounded-xl shadow-sm">
            <p class="text-gray-500 text-lg">لا توجد شهادات متاحة حالياً في {{ $country->name_ar }}</p>
        </div>
        @endforelse
    </div>
    
    {{-- Country Content Section for SEO --}}
    @if(isset($country) && $country->show_content_section && ($country->content_title || $country->content_body))
    <div class="w-full max-w-6xl mx-auto mt-12 px-3">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg p-6 md:p-10 border border-blue-100">
            @if($country->content_title)
            <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-blue-800 mb-5 pb-3 border-b-2 border-blue-200 flex items-center gap-3">
                <i class="fa-solid fa-globe text-blue-600"></i>
                {{ $country->content_title }}
            </h2>
            @endif
            @if($country->content_intro)
            <p class="text-blue-700 mb-6 text-base md:text-lg leading-relaxed">{{ $country->content_intro }}</p>
            @endif
            @if($country->content_body)
            <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                        prose-headings:font-bold prose-headings:text-blue-800 prose-headings:mt-6 prose-headings:mb-3
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
