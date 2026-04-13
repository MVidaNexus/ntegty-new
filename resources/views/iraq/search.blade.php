@extends('layouts.layout')

@section('structured_data')
@if(isset($structuredData))
{!! $structuredData !!}
@endif
@endsection

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

    {{-- Check result service type --}}
    @php
        $serviceType = isset($examType) ? ($examType->result_service_type ?? 'search') : 'search';
    @endphp

    @if($serviceType === 'embed' && isset($examType))
        {{-- Embed/iFrame Mode --}}
        @include('partials.result-embed', ['examType' => $examType, 'title' => $title ?? 'نتيجة السادس الإعدادي'])
    @elseif($serviceType === 'pdf' && isset($examType))
        {{-- PDF Viewer Mode --}}
        @include('partials.result-pdf', ['examType' => $examType, 'title' => $title ?? 'نتيجة السادس الإعدادي'])
    @elseif($serviceType === 'governorate_table' && isset($examType))
        {{-- Governorate Table Mode --}}
        @include('partials.result-governorate-table', ['examType' => $examType, 'title' => $title ?? 'نتيجة السادس الإعدادي'])
    @else
        {{-- Default Search Mode --}}
        <!-- Same search interface as Egypt -->
        <div class="w-full max-w-6xl mx-auto px-3" x-data="searchComponent()">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h1 class="text-2xl md:text-3xl font-black text-center text-gray-800 mb-4 leading-relaxed">
                    @if(isset($governorate))
                        نتيجة السادس الإعدادي في محافظة {{ $governorate->name_ar }} في العراق 2026
                    @else
                        نتيجة السادس الإعدادي في العراق 2026
                    @endif
                </h1>
                
                <!-- Result Timer -->
                <x-result-timer country="iraq" type="preparatory" />
                
                <p class="text-center text-gray-600 mb-8 font-medium">
                    ابحث برقم الجلوس أو الاسم
                </p>

                <!-- Search Form -->
                <form @submit.prevent="search" class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if(isset($showYearFilter) && $showYearFilter && isset($academicYears))
                        <div class="w-full sm:w-1/3">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                السنة الدراسية
                            </label>
                            <select x-model="academicYearId" 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-iraq-500 focus:ring-4 focus:ring-iraq-100 focus:outline-none text-lg transition-all bg-white">
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->year }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="w-full {{ (isset($showYearFilter) && $showYearFilter) ? 'sm:w-2/3' : '' }}">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            رقم الجلوس أو الاسم
                        </label>
                        <input type="text" 
                               x-model="query" 
                               required
                               placeholder="أدخل رقم الجلوس أو الاسم"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-iraq-500 focus:outline-none text-lg">
                    </div>
                </div>

                <button type="submit" 
                        :disabled="loading"
                        class="w-full bg-iraq-600 hover:bg-iraq-700 text-white font-bold py-3 px-6 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading"><i class="fa-solid fa-magnifying-glass"></i> بحث</span>
                    <span x-show="loading">جاري البحث...</span>
                </button>
            </form>
            </div>{{-- End of search box --}}

            <!-- Popular Searches - Outside Search Form -->
    

            <!-- Results (same as Egypt search) -->
            <div x-show="results.length > 0" class="mt-8 space-y-4">
                <h2 class="text-xl font-bold text-gray-800">النتائج:</h2>
                <template x-for="result in results" :key="result.id">
                    <div class="bg-gray-50 rounded-lg p-6 border-2 border-gray-200">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800" x-text="result.student_name"></h3>
                                <p class="text-gray-600">رقم الجلوس: <span x-text="result.seat_number"></span></p>
                                <p class="text-gray-600">المحافظة: <span x-text="result.governorate"></span></p>
                            </div>
                            <div class="text-left">
                                <p class="text-3xl font-bold text-iraq-600" x-text="result.total_score"></p>
                                <p class="text-sm text-gray-600">المجموع</p>
                            </div>
                        </div>

                        <!-- Subjects -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4">
                            <template x-for="(score, subject) in result.subjects" :key="subject">
                                <div class="bg-white rounded p-3 text-center">
                                    <p class="text-sm text-gray-600" x-text="subject"></p>
                                    <p class="text-lg font-bold text-gray-800" x-text="score"></p>
                                </div>
                            </template>
                        </div>

                        <!-- Status -->
                        <div class="flex items-center justify-between">
                            <span class="px-4 py-2 rounded-full text-sm font-semibold"
                                  :class="result.status === 'ناجح' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                  x-text="result.status"></span>
                            
                            <a :href="`/result/${result.id}/print`" 
                               target="_blank"
                               class="text-iraq-600 hover:text-iraq-700 font-semibold">
                                <i class="fa-solid fa-print"></i> طباعة
                            </a>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Error Message -->
            <div x-show="error" 
                 x-text="error" 
                 class="mt-4 p-4 bg-red-50 border-2 border-red-200 rounded-lg text-red-700 text-center">
            </div>
        </div>
    </div>

@push('scripts')
<script>
function searchComponent() {
    return {
        query: @json($seat_number ?? ''),
        academicYearId: localStorage.getItem('academic_year_id') || '{{ request("academic_year_id", $academicYears->first()->id ?? "") }}',
        loading: false,
        results: [],
        error: '',
        
        // Save preferences to localStorage
        savePreferences() {
            localStorage.setItem('academic_year_id', this.academicYearId);
        },
        
        async search() {
            // Save user preferences
            this.savePreferences();
            this.loading = true;
            this.error = '';
            this.results = [];
            
            try {
                const examType = await fetch('/api/exam-types/iraq-sixth').then(r => r.json());
                
                const response = await fetch('{{ route("search") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        query: this.query,
                        exam_type_id: examType.id,
                        governorate_id: {{ $governorate->id ?? 'null' }},
                        academic_year_id: this.academicYearId
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.results = data.results;
                    // Update URL to unique student result page
                    if (this.results.length > 0) {
                        const firstSeat = this.results[0].seat_number;
                        const newUrl = '/iraq/sixth/student/' + firstSeat;
                        window.history.pushState({path: newUrl}, '', newUrl);
                    }
                } else {
                    this.error = data.message;
                }
            } catch (error) {
                this.error = 'حدث خطأ أثناء البحث. يرجى المحاولة مرة أخرى.';
            } finally {
                this.loading = false;
            }
        },
        
        init() {
            if (this.query) {
                this.search();
            }
        }
    }
}
</script>
@endpush
    @endif {{-- End of search service type --}}
    
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
    
    {{-- Governorate Content Section for SEO --}}
    @if(isset($governorate) && $governorate->show_content_section && ($governorate->content_title || $governorate->content_body))
    <div class="w-full max-w-6xl mx-auto mt-8 px-3">
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-lg p-6 md:p-10 border border-green-100">
            @if($governorate->content_title)
            <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-green-800 mb-5 pb-3 border-b-2 border-green-200 flex items-center gap-3">
                <i class="fa-solid fa-map-location-dot text-green-600"></i>
                {{ $governorate->content_title }}
            </h2>
            @endif
            @if($governorate->content_intro)
            <p class="text-green-700 mb-6 text-base md:text-lg leading-relaxed">{{ $governorate->content_intro }}</p>
            @endif
            @if($governorate->content_body)
            <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                        prose-headings:font-bold prose-headings:text-green-800 prose-headings:mt-6 prose-headings:mb-3
                        prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-green-500 prose-h2:pr-4 prose-h2:py-1
                        prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-green-700
                        prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                        prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                        prose-a:text-green-600 prose-a:hover:text-green-700">
                {!! $governorate->content_body !!}
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
