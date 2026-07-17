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
    <nav class="mb-6 text-sm no-print">
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
        $serviceType = 'search'; // الافتراضي دائماً بحث
        $serviceSource = null;
        
        // أولاً: تحقق من المحافظة (للإعدادية وغيرها)
        if (isset($governorate) && $governorate->result_service_type && $governorate->result_service_type !== 'search') {
            // المحافظة لها إعدادات خاصة (embed أو pdf)
            $serviceType = $governorate->result_service_type;
            $serviceSource = $governorate;
        } 
        // ثانياً: للشهادات الموحدة (الثانوية/الدبلومات) - استخدم إعدادات ExamType مباشرة
        elseif (!isset($governorate) && $examType->result_service_type && $examType->result_service_type !== 'search') {
            // الشهادات الموحدة مثل الثانوية والدبلومات
            $examCode = $examType->code ?? '';
            $isUnifiedExam = str_contains($examCode, 'secondary') || 
                            str_contains($examCode, 'diploma') || 
                            str_contains($examCode, 'azhar') ||
                            str_contains($examCode, 'baccalaureate') ||
                            str_contains($examCode, 'tawjihi');
            
            if ($isUnifiedExam || $examType->result_service_type === 'governorate_table') {
                $serviceType = $examType->result_service_type;
                $serviceSource = $examType;
            }
        }
    @endphp

    @if($serviceType === 'embed' && $serviceSource)
        {{-- Embed/iFrame Mode --}}
        @include('partials.result-embed', ['source' => $serviceSource, 'examType' => $examType, 'governorate' => $governorate ?? null, 'title' => $title])
    @elseif($serviceType === 'pdf' && $serviceSource)
        {{-- PDF Viewer Mode --}}
        @include('partials.result-pdf', ['source' => $serviceSource, 'examType' => $examType, 'governorate' => $governorate ?? null, 'title' => $title])
    @elseif($serviceType === 'governorate_table')
        {{-- Governorate Table Mode --}}
        @include('partials.result-governorate-table', ['examType' => $examType, 'title' => $title])
    @else
        {{-- Default Search Mode --}}
        <!-- Search Section -->
        <div class="w-full max-w-6xl mx-auto px-3" x-data="searchComponent()">
            
            <!-- Result Timer - Above Search Box -->
            <div class="mb-4 no-print">
                <x-result-timer country="egypt" :type="isset($examType) ? $examType->code : 'preparatory'" :governorate="$governorate->slug ?? null" />
            </div>
            
            <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl sm:rounded-3xl shadow-2xl p-5 sm:p-8 md:p-10 border border-blue-100 print:shadow-none print:border-0 print:p-0 print:bg-white">
                <h1 class="text-lg sm:text-xl md:text-2xl font-black text-center text-gray-800 mb-2 leading-tight px-2 no-print">
                    {{ $title ?? $examType->content_title ?? $examType->seo_title ?? ('نتيجة ' . ($certName ?? 'الشهادة الإعدادية')) }}
                </h1>
                
                @if(isset($governorate))
                <p class="text-center text-emerald-600 font-bold text-base sm:text-lg mb-1 no-print">
                    محافظة {{ $governorate->name_ar }}
                </p>
                @endif
                
                <p class="text-center text-gray-500 text-sm mb-4 no-print">
                    <i class="fa-solid fa-calendar-alt ml-1"></i>
                    {{ $suffix ?? '' }}
                </p>

                <p class="text-center text-gray-600 mb-6 sm:mb-8 text-sm sm:text-base px-2 no-print">
                    ابحث برقم الجلوس أو الاسم
                </p>

                {{-- Ad: After Title (Governorate) --}}
                <x-ad-unit slug="gov-after-title" />

                <!-- Search Form -->
                {{-- Ad: Before Search (Governorate) --}}
                <x-ad-unit slug="gov-before-search" />

                <form @submit.prevent="search" class="space-y-4 no-print">
                    @if(isset($examType) && str_contains($examType->code, 'secondary'))
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                    <label class="block text-sm font-bold text-gray-800 mb-3">
                        اختر نظام الدراسة:
                    </label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="radio" x-model="systemType" value="new" class="peer sr-only">
                                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-blue-600 peer-checked:bg-blue-600 transition-all"></div>
                                <div class="absolute w-2 h-2 bg-white rounded-full left-1.5 top-1.5 opacity-0 peer-checked:opacity-100 transition-all"></div>
                            </div>
                            <span class="text-gray-700 font-medium group-hover:text-blue-700 transition-colors">نظام حديث</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="radio" x-model="systemType" value="old" class="peer sr-only">
                                <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-blue-600 peer-checked:bg-blue-600 transition-all"></div>
                                <div class="absolute w-2 h-2 bg-white rounded-full left-1.5 top-1.5 opacity-0 peer-checked:opacity-100 transition-all"></div>
                            </div>
                            <span class="text-gray-700 font-medium group-hover:text-blue-700 transition-colors">نظام قديم</span>
                        </label>
                    </div>
                </div>
                
                {{-- Branch Selection for Secondary (علمي علوم - علمي رياضة - أدبي) --}}
                @if(isset($branches) && $branches->count() > 0)
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-100">
                    <label class="block text-sm font-bold text-gray-800 mb-3">
                        <i class="fa-solid fa-graduation-cap ml-1 text-blue-600"></i>
                        اختر الشعبة:
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach($branches as $branch)
                            <button type="button" 
                                    @click="selectedBranchId = {{ $branch->id }}; selectedBranchCode = '{{ $branch->code }}'"
                                    :class="selectedBranchId === {{ $branch->id }}
                                        ? 'bg-blue-600 text-white border-blue-600 shadow-lg scale-105' 
                                        : 'bg-white text-gray-700 border-gray-200 hover:border-blue-300 hover:bg-blue-50'"
                                    class="flex flex-col items-center justify-center p-3 rounded-xl border-2 transition-all duration-200">
                                <i class="fa-solid {{ $branch->icon ?? 'fa-book' }} text-xl mb-1"></i>
                                <span class="text-sm font-bold">{{ $branch->name_ar }}</span>
                                @if($branch->total_score)
                                    <span class="text-xs opacity-75 mt-1">{{ $branch->total_score }} درجة</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif
                @endif

                <div class="flex flex-col sm:flex-row gap-4">
                    @if(isset($showYearFilter) && $showYearFilter && isset($academicYears))
                    <div class="w-full sm:w-1/3">
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                            السنة الدراسية
                        </label>
                        <select x-model="academicYearId" 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none text-base sm:text-lg transition-all bg-white">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="w-full {{ (isset($showYearFilter) && $showYearFilter) ? 'sm:w-2/3' : '' }}">
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                            رقم الجلوس أو الاسم
                        </label>
                        <input type="text" 
                               x-model="query" 
                               required
                               placeholder="أدخل رقم الجلوس أو الاسم"
                               class="w-full px-4 py-3 sm:px-4 sm:py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none text-base sm:text-lg transition-all">
                    </div>
                </div>

                {{-- Ad: Inside Search (Governorate) --}}
                <x-ad-unit slug="gov-inside-search" />

                <button type="submit" 
                        :disabled="loading"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-xl transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl">
                    <span x-show="!loading" class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="text-base sm:text-lg">بحث عن النتيجة</span>
                    </span>
                    <span x-show="loading" x-cloak class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm sm:text-base">جاري البحث...</span>
                    </span>
                </button>

                <!-- Search Tips - Hidden after search -->
                <div x-show="results.length === 0 && !loading" class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-xl text-sm text-blue-800"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="flex items-center gap-2 mb-2 font-bold">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>تنويهات هامة للبحث:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-blue-700/90 leading-relaxed font-medium">
                        <li>عند البحث بالاسم، يجب كتابة <strong>الاسم ثلاثي</strong> على الأقل.</li>
                        <li>النظام يعالج تلقائياً الاختلافات في (أ/إ/آ) و (ة/هـ) و (ي/ى).</li>
                        <li>يفضل البحث برقم الجلوس للحصول على نتيجة أكثر دقة وسرعة.</li>
                    </ul>
                </div>
            </form>
            </div>{{-- End of search box --}}

            {{-- Ad: After Search (Governorate) --}}
            <x-ad-unit slug="gov-after-search" />

            <!-- Results -->
            <div x-show="results.length > 0" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="mt-6 sm:mt-8 print:mt-0">
                <h2 class="text-xl sm:text-2xl font-black text-gray-800 mb-4 sm:mb-6 flex items-center gap-2 no-print">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                    النتائج
                </h2>
                
                <template x-for="result in results" :key="result.id">
                    <div class="bg-white rounded-2xl p-5 sm:p-8 md:p-10 border-2 border-blue-100 shadow-lg hover:shadow-xl transition-shadow mb-6 sm:mb-8 print:shadow-none print:border-2 print:border-gray-800 print:rounded-none break-inside-avoid">
                        
                        <!-- Student Page Title with Link -->
                        <div class="mb-6 sm:mb-8 pb-5 border-b-2 border-gray-100 no-print">
                            <h2 class="text-xl sm:text-2xl md:text-3xl font-black text-gray-800 text-center leading-relaxed">
                                <template x-if="result.is_secondary">
                                    <a :href="buildUrl('/egypt/secondary/student/' + result.seat_number, {})" 
                                       target="_blank"
                                       class="hover:text-blue-600 transition-colors inline-flex items-center justify-center gap-2 flex-wrap">
                                        <i class="fa-solid fa-graduation-cap text-blue-500"></i>
                                        <span>نتيجة الطالب/ة</span>
                                        <span class="text-blue-600" x-text="result.student_name"></span>
                                        <span>-</span>
                                        <span x-text="result.branch || 'الثانوية العامة'"></span>
                                        <i class="fa-solid fa-external-link text-sm text-gray-400"></i>
                                    </a>
                                </template>
                                <template x-if="!result.is_secondary && result.governorate_slug">
                                    <a :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/' + result.seat_number, {})" 
                                       target="_blank"
                                       class="hover:text-blue-600 transition-colors inline-flex items-center justify-center gap-2 flex-wrap">
                                        <i class="fa-solid fa-award text-emerald-500"></i>
                                        <span>نتيجة الطالب/ة</span>
                                        <span class="text-emerald-600" x-text="result.student_name"></span>
                                        <span>- الشهادة الإعدادية -</span>
                                        <span x-text="'محافظة ' + result.governorate"></span>
                                        <i class="fa-solid fa-external-link text-sm text-gray-400"></i>
                                    </a>
                                </template>
                                <template x-if="!result.is_secondary && !result.governorate_slug">
                                    <span class="inline-flex items-center justify-center gap-2 flex-wrap">
                                        <i class="fa-solid fa-award text-emerald-500"></i>
                                        <span>نتيجة الطالب/ة</span>
                                        <span class="text-emerald-600" x-text="result.student_name"></span>
                                    </span>
                                </template>
                            </h2>
                            <p class="text-center text-sm text-gray-500 mt-2">
                                <span>رقم الجلوس: </span>
                                <strong x-text="result.seat_number"></strong>
                                <span x-show="result.school"> | </span>
                                <span x-show="result.school" x-text="result.school"></span>
                            </p>
                        </div>
                        
                        <!-- Header for Print Only -->
                        <div class="hidden print:block mb-6 border-b-2 border-gray-800 pb-4">
                            <div class="flex items-center justify-between px-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://flagcdn.com/w160/eg.png" class="h-12 w-auto object-contain">
                                    <div class="text-right">
                                        <h2 class="text-lg font-bold text-gray-900 leading-tight">نتيجتي - جمهورية مصر العربية</h2>
                                        <p class="text-xs text-gray-600">{{ $title }}</p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    @foreach(App\Models\Setting::all() as $setting)
                                        @if($setting->key == 'logo')
                                            <img src="{{ asset('uploads/' . $setting->value) }}" class="h-14 w-auto object-contain">
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <!-- Student Info (Right) & Ranks (Left) Layout -->
                        <div class="flex flex-col sm:flex-row gap-5 sm:gap-6 mb-6 sm:mb-8 sm:items-stretch">
                            <!-- Student Info - Right Side (appears first in RTL) -->
                            <div class="flex-1 flex flex-col">
                                <!-- Total Score -->
                                <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-xl p-5 sm:p-6 text-center text-white shadow-md print:bg-none print:bg-white print:border-2 print:border-gray-800 print:text-black print:shadow-none mb-4">
                                    <p class="text-sm sm:text-base font-medium mb-2 opacity-90">المجموع الكلي</p>
                                    <p class="text-5xl sm:text-6xl font-black" x-text="result.total_score"></p>
                                </div>
                                
                                <!-- Student Name & Info -->
                                <div class="bg-gray-50 rounded-xl p-5 sm:p-6 border border-gray-200 print:bg-white print:border-gray-800 flex-1">
                                    <h3 class="text-xl sm:text-2xl md:text-3xl font-black text-gray-800 print:text-black mb-4 flex items-center gap-4">
                                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 print:hidden">
                                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <span class="hidden print:inline text-gray-600 font-medium ml-1">الاسم:</span>
                                        <!-- رابط صفحة النتيجة -->
                                        <template x-if="result.is_secondary">
                                            <a :href="buildUrl('/egypt/secondary/student/' + result.seat_number, {})" target="_blank" class="hover:text-blue-600 hover:underline transition-colors leading-tight" x-text="result.student_name"></a>
                                        </template>
                                        <template x-if="!result.is_secondary && result.governorate_slug">
                                            <a :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/' + (result.academic_year_slug || '2024-2025') + '/' + (result.term_slug || 'term1') + '/' + result.seat_number, {})" target="_blank" class="hover:text-blue-600 hover:underline transition-colors leading-tight" x-text="result.student_name"></a>
                                        </template>
                                        <template x-if="!result.is_secondary && !result.governorate_slug">
                                            <span x-text="result.student_name" class="leading-tight"></span>
                                        </template>
                                    </h3>
                                    
                                    <div class="space-y-3 sm:space-y-4 text-base sm:text-lg text-gray-700">
                                        <div class="flex items-center gap-3 sm:gap-4">
                                            <span class="text-blue-600 text-lg sm:text-xl"><i class="fa-solid fa-hashtag"></i></span>
                                            <span class="font-medium">رقم الجلوس:</span>
                                            <strong class="text-gray-900 text-lg sm:text-xl" x-text="result.seat_number"></strong>
                                        </div>
                                        <!-- الشعبة للثانوية العامة -->
                                        <div class="flex items-center gap-3 sm:gap-4" x-show="result.branch && result.is_secondary">
                                            <span class="text-indigo-600 text-lg sm:text-xl"><i class="fa-solid fa-graduation-cap"></i></span>
                                            <span class="font-medium">الشعبة:</span>
                                            <a :href="buildUrl('/egypt/secondary/' + result.branch_code, {})" 
                                               target="_blank"
                                               class="text-gray-900 font-bold hover:text-indigo-600 hover:underline transition-colors text-base sm:text-lg" 
                                               x-text="result.branch"></a>
                                            <span x-show="result.branch_total_score" class="text-xs sm:text-sm text-gray-500">(<span x-text="result.branch_total_score"></span> درجة)</span>
                                        </div>
                                        <!-- نظام الدراسة للثانوية -->
                                        <div class="flex items-center gap-3 sm:gap-4" x-show="result.system_type_label && result.is_secondary">
                                            <span class="text-cyan-600 text-lg sm:text-xl"><i class="fa-solid fa-book-open"></i></span>
                                            <span class="font-medium">النظام:</span>
                                            <a :href="buildUrl('/egypt/secondary/all', {system_type: result.system_type})" 
                                               target="_blank"
                                               class="text-cyan-700 font-bold text-base sm:text-lg bg-cyan-50 px-3 py-1 rounded-lg border border-cyan-200 hover:bg-cyan-100 hover:border-cyan-400 transition-all inline-flex items-center gap-2" 
                                               x-text="result.system_type_label">
                                            </a>
                                            <!-- زر كشف النظام -->
                                            <a :href="buildUrl('/egypt/secondary/all', {system_type: result.system_type})"
                                               target="_blank"
                                               class="text-xs bg-cyan-500 text-white px-2 py-1 rounded-full hover:bg-cyan-600 transition-all no-print inline-flex items-center gap-1">
                                               <i class="fa-solid fa-list"></i>
                                               <span>كشف الدرجات</span>
                                            </a>
                                        </div>
                                        <div class="flex items-center gap-3 sm:gap-4 flex-wrap" x-show="result.governorate && !result.is_unified">
                                            <span class="text-red-500 text-lg sm:text-xl"><i class="fa-solid fa-location-dot"></i></span>
                                            <span class="font-medium">المحافظة:</span>
                                            <a x-show="result.governorate_slug && !result.is_secondary" 
                                               :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/all', {})"
                                               target="_blank"
                                               class="text-gray-900 font-bold hover:text-red-500 hover:underline transition-colors text-base sm:text-lg" 
                                               x-text="result.governorate"></a>
                                            <strong x-show="!result.governorate_slug || result.is_secondary" class="text-gray-900 text-base sm:text-lg" x-text="result.governorate"></strong>
                                            <!-- أزرار المحافظة -->
                                            <div x-show="result.governorate_slug && !result.is_secondary" class="flex gap-1 no-print">
                                                <a :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/all', {})"
                                                   target="_blank"
                                                   class="text-xs bg-amber-500 text-white px-2 py-1 rounded-full hover:bg-amber-600 transition-all inline-flex items-center gap-1">
                                                   <i class="fa-solid fa-list"></i>كشف
                                                </a>
                                                <a :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/top', {})"
                                                   target="_blank"
                                                   class="text-xs bg-orange-500 text-white px-2 py-1 rounded-full hover:bg-orange-600 transition-all inline-flex items-center gap-1">
                                                   <i class="fa-solid fa-medal"></i>أوائل
                                                </a>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 sm:gap-4 flex-wrap" x-show="result.administration || (result.subjects && (result.subjects['الادارة'] || result.subjects['الإدارة'] || result.subjects['الاداره'] || result.subjects['الإدارة التعليمية'] || result.subjects['EDARA'] || result.subjects['Edara'] || result.subjects['edara']))">
                                            <span class="text-emerald-600 text-lg sm:text-xl"><i class="fa-solid fa-building-columns"></i></span>
                                            <span class="font-medium">الإدارة:</span>
                                            <a x-show="!result.is_secondary && result.governorate_slug" 
                                               :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/all', {search: result.administration || result.subjects?.['الادارة'] || result.subjects?.['الإدارة'] || result.subjects?.['الاداره'] || result.subjects?.['الإدارة التعليمية'] || result.subjects?.['EDARA'] || result.subjects?.['Edara'] || result.subjects?.['edara']})"
                                               target="_blank"
                                               class="text-emerald-700 font-bold text-base sm:text-lg bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-200 hover:bg-emerald-100 hover:border-emerald-400 transition-all inline-flex items-center gap-2" 
                                               x-text="cleanAdminName(result.administration || result.subjects?.['الادارة'] || result.subjects?.['الإدارة'] || result.subjects?.['الاداره'] || result.subjects?.['الإدارة التعليمية'] || result.subjects?.['EDARA'] || result.subjects?.['Edara'] || result.subjects?.['edara'])">
                                            </a>
                                            <strong x-show="result.is_secondary || !result.governorate_slug" class="text-gray-900 text-base sm:text-lg" x-text="cleanAdminName(result.administration || result.subjects?.['الادارة'] || result.subjects?.['الإدارة'] || result.subjects?.['الاداره'] || result.subjects?.['الإدارة التعليمية'] || result.subjects?.['EDARA'] || result.subjects?.['Edara'] || result.subjects?.['edara'])"></strong>
                                            <!-- أزرار الإدارة -->
                                            <div x-show="!result.is_secondary && result.governorate_slug" class="flex gap-1 no-print">
                                                <a :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/all', {search: result.administration || result.subjects?.['الادارة'] || result.subjects?.['الإدارة'] || result.subjects?.['الاداره'] || result.subjects?.['الإدارة التعليمية'] || result.subjects?.['EDARA'] || result.subjects?.['Edara'] || result.subjects?.['edara']})"
                                                   target="_blank"
                                                   class="text-xs bg-emerald-500 text-white px-2 py-1 rounded-full hover:bg-emerald-600 transition-all inline-flex items-center gap-1">
                                                   <i class="fa-solid fa-list"></i>كشف
                                                </a>
                                                <a :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/top', {type: 'admin', name: result.administration || result.subjects?.['الادارة'] || result.subjects?.['الإدارة'] || result.subjects?.['الاداره'] || result.subjects?.['الإدارة التعليمية'] || result.subjects?.['EDARA'] || result.subjects?.['Edara'] || result.subjects?.['edara']})"
                                                   target="_blank"
                                                   class="text-xs bg-green-500 text-white px-2 py-1 rounded-full hover:bg-green-600 transition-all inline-flex items-center gap-1">
                                                   <i class="fa-solid fa-medal"></i>أوائل
                                                </a>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 sm:gap-4 flex-wrap" x-show="result.school || (result.subjects && (result.subjects['المدرسة'] || result.subjects['المدرسه'] || result.subjects['SCHOOL'] || result.subjects['School'] || result.subjects['school']))">
                                            <span class="text-purple-600 text-lg sm:text-xl"><i class="fa-solid fa-school"></i></span>
                                            <span class="font-medium">المدرسة:</span>
                                            <a x-show="!result.is_secondary && result.governorate_slug" 
                                               :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/all', {search: result.school || result.subjects?.['المدرسة'] || result.subjects?.['المدرسه'] || result.subjects?.['SCHOOL'] || result.subjects?.['School'] || result.subjects?.['school']})"
                                               target="_blank"
                                               class="text-purple-700 font-bold text-base sm:text-lg bg-purple-50 px-3 py-1 rounded-lg border border-purple-200 hover:bg-purple-100 hover:border-purple-400 transition-all leading-tight" 
                                               x-text="result.school || result.subjects?.['المدرسة'] || result.subjects?.['المدرسه'] || result.subjects?.['SCHOOL'] || result.subjects?.['School'] || result.subjects?.['school']">
                                            </a>
                                            <strong x-show="result.is_secondary || !result.governorate_slug" class="text-gray-900 text-base sm:text-lg leading-tight" x-text="result.school || result.subjects?.['المدرسة'] || result.subjects?.['المدرسه'] || result.subjects?.['SCHOOL'] || result.subjects?.['School'] || result.subjects?.['school']"></strong>
                                            <!-- أزرار المدرسة -->
                                            <div x-show="!result.is_secondary && result.governorate_slug" class="flex gap-1 no-print">
                                                <a :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/all', {search: result.school || result.subjects?.['المدرسة'] || result.subjects?.['المدرسه'] || result.subjects?.['SCHOOL'] || result.subjects?.['School'] || result.subjects?.['school']})"
                                                   target="_blank"
                                                   class="text-xs bg-purple-500 text-white px-2 py-1 rounded-full hover:bg-purple-600 transition-all inline-flex items-center gap-1">
                                                   <i class="fa-solid fa-list"></i>كشف
                                                </a>
                                                <a :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/top', {type: 'school', name: result.school || result.subjects?.['المدرسة'] || result.subjects?.['المدرسه'] || result.subjects?.['SCHOOL'] || result.subjects?.['School'] || result.subjects?.['school'], admin: result.administration || result.subjects?.['الادارة'] || result.subjects?.['الإدارة'] || result.subjects?.['الاداره'] || result.subjects?.['الإدارة التعليمية'] || result.subjects?.['EDARA'] || result.subjects?.['Edara'] || result.subjects?.['edara']})"
                                                   target="_blank"
                                                   class="text-xs bg-violet-500 text-white px-2 py-1 rounded-full hover:bg-violet-600 transition-all inline-flex items-center gap-1">
                                                   <i class="fa-solid fa-medal"></i>أوائل
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Rank Cards - Left Side -->
                            <div class="flex flex-col gap-3 sm:gap-4 sm:w-2/5" x-show="result.is_secondary || (result.country_rank && result.total_in_country) || (result.branch_rank && result.total_in_branch) || (result.rank && result.total_students && !result.is_unified) || (result.admin_rank && result.total_in_admin) || (result.school_rank && result.total_in_school)">
                                
                                <!-- Rank in Country (للثانوية العامة - حسب النظام) -->
                                <div x-show="result.country_rank && result.total_in_country && result.is_secondary" 
                                     class="bg-gradient-to-br from-red-500 to-rose-600 rounded-xl p-4 sm:p-5 text-white shadow-lg relative overflow-hidden print:bg-none print:bg-white print:border print:border-gray-800 print:text-black print:shadow-none text-center flex-1 flex flex-col justify-center min-h-[90px] sm:min-h-[100px]">
                                    <div class="absolute top-2 right-2 opacity-15">
                                        <i class="fa-solid fa-earth-africa text-4xl sm:text-5xl"></i>
                                    </div>
                                    <p class="text-sm sm:text-base font-bold mb-2 flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-flag"></i>
                                        <span>الترتيب على الجمهورية</span>
                                    </p>
                                    <p class="text-xs text-white/80 mb-1" x-show="result.system_type_label">
                                        (<span x-text="result.system_type_label"></span> - جميع الشعب)
                                    </p>
                                    <p class="text-lg sm:text-2xl font-black leading-tight">
                                        <span x-text="result.country_rank?.toLocaleString()"></span>
                                        <span class="text-xs sm:text-sm opacity-80 mx-1">من</span>
                                        <span x-text="result.total_in_country?.toLocaleString()"></span>
                                    </p>
                                </div>
                                
                                <!-- Rank in Branch (للثانوية العامة - الشعبة) -->
                                <div x-show="result.branch_rank && result.total_in_branch && result.branch" 
                                     class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-4 sm:p-5 text-white shadow-lg relative overflow-hidden print:bg-none print:bg-white print:border print:border-gray-800 print:text-black print:shadow-none text-center flex-1 flex flex-col justify-center min-h-[90px] sm:min-h-[100px]">
                                    <div class="absolute top-2 right-2 opacity-15">
                                        <i class="fa-solid fa-graduation-cap text-4xl sm:text-5xl"></i>
                                    </div>
                                    <p class="text-sm sm:text-base font-bold mb-2 flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-graduation-cap"></i>
                                        <span>الترتيب على الشعبة</span>
                                    </p>
                                    <p class="text-xl sm:text-3xl font-black leading-tight">
                                        <span x-text="result.branch_rank?.toLocaleString()"></span>
                                        <span class="text-xs sm:text-sm opacity-80 mx-1">من</span>
                                        <span x-text="result.total_in_branch?.toLocaleString()"></span>
                                    </p>
                                </div>
                                
                                <!-- Rank in Governorate -->
                                <div x-show="result.rank && result.total_students && !result.is_unified" 
                                     class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-4 sm:p-5 text-white shadow-lg relative overflow-hidden print:bg-none print:bg-white print:border print:border-gray-800 print:text-black print:shadow-none text-center flex-1 flex flex-col justify-center min-h-[90px] sm:min-h-[100px]">
                                    <div class="absolute top-2 right-2 opacity-15">
                                        <i class="fa-solid fa-trophy text-4xl sm:text-5xl"></i>
                                    </div>
                                    <p class="text-sm sm:text-base font-bold mb-2 flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <span>الترتيب على المحافظة</span>
                                    </p>
                                    <p class="text-xl sm:text-3xl font-black leading-tight">
                                        <span x-text="result.rank?.toLocaleString()"></span>
                                        <span class="text-xs sm:text-sm opacity-80 mx-1">من</span>
                                        <span x-text="result.total_students?.toLocaleString()"></span>
                                    </p>
                                </div>
                                
                                <!-- Rank in Administration -->
                                <div x-show="result.admin_rank && result.total_in_admin" 
                                     class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl p-4 sm:p-5 text-white shadow-lg relative overflow-hidden print:bg-none print:bg-white print:border print:border-gray-800 print:text-black print:shadow-none text-center flex-1 flex flex-col justify-center min-h-[90px] sm:min-h-[100px]">
                                    <div class="absolute top-2 right-2 opacity-15">
                                        <i class="fa-solid fa-building-columns text-4xl sm:text-5xl"></i>
                                    </div>
                                    <p class="text-sm sm:text-base font-bold mb-2 flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-building-columns"></i>
                                        <span>الترتيب على الإدارة</span>
                                    </p>
                                    <p class="text-xl sm:text-3xl font-black leading-tight">
                                        <span x-text="result.admin_rank?.toLocaleString()"></span>
                                        <span class="text-xs sm:text-sm opacity-80 mx-1">من</span>
                                        <span x-text="result.total_in_admin?.toLocaleString()"></span>
                                    </p>
                                </div>
                                
                                <!-- Rank in School -->
                                <div x-show="result.school_rank && result.total_in_school" 
                                     class="bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl p-4 sm:p-5 text-white shadow-lg relative overflow-hidden print:bg-none print:bg-white print:border print:border-gray-800 print:text-black print:shadow-none text-center flex-1 flex flex-col justify-center min-h-[90px] sm:min-h-[100px]">
                                    <div class="absolute top-2 right-2 opacity-15">
                                        <i class="fa-solid fa-school text-4xl sm:text-5xl"></i>
                                    </div>
                                    <p class="text-sm sm:text-base font-bold mb-2 flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-school"></i>
                                        <span>الترتيب على المدرسة</span>
                                    </p>
                                    <p class="text-lg sm:text-2xl font-black leading-tight">
                                        <span x-text="result.school_rank?.toLocaleString()"></span>
                                        <span class="text-xs sm:text-sm opacity-80 mx-1">من</span>
                                        <span x-text="result.total_in_school?.toLocaleString()"></span>
                                    </p>
                                </div>
                                
                                <!-- Percentage Box (للإعدادية والثانوية) -->
                                <div x-show="result.total_score && result.exam_total_score" 
                                     class="bg-gradient-to-br from-cyan-500 to-teal-600 rounded-xl p-4 sm:p-5 text-white shadow-lg relative overflow-hidden print:bg-none print:bg-white print:border print:border-gray-800 print:text-black print:shadow-none text-center flex-1 flex flex-col justify-center min-h-[90px] sm:min-h-[100px]">
                                    <div class="absolute top-2 right-2 opacity-15">
                                        <i class="fa-solid fa-percent text-4xl sm:text-5xl"></i>
                                    </div>
                                    <p class="text-sm sm:text-base font-bold mb-2 flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-chart-pie"></i>
                                        <span>النسبة المئوية</span>
                                    </p>
                                    <p class="text-2xl sm:text-3xl font-black leading-tight">
                                        <span x-text="((result.total_score / (result.branch_total_score || result.exam_total_score)) * 100).toFixed(2)"></span><span class="text-base">%</span>
                                    </p>
                                    <p class="text-xs sm:text-sm opacity-75 mt-1">
                                        <span x-text="result.total_score"></span> من <span x-text="result.branch_total_score || result.exam_total_score"></span> درجة
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Subjects Grid -->
                        <div x-show="result.subjects && Object.keys(result.subjects).length > 0" class="mt-6">
                            <!-- المواد التي تضاف للمجموع -->
                            <div class="flex items-center gap-3 mb-4">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                                </svg>
                                <h4 class="font-bold text-gray-800 text-base sm:text-lg">درجات المواد</h4>
                                <span class="text-sm text-gray-500">(تُضاف للمجموع)</span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 mb-5 print:grid-cols-3 print:gap-4">
                                <template x-for="(score, subject) in result.subjects" :key="subject">
                                    <div x-show="!isMetadataField(subject) && !isNegativeScore(subject, result) && !isExcludedSubject(subject, result) && !shouldHideSubject(subject, result.subjects)" 
                                         class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 text-center border-2 border-blue-100 print:bg-white print:border-2 print:border-gray-900 print:p-0 print:rounded-lg print:break-inside-avoid"
                                         :class="isAbsentScore(score, result) ? 'from-red-50 to-orange-50 border-red-200' : ''">
                                        <div class="print:bg-gray-100 print:border-b-2 print:border-gray-900 print:p-2">
                                            <p class="text-sm text-gray-600 font-semibold line-clamp-1 print:text-sm print:text-black print:font-bold print:line-clamp-none" x-text="subject"></p>
                                        </div>
                                        <div class="print:p-3 mt-2">
                                            <p class="text-3xl font-black print:text-3xl print:text-black" 
                                               :class="isAbsentScore(score, result) ? 'text-red-500' : 'text-blue-600'" 
                                               x-text="formatScore(score, result)"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- المواد المستثناة من المجموع -->
                            <template x-if="hasExcludedSubjects(result)">
                                <div class="mt-6 pt-5 border-t border-gray-200">
                                    <div class="flex items-center gap-3 mb-4">
                                        <svg class="w-6 h-6 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        <h4 class="font-bold text-amber-700 text-base sm:text-lg">مواد لا تُضاف للمجموع</h4>
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 print:grid-cols-3 print:gap-4">
                                        <template x-for="(score, subject) in result.subjects" :key="'excluded-' + subject">
                                            <div x-show="!isMetadataField(subject) && !isNegativeScore(subject, result) && isExcludedSubject(subject, result) && !shouldHideSubject(subject, result.subjects)" 
                                                 class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl p-4 text-center border-2 border-amber-200 print:bg-white print:border-2 print:border-amber-400 print:p-0 print:rounded-lg print:break-inside-avoid"
                                                 :class="isAbsentScore(score, result) ? 'from-red-50 to-orange-50 border-red-200' : ''">
                                                <div class="print:bg-amber-50 print:border-b-2 print:border-amber-400 print:p-2">
                                                    <p class="text-sm text-amber-700 font-semibold line-clamp-1 print:text-sm print:text-amber-800 print:font-bold print:line-clamp-none" x-text="subject"></p>
                                                </div>
                                                <div class="print:p-3 mt-2">
                                                    <p class="text-3xl font-black print:text-3xl" 
                                                       :class="isAbsentScore(score, result) ? 'text-red-500' : 'text-amber-600'" 
                                                       x-text="formatScore(score, result)"></p>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Status & Actions -->
                        <div class="pt-8 mt-6 border-t border-gray-200 print:border-gray-800">
                            <!-- Large Status Display -->
                            <div class="flex justify-center mb-8 print:mb-8">
                                <div class="px-12 py-5 rounded-2xl font-black text-2xl sm:text-4xl shadow-md border-2 transform hover:scale-105 transition-transform duration-300 print:shadow-none print:border-4 print:border-gray-900 print:w-full print:text-center print:text-4xl print:py-6"
                                     :class="result.status === 'ناجح' ? 'bg-gradient-to-r from-green-50 to-emerald-50 text-emerald-600 border-emerald-200' : 'bg-gradient-to-r from-red-50 to-pink-50 text-red-600 border-red-200 print:bg-white print:text-black'">
                                    <template x-if="result.status === 'ناجح'">
                                        <span class="flex items-center gap-3">
                                            <i class="fa-solid fa-party-horn print:hidden"></i>
                                            <span>مبروووك! ناجح</span>
                                            <i class="fa-solid fa-star print:hidden animate-pulse"></i>
                                        </span>
                                    </template>
                                    <template x-if="result.status !== 'ناجح'">
                                        <span class="flex items-center gap-2">
                                            <i class="fa-solid fa-heart-crack print:hidden"></i>
                                            <span x-text="result.status || 'غير محدد'"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col items-center justify-center gap-4 no-print">
                                <!-- Print & Certificate Buttons - Side by Side -->
                                <div class="w-full flex flex-col sm:flex-row items-center justify-center gap-3">
                                    <!-- Print Button -->
                                    <button @click="window.print()" 
                                       type="button"
                                       class="w-full sm:w-auto px-6 py-3 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-1 flex items-center justify-center gap-2 group">
                                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                        <span>طباعة النتيجة</span>
                                    </button>

                                    <!-- Certificate Button - Dynamic based on exam type -->
                                    <a :href="buildCertificateUrl(result)" 
                                       target="_blank"
                                       class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-1 flex items-center justify-center gap-2 group relative overflow-hidden">
                                        <span class="absolute top-0 left-0 w-full h-full bg-white/20 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></span>
                                        <i class="fa-solid fa-trophy text-lg group-hover:rotate-12 transition-transform"></i>
                                        <span>تصميم شهادة التقدير</span>
                                    </a>
                                </div>
                                
                                <!-- أزرار أوائل وكشف الثانوية العامة -->
                                <div x-show="result.is_secondary && result.system_type" class="w-full flex flex-col sm:flex-row gap-3">
                                    <a :href="'/egypt/secondary/all?system_type=' + result.system_type + '&top_count=10'"
                                       target="_blank"
                                       class="flex-1 flex flex-col items-center justify-center gap-1 px-5 py-3 bg-gradient-to-r from-yellow-400 to-amber-500 hover:from-yellow-500 hover:to-amber-600 text-amber-900 font-bold rounded-xl transition-all shadow-lg hover:shadow-xl text-sm">
                                       <span class="flex items-center gap-2">
                                           <i class="fa-solid fa-trophy"></i>
                                           <span>🏆 أوائل الثانوية العامة</span>
                                       </span>
                                       <span class="text-xs bg-amber-900/20 px-3 py-1 rounded-full font-black" x-text="'(' + result.system_type_label + ')'"></span>
                                    </a>
                                    <a :href="'/egypt/secondary/all?system_type=' + result.system_type + '&top_count=all'"
                                       target="_blank"
                                       class="flex-1 flex flex-col items-center justify-center gap-1 px-5 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl text-sm">
                                       <span class="flex items-center gap-2">
                                           <i class="fa-solid fa-list-ol"></i>
                                           <span>📋 كشف درجات الثانوية العامة</span>
                                       </span>
                                       <span class="text-xs bg-white/20 px-3 py-1 rounded-full font-black" x-text="'(' + result.system_type_label + ')'"></span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Results Navigation Buttons -->
                            <div class="mt-6 no-print">
                                <!-- Governorate Results (First) -->
                                <div x-show="result.governorate_slug && !result.is_secondary" class="mb-5">
                                    <div class="flex items-center gap-2 mb-3">
                                        <i class="fa-solid fa-location-dot text-amber-600 text-lg sm:text-xl"></i>
                                        <span class="text-sm sm:text-base font-bold text-gray-700">نتائج محافظة <span x-text="result.governorate" class="text-amber-600"></span></span>
                                    </div>
                                    <div class="flex flex-col sm:flex-row gap-3">
                                        <a :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/all', {})"
                                           target="_blank"
                                           class="flex-1 px-4 sm:px-5 py-3 sm:py-4 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-sm sm:text-base font-bold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 text-center">
                                            <i class="fa-solid fa-list flex-shrink-0"></i>
                                            <span class="leading-tight">عرض جميع نتائج محافظة <span x-text="result.governorate"></span></span>
                                        </a>
                                        <a :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/top', {})"
                                           target="_blank"
                                           class="flex-1 px-4 sm:px-5 py-3 sm:py-4 bg-gradient-to-r from-amber-400 to-orange-500 hover:from-amber-500 hover:to-orange-600 text-white text-sm sm:text-base font-bold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 border-2 border-amber-300 text-center">
                                            <i class="fa-solid fa-medal flex-shrink-0"></i>
                                            <span class="leading-tight">أوائل محافظة <span x-text="result.governorate"></span></span>
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Administration Results (Second) -->
                                <div x-show="result.administration && result.governorate_slug && !result.is_secondary" class="mb-5">
                                    <div class="flex items-center gap-2 mb-3">
                                        <i class="fa-solid fa-building-columns text-emerald-600 text-lg sm:text-xl"></i>
                                        <span class="text-sm sm:text-base font-bold text-gray-700">نتائج إدارة <span x-text="cleanAdminName(result.administration)" class="text-emerald-600"></span></span>
                                    </div>
                                    <div class="flex flex-col sm:flex-row gap-3">
                                        <a :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/all', {search: result.administration})"
                                           target="_blank"
                                           class="flex-1 px-4 sm:px-5 py-3 sm:py-4 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white text-sm sm:text-base font-bold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 text-center">
                                            <i class="fa-solid fa-list flex-shrink-0"></i>
                                            <span class="leading-tight">عرض جميع نتائج إدارة <span x-text="cleanAdminName(result.administration)"></span></span>
                                        </a>
                                        <a :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/top', {type: 'admin', name: result.administration})"
                                           target="_blank"
                                           class="flex-1 px-4 sm:px-5 py-3 sm:py-4 bg-gradient-to-r from-emerald-400 to-green-500 hover:from-emerald-500 hover:to-green-600 text-white text-sm sm:text-base font-bold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 border-2 border-emerald-300 text-center">
                                            <i class="fa-solid fa-medal flex-shrink-0"></i>
                                            <span class="leading-tight">أوائل إدارة <span x-text="cleanAdminName(result.administration)"></span></span>
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- School Results (Third) -->
                                <div x-show="result.school && result.governorate_slug && !result.is_secondary" class="mb-5">
                                    <div class="flex items-center gap-2 mb-3 flex-wrap">
                                        <i class="fa-solid fa-school text-purple-600 text-lg sm:text-xl"></i>
                                        <span class="text-sm sm:text-base font-bold text-gray-700">نتائج مدرسة</span>
                                        <span x-text="result.school" class="text-purple-600 text-xs sm:text-sm font-bold"></span>
                                    </div>
                                    <div class="flex flex-col sm:flex-row gap-3">
                                        <a :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/all', {search: result.school})"
                                           target="_blank"
                                           class="flex-1 px-4 sm:px-5 py-3 sm:py-4 bg-gradient-to-r from-purple-500 to-violet-600 hover:from-purple-600 hover:to-violet-700 text-white text-sm sm:text-base font-bold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 text-center">
                                            <i class="fa-solid fa-list flex-shrink-0"></i>
                                            <span class="leading-tight">عرض جميع نتائج مدرسة <span x-text="result.school"></span></span>
                                        </a>
                                        <a :href="buildUrl('/egypt/preparatory/' + result.governorate_slug + '/top', {type: 'school', name: result.school, admin: result.administration})"
                                           target="_blank"
                                           class="flex-1 px-4 sm:px-5 py-3 sm:py-4 bg-gradient-to-r from-purple-400 to-violet-500 hover:from-purple-500 hover:to-violet-600 text-white text-sm sm:text-base font-bold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 border-2 border-purple-300 text-center">
                                            <i class="fa-solid fa-medal flex-shrink-0"></i>
                                            <span class="leading-tight">أوائل مدرسة <span x-text="result.school"></span></span>
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Secondary Results (Branches) -->
                                <div x-show="result.is_secondary && result.branch_code" class="mb-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="fa-solid fa-graduation-cap text-blue-600"></i>
                                        <span class="text-sm font-bold text-gray-700">نتائج الشعبة</span>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <a :href="buildUrl('/egypt/secondary/branch/' + result.branch_code, {})"
                                           target="_blank"
                                           class="flex-1 min-w-[140px] px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-list"></i>
                                            أوائل الشعبة
                                        </a>
                                    </div>
                                </div>
                                
                            </div>

                            <!-- Copy Link Button -->
                            @if(isset($governorate))
                            <div class="mt-4 text-center no-print">
                                <button @click="navigator.clipboard.writeText('{{ url('/egypt/preparatory/' . $governorate->slug) }}/' + (result.academic_year_slug || '{{ $urlAcademicYear ?? '2024-2025' }}') + '/' + (result.term_slug || '{{ $urlTerm ?? 'term1' }}') + '/' + result.seat_number); copied = true; setTimeout(() => copied = false, 3000)" 
                                        x-data="{ copied: false }"
                                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-slate-600 to-slate-700 hover:from-slate-700 hover:to-slate-800 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    <i class="fa-solid fa-link" x-show="!copied"></i>
                                    <i class="fa-solid fa-check" x-show="copied" x-cloak></i>
                                    <span x-text="copied ? 'تم نسخ الرابط بنجاح!' : 'نسخ الرابط المباشر للنتيجة'"></span>
                                    <i class="fa-solid fa-copy" x-show="!copied"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Footer for Print Only -->
                        <div class="hidden print:block text-center mt-6 pt-4 border-t-2 border-gray-800">
                            <p class="text-sm font-bold text-red-600">⚠️ هذا الكشف غير رسمي - قم بمراجعة مدرستك للتأكد من النتيجة ⚠️</p>
                            <p class="text-xs text-gray-500 mt-1">ntegty.com - {{ date('Y-m-d H:i') }}</p>
                        </div>
                        
                        <!-- تنويه للشاشة -->
                        <div class="mt-4 p-3 bg-amber-50 border-2 border-amber-300 rounded-xl text-center no-print">
                            <p class="text-amber-800 font-bold text-sm">
                                <i class="fa-solid fa-triangle-exclamation ml-2"></i>
                                تنبيه: هذه النتيجة غير رسمية - قم بمراجعة مدرستك للتأكد
                            </p>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Error Message -->
            <div x-show="error" 
                 x-cloak
                 class="mt-4 sm:mt-6 p-4 sm:p-5 rounded-xl text-center text-sm sm:text-base no-print"
                 :class="notDeclared ? 'bg-amber-50 border-2 border-amber-300 text-amber-800' : 'bg-red-50 border-2 border-red-200 text-red-700'">
                 
                 <!-- أيقونة مختلفة حسب نوع الرسالة -->
                 <div class="flex flex-col items-center gap-3">
                    <template x-if="notDeclared">
                        <div class="flex items-center gap-2 text-amber-600">
                            <i class="fa-solid fa-clock text-3xl"></i>
                        </div>
                    </template>
                    <template x-if="!notDeclared && error">
                        <div class="flex items-center gap-2 text-red-500">
                            <i class="fa-solid fa-circle-exclamation text-3xl"></i>
                        </div>
                    </template>
                    
                    <span x-text="error" class="font-semibold"></span>
                    
                    <!-- رسالة إضافية للنتيجة غير المعتمدة -->
                    <template x-if="notDeclared">
                        <p class="text-sm text-amber-600 mt-1">
                            <i class="fa-solid fa-info-circle ml-1"></i>
                            تابع موقعنا لمعرفة موعد الإعلان
                        </p>
                    </template>
                 </div>
            </div>
        </div>
    </div>

<style>
@media print {
    /* ===== إعدادات الصفحة ===== */
    @page { 
        margin: 5mm 3mm; /* أعلى/أسفل 5mm - يمين/شمال 3mm */
        size: A4 portrait;
    }
    
    /* ===== إعدادات عامة ===== */
    * { 
        -webkit-print-color-adjust: exact !important; 
        print-color-adjust: exact !important;
        box-sizing: border-box !important;
    }
    
    body, html { 
        background: white !important; 
        margin: 0 !important;
        padding: 0 !important;
        font-size: 10pt !important;
        direction: rtl !important;
    }
    
    /* ===== إخفاء العناصر غير المطلوبة ===== */
    .no-print,
    nav, footer, header, .breadcrumb,
    .fa-party-horn, .fa-stars,
    .absolute.opacity-20,
    .flex.flex-col.gap-4.pt-4,
    .flex.flex-col.gap-4.pt-6,
    .mb-5:has(a[href*="/all"]),
    .mb-5:has(a[href*="/top"]) { 
        display: none !important; 
    }
    
    /* ===== الحاوية الرئيسية ===== */
    .min-h-screen {
        min-height: auto !important;
        padding: 0 !important;
    }
    
    .container { 
        max-width: 100% !important; 
        padding: 0 !important; 
        margin: 0 !important; 
    }
    
    /* ===== البطاقة الرئيسية ===== */
    .bg-white.rounded-2xl {
        padding: 12px !important;
        border: 2px solid #333 !important;
        border-radius: 8px !important;
        box-shadow: none !important;
    }
    
    /* ===== الهيدر (للطباعة فقط) ===== */
    .hidden.print\\:block.mb-6 {
        display: block !important;
        margin-bottom: 10px !important;
    }
    .hidden.print\\:block.mb-6 .border-b-2 {
        padding-bottom: 8px !important;
        border-color: #333 !important;
        margin-bottom: 8px !important;
    }
    .hidden.print\\:block.mb-6 img {
        height: 35px !important;
    }
    .hidden.print\\:block.mb-6 h2 {
        font-size: 12pt !important;
        font-weight: bold !important;
        color: #1a1a1a !important;
    }
    .hidden.print\\:block.mb-6 p {
        font-size: 8pt !important;
        color: #444 !important;
    }
    
    /* ===== قسم المجموع الكلي ===== */
    .print\\:border-2.print\\:border-gray-800 {
        border: 2px solid #333 !important;
        padding: 8px !important;
        border-radius: 6px !important;
        text-align: center !important;
    }
    .print\\:border-2 .text-4xl,
    .print\\:border-2 .text-5xl {
        font-size: 24pt !important;
        font-weight: 900 !important;
        color: #000 !important;
    }
    
    /* ===== بيانات الطالب ===== */
    .bg-gray-50.rounded-xl.p-3 {
        background: #f5f5f5 !important;
        padding: 8px !important;
        border: 1px solid #ccc !important;
        border-radius: 6px !important;
        margin-bottom: 8px !important;
    }
    .bg-gray-50 h3 {
        font-size: 10pt !important;
        font-weight: bold !important;
        margin-bottom: 6px !important;
        color: #333 !important;
    }
    .bg-gray-50 .text-sm {
        font-size: 9pt !important;
        line-height: 1.5 !important;
    }
    .bg-gray-50 strong {
        font-size: 9pt !important;
        color: #000 !important;
    }
    /* إخفاء الأيقونات داخل بيانات الطالب */
    .bg-gray-50 i, .bg-gray-50 svg {
        display: none !important;
    }
    
    /* ===== بوكسات الترتيب ===== */
    .sm\\:w-2\\/5 { 
        display: block !important;
        width: 100% !important;
    }
    .sm\\:w-2\\/5 > div {
        padding: 6px !important;
        margin-bottom: 4px !important;
    }
    .sm\\:w-2\\/5 .text-2xl,
    .sm\\:w-2\\/5 .text-xl {
        font-size: 10pt !important;
    }
    .sm\\:w-2\\/5 .text-xs {
        font-size: 6pt !important;
    }
    /* إظهار أيقونات الترتيب */
    .sm\\:w-2\\/5 i {
        display: inline-block !important;
        font-size: 9pt !important;
    }
    
    /* ===== شبكة درجات المواد ===== */
    .grid.grid-cols-2 { 
        display: grid !important;
        grid-template-columns: repeat(5, 1fr) !important; 
        gap: 2px !important;
        margin: 4px 0 !important;
    }
    .grid.grid-cols-2 > div { 
        padding: 3px 2px !important;
        border: 1px solid #333 !important;
        border-radius: 3px !important;
        background: white !important;
        text-align: center !important;
    }
    .grid.grid-cols-2 .text-2xl { 
        font-size: 10pt !important;
        font-weight: bold !important;
        color: #000 !important;
    }
    .grid.grid-cols-2 .text-xs { 
        font-size: 6pt !important;
        font-weight: bold !important;
        color: #444 !important;
    }
    
    /* ===== قسم مواد لا تضاف للمجموع (مصغر جداً) ===== */
    /* الحاوية الرئيسية */
    .mt-6.pt-5.border-t {
        margin-top: 3px !important;
        padding-top: 3px !important;
        border-top: 1px solid #ccc !important;
    }
    /* العنوان */
    .mt-6.pt-5.border-t .flex.items-center.gap-3 {
        margin-bottom: 2px !important;
        gap: 3px !important;
    }
    .mt-6.pt-5.border-t .flex.items-center.gap-3 svg {
        width: 10px !important;
        height: 10px !important;
    }
    .mt-6.pt-5.border-t .flex.items-center.gap-3 h4 {
        font-size: 7pt !important;
    }
    /* شبكة البوكسات - 5 أعمدة */
    .mt-6.pt-5.border-t .grid {
        grid-template-columns: repeat(5, 1fr) !important;
        gap: 2px !important;
        margin: 0 !important;
    }
    /* البوكسات نفسها */
    .border-amber-200 {
        border: 1px solid #f59e0b !important;
        background: #fffbeb !important;
        padding: 1px !important;
        margin: 0 !important;
        border-radius: 2px !important;
    }
    .border-amber-200 .print\\:bg-amber-50 {
        padding: 1px !important;
        border-bottom: 1px solid #f59e0b !important;
    }
    /* اسم المادة - صغير */
    .border-amber-200 .text-sm {
        font-size: 5pt !important;
        line-height: 1.1 !important;
    }
    /* الدرجة - واضحة مثل المواد الأساسية */
    .border-amber-200 .print\\:p-3 {
        padding: 1px !important;
        margin-top: 0 !important;
    }
    .border-amber-200 .text-3xl {
        font-size: 10pt !important;
        font-weight: bold !important;
    }
    .text-amber-600 {
        color: #d97706 !important;
    }
    
    /* ===== حالة الطالب (ناجح/راسب) - مصغر ===== */
    .px-10.py-4 {
        padding: 2px 8px !important;
        border: 1px solid #333 !important;
        border-radius: 3px !important;
        margin: 2px 0 !important;
        text-align: center !important;
    }
    .px-10.py-4 span {
        font-size: 9pt !important;
        font-weight: bold !important;
    }
    
    /* ===== الفوتر - إخفاء تاريخ الاستخراج ===== */
    .hidden.print\\:block.text-center.mt-6 {
        display: block !important;
        margin-top: 0 !important;
        padding-top: 0 !important;
        border-top: none !important;
        text-align: center !important;
    }
    .hidden.print\\:block.text-center .text-sm {
        font-size: 6pt !important;
        color: #333 !important;
        margin-bottom: 0 !important;
    }
    /* إخفاء تاريخ الاستخراج */
    .hidden.print\\:block.text-center .text-xs {
        display: none !important;
    }
    
    /* ===== تعديلات التخطيط ===== */
    .sm\\:flex-row {
        flex-direction: row !important;
    }
    .flex-1.flex.flex-col {
        width: auto !important;
    }
    
    /* ===== المسافات ===== */
    .mb-3, .mb-4 { margin-bottom: 6px !important; }
    .mb-5, .mb-6 { margin-bottom: 8px !important; }
    .gap-4 { gap: 6px !important; }
    .p-3 { padding: 6px !important; }
    .p-4, .p-5 { padding: 8px !important; }
    
    /* ===== منع تقسيم الصفحة ===== */
    .break-inside-avoid,
    .result-card { 
        break-inside: avoid !important; 
        page-break-inside: avoid !important; 
    }
}
</style>
@push('scripts')
<script>
function searchComponent() {
    return {
        query: @json($seat_number ?? ''),
        systemType: localStorage.getItem('eg_secondary_system_type') || 'new',
        selectedBranchId: parseInt(localStorage.getItem('eg_secondary_branch_id')) || null,
        selectedBranchCode: localStorage.getItem('eg_secondary_branch_code') || '',
        academicYearId: '{{ $selectedAcademicYearId ?? '' }}' || localStorage.getItem('academic_year_id') || '{{ \App\Models\AcademicYear::where("is_active", true)->value("id") }}',
        loading: false,
        results: [],
        error: '',
        notDeclared: false, // هل النتيجة غير معتمدة بعد
        defaultAbsentMarkers: @json(\App\Models\ExamType::DEFAULT_ABSENT_MARKERS),
        // Metadata fields that should NOT have absent marker styling applied AND should be hidden from subjects grid
        metadataFields: ['الإدارة', 'الادارة', 'الاداره', 'الإداره', 'الإدارة التعليمية', 'اسم الإدارة', 'اسم الاداره', 'اسم الادارة', 'المدرسة', 'المدرسه', 'اسم المدرسة', 'اسم المدرسه', 'الاسم', 'رقم الجلوس', 'المجموع', 'المجموع الكلي', 'الحالة', 'الترتيب', 'EDARA', 'SCHOOL', 'Edara', 'School', 'edara', 'school', 'NAME', 'SEAT_NUMBER', 'TOTAL'],
        
        // Check if a subject should be hidden (e.g., مجموع الرياضيات when جبر/هندسة exist)
        shouldHideSubject(fieldName, subjects) {
            if (!subjects) return false;
            const fieldLower = fieldName.toLowerCase();
            // Hide مجموع الرياضيات if جبر or هندسة exists
            if (fieldLower.includes('رياضيات') || fieldLower.includes('الرياضيات')) {
                const hasAlgebra = subjects['الجبر'] || subjects['جبر'] || subjects['Algebra'];
                const hasGeometry = subjects['الهندسة'] || subjects['هندسة'] || subjects['الهندسه'] || subjects['Geometry'];
                if (hasAlgebra || hasGeometry) {
                    return true;
                }
            }
            return false;
        },
        
        // Build URL with academic year parameter
        buildUrl(basePath, params = {}) {
            // Add academic year to params
            params.academic_year_id = this.academicYearId;
            
            // Build query string
            const queryString = Object.entries(params)
                .filter(([key, value]) => value !== null && value !== undefined && value !== '')
                .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
                .join('&');
            
            return queryString ? `${basePath}?${queryString}` : basePath;
        },
        
        // Check if a field is a metadata field (not a subject)
        isMetadataField(fieldName) {
            return this.metadataFields.includes(fieldName);
        },
        
        // Normalize subject name for comparison (remove newlines and extra spaces)
        normalizeSubjectName(name) {
            if (!name) return '';
            return name.replace(/[\r\n\t]+/g, ' ').replace(/\s+/g, ' ').trim();
        },
        
        // Check if score is negative (should be hidden completely)
        isNegativeScore(subject, result) {
            if (!result || !result.subjects || result.subjects[subject] === undefined) return false;
            const score = result.subjects[subject];
            if (typeof score === 'number' && score < 0) {
                return true;
            }
            // Handle string numbers like "-4"
            if (typeof score === 'string' && !isNaN(parseFloat(score)) && parseFloat(score) < 0) {
                return true;
            }
            return false;
        },
        
        // Check if subject is excluded from total (by name only - NOT by negative score)
        isExcludedSubject(subject, result) {
            if (!result) return false;
            
            // Negative scores are now hidden completely, not shown in excluded section
            // So we don't check for negative scores here anymore
            
            // Check by excluded_subjects list
            if (!result.excluded_subjects) return false;
            const normalizedSubject = this.normalizeSubjectName(subject);
            // Check exact match after normalization
            for (const excluded of result.excluded_subjects) {
                const normalizedExcluded = this.normalizeSubjectName(excluded);
                // Exact match after normalization
                if (normalizedSubject === normalizedExcluded) {
                    return true;
                }
                // Check if subject starts with excluded name (for variants like "التربية الدينية ترم أول")
                if (normalizedSubject.startsWith(normalizedExcluded + ' ') || 
                    normalizedExcluded.startsWith(normalizedSubject + ' ')) {
                    return true;
                }
            }
            return false;
        },
        
        // Check if result has any excluded subjects (by name only - NOT by negative score)
        hasExcludedSubjects(result) {
            if (!result || !result.subjects) return false;
            for (const subject of Object.keys(result.subjects)) {
                // Only check excluded by name, not by negative score (those are hidden completely)
                if (!this.isMetadataField(subject) && !this.isNegativeScore(subject, result) && this.isExcludedSubject(subject, result)) {
                    return true;
                }
            }
            return false;
        },
        
        // Check if score is absent marker (غ, غائب, etc.) - only for subject scores
        isAbsentScore(score, result = null) {
            if (score === null || score === undefined) return false;
            const scoreStr = String(score).trim();
            const scoreLower = scoreStr.toLowerCase();
            
            // Use result's absent_markers if available, otherwise use defaults
            const markers = (result && result.absent_markers) ? result.absent_markers : this.defaultAbsentMarkers;
            
            for (const marker of markers) {
                if (scoreLower === marker.toLowerCase().trim()) {
                    return true;
                }
            }
            // Check if starts with غ only for short values (5 chars or less - likely absence markers not names)
            if (scoreStr.length <= 5 && scoreStr.startsWith('غ')) {
                return true;
            }
            return false;
        },
        
        // Format score for display
        formatScore(score, result = null) {
            if (score === null || score === undefined || score === '') return '-';
            const scoreStr = String(score).trim();
            if (this.isAbsentScore(scoreStr, result)) {
                return 'غائب';
            }
            // If negative score, show absolute value (the actual score without the minus)
            const numScore = parseFloat(scoreStr);
            if (!isNaN(numScore) && numScore < 0) {
                return Math.abs(numScore).toString();
            }
            return scoreStr;
        },
        
        // Clean administration name (remove prefix like "ادارة")
        cleanAdminName(name) {
            if (!name) return '';
            // Remove common prefixes
            return name.replace(/^(ادارة|إدارة|اداره|إداره)\s*/i, '').trim();
        },
        
        // Save preferences to localStorage
        savePreferences() {
            localStorage.setItem('academic_year_id', this.academicYearId);
            localStorage.setItem('eg_secondary_system_type', this.systemType);
            if (this.selectedBranchId) {
                localStorage.setItem('eg_secondary_branch_id', this.selectedBranchId);
                localStorage.setItem('eg_secondary_branch_code', this.selectedBranchCode);
            }
        },
        
        async search() {
            this.loading = true;
            this.error = '';
            this.notDeclared = false;
            this.results = [];
            
            // Save user preferences
            this.savePreferences();
            
            try {
                @php
                    $isSecondary = isset($examType) && str_contains($examType->code, 'secondary');
                    $hasBranches = isset($branches) && $branches->count() > 0;
                @endphp
                
                const requestBody = {
                    query: this.query,
                    exam_type_id: {{ $examType->id ?? 'null' }},
                    governorate_id: {{ isset($governorate) ? $governorate->id : 'null' }},
                    academic_year_id: this.academicYearId
                };
                
                @if($isSecondary)
                requestBody.system_type = this.systemType;
                @if($hasBranches)
                if (this.selectedBranchId) {
                    requestBody.branch_id = this.selectedBranchId;
                    requestBody.branch = this.selectedBranchCode;
                }
                @endif
                @endif
                
                const response = await fetch('{{ route("search") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(requestBody)
                });
                
                const data = await response.json();
                

                if (data.results && data.results.length > 0) {
                    this.results = data.results;
                    
                    // Update URL to unique student result page
                    if (this.results.length > 0) {
                        const firstResult = this.results[0];
                        const firstSeat = firstResult.seat_number;
                        const academicYearSlug = firstResult.academic_year_slug || '2024-2025';
                        const termSlug = firstResult.term_slug || 'term1';
                        let newUrl = '';
                        
                        @if(isset($examType) && str_contains($examType->code, 'secondary'))
                        // For Secondary - use /egypt/secondary/student/{seat_number}
                        newUrl = '/egypt/secondary/student/' + firstSeat;
                        @elseif(isset($governorate))
                        // For Preparatory - use new format /egypt/preparatory/{governorate}/{year}/{term}/{seat}
                        newUrl = '/egypt/preparatory/{{ $governorate->slug }}/' + academicYearSlug + '/' + termSlug + '/' + firstSeat;
                        @endif
                        
                        if (newUrl) {
                            window.history.pushState({path: newUrl}, '', newUrl);
                        }
                    }
                } else {
                    // استخدام الرسالة من الـ API
                    this.error = data.message || 'لم يتم العثور على نتيجة';
                    this.notDeclared = data.not_declared || false;
                }
            } catch (error) {
                console.error('Search error:', error);
                this.error = 'حدث خطأ أثناء البحث. يرجى المحاولة مرة أخرى.';
            } finally {
                this.loading = false;
            }
        },
        
        buildCertificateUrl(result) {
            const baseUrl = '{{ route("certificate.index") }}';
            const params = new URLSearchParams();
            
            // Student name
            params.set('name', result.student_name || '');
            
            // Score
            params.set('score', result.total_score || '');
            
            // Max score (from branch or exam type)
            params.set('max', result.branch_total_score || result.exam_total_score || '');
            
            // School
            const school = result.subjects?.['المدرسة'] || result.subjects?.['المدرسه'] || result.governorate || 'مصر';
            params.set('school', school);
            
            // Exam type - dynamic based on is_secondary
            let examTypeName = '';
            if (result.is_secondary) {
                examTypeName = 'الثانوية العامة';
                if (result.branch) {
                    examTypeName += ' - ' + result.branch;
                }
            } else {
                examTypeName = 'الشهادة الإعدادية';
                if (result.governorate) {
                    examTypeName += ' - ' + result.governorate;
                }
            }
            examTypeName += ' - {{ $egypt->academic_year ?? "" }}';
            @if(isset($egypt) && $egypt->semester)
            examTypeName += ' - {{ $egypt->semester }}';
            @endif
            params.set('type', examTypeName);
            
            // Governorate
            params.set('governorate', result.governorate || '');
            
            // Status
            params.set('status', result.status || '');
            
            // Percentage for secondary
            if (result.is_secondary && result.total_score && (result.branch_total_score || result.exam_total_score)) {
                const percentage = ((result.total_score / (result.branch_total_score || result.exam_total_score)) * 100).toFixed(2);
                params.set('percentage', percentage);
            }
            
            // Seat number
            params.set('seat', result.seat_number || '');
            
            return baseUrl + '?' + params.toString();
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
    
    {{-- Governorate Content Section for SEO (First) --}}
    @if(isset($governorate) && $governorate->show_content_section && ($governorate->content_title || $governorate->content_body))
    <div class="w-full max-w-6xl mx-auto mt-8 px-3 no-print">
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-10 border border-gray-100 overflow-hidden">
            @if($governorate->content_title)
            <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-5 pb-3 border-b-2 border-gray-100">{{ $governorate->content_title }}</h2>
            @endif
            @if($governorate->content_intro)
            <p class="text-gray-600 mb-6 text-base md:text-lg leading-relaxed">{{ $governorate->content_intro }}</p>
            @endif
            @if($governorate->content_body)
            {{-- Isolated content wrapper with scoped styles --}}
            <article class="gov-content-body">
                {!! $governorate->getFormattedContentBody() !!}
            </article>
            <style>
                .gov-content-body { color: #374151; line-height: 1.6; font-size: 1rem; }
                .gov-content-body * { max-width: 100%; box-sizing: border-box; }
                .gov-content-body h1, .gov-content-body h2, .gov-content-body h3, .gov-content-body h4, .gov-content-body h5, .gov-content-body h6 { font-weight: 700; color: #1f2937; margin-top: 1.25rem; margin-bottom: 0.5rem; line-height: 1.4; }
                .gov-content-body h2 { font-size: 1.375rem; border-right: 4px solid #10b981; padding-right: 0.75rem; }
                .gov-content-body h3 { font-size: 1.125rem; color: #047857; }
                .gov-content-body p { margin-bottom: 0.5rem; line-height: 1.6; }
                .gov-content-body ul, .gov-content-body ol { margin: 0.25rem 0 0.5rem 0; padding-right: 1.5rem; padding-left: 0; list-style-position: inside; }
                .gov-content-body ul { list-style-type: disc; }
                .gov-content-body ol { list-style-type: decimal; }
                .gov-content-body li { margin: 0; padding: 0.1rem 0; line-height: 1.5; display: list-item; }
                .gov-content-body li::marker { color: #10b981; font-weight: bold; }
                .gov-content-body li p { margin: 0; padding: 0; display: inline; }
                .gov-content-body li br, .gov-content-body ol br, .gov-content-body ul br { display: none !important; }
                .gov-content-body strong { font-weight: 700; color: #111827; }
                .gov-content-body a { color: #059669; text-decoration: underline; }
                .gov-content-body table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.95rem; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
                .gov-content-body table th, .gov-content-body table td { border: 1px solid #e5e7eb; padding: 0.5rem 0.75rem; text-align: right; }
                .gov-content-body table th { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 700; }
                .gov-content-body table tbody tr:nth-child(even) { background-color: #f9fafb; }
                .gov-content-body table tbody tr:hover { background-color: #ecfdf5; }
                .gov-content-body table br { display: none !important; }
                .gov-content-body br + br { display: none; }
                .gov-content-body > br:first-child { display: none; }
            </style>
            @endif
        </div>
    </div>
    @endif
    
    {{-- Internal Linking Section for All Egyptian Governorates (SEO Optimized) --}}
    @if(isset($governorate))
        @include('partials.governorates-internal-links', ['currentGovernorateSlug' => $governorate->slug ?? null])
    @endif
    
    {{-- Content Section for SEO (Second) - Hidden for Preparatory & Secondary certificate --}}
    @if(isset($examType) && $examType->show_content_section && ($examType->content_title || $examType->content_body) && !str_contains($examType->code ?? '', 'preparatory') && ($examType->code ?? '') !== 'eg_secondary')
    <div class="w-full max-w-6xl mx-auto mt-8 px-3">
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

    {{-- Secondary Custom Rich SEO Article — Updated July 2026 (1000+ words) --}}
    @if(isset($examType) && $examType->code === 'eg_secondary')
    <div class="max-w-4xl mx-auto mt-14 px-3 no-print">
        <article class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 md:p-12">
            <h2 class="text-2xl md:text-3xl font-black text-gray-800 mb-5 border-r-4 border-blue-600 pr-4">
                دليل نتيجة الثانوية العامة 2026 — استعلم عن نتيجتك فوراً برقم الجلوس والاسم
            </h2>

            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-5 mb-8">
                <p class="text-blue-800 font-bold text-base mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-bullhorn text-blue-600"></i>
                    تحديث هام — تموز/يوليو 2026
                </p>
                <p class="text-blue-700 text-sm leading-relaxed">
                    منصة نتيجتي تتيح لك الحصول على نتيجة الشهادة الثانوية العامة للعام الدراسي 2025/2026 فور اعتمادها رسمياً من وزير التربية والتعليم والتعليم الفني. سنقوم بتوفير قواعد البيانات والروابط المباشرة للاستعلام بالاسم ورقم الجلوس بدقة متناهية وسرعة فائقة.
                </p>
            </div>

            <p class="text-gray-600 text-base md:text-lg leading-relaxed mb-6">
                تُعتبر امتحانات الثانوية العامة في مصر بمثابة محطة مصيرية هامة ترسم ملامح المستقبل الأكاديمي والمهني لعشرات الآلاف من الطلاب وأسرهم. ولهذا، يزداد البحث بشكل مكثف عن <strong>نتيجة الثانوية العامة</strong> مع انتهاء امتحانات العام الدراسي الحالي. في بوابة نتيجتي، نسعى جاهدين لتبسيط عملية الاستعلام وتقليل الضغط النفسي على الطلاب، من خلال تقديم نتائج رسمية ومطابقة 100% لوزارة التربية والتعليم والتعليم الفني، مجاناً وبدون أي تعقيدات.
            </p>

            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-blue-500"></i>
                خطوات الاستعلام عن نتيجة الثانوية العامة 2026 عبر نتيجتي
            </h3>
            <p class="text-gray-600 leading-relaxed mb-4">
                نوفر لك نظام بحث مرن يتيح الوصول للنتيجة بعدة طرق لضمان راحتك. اتبع الخطوات البسيطة التالية:
            </p>
            <ol class="list-decimal pr-6 text-gray-600 leading-relaxed mb-8 space-y-2">
                <li>قم بالصعود إلى أعلى هذه الصفحة حيث توجد أداة الاستعلام المخصصة لجمهورية مصر العربية.</li>
                <li>اختر نظام الدراسة الخاص بك (سواء كنت طالباً في <strong>النظام الحديث</strong> أو <strong>النظام القديم</strong>).</li>
                <li>اختر الشعبة الدراسية المناسبة لك (<strong>علمي علوم</strong>، <strong>علمي رياضة</strong>، أو <strong>أدبي</strong>) لتسهيل الفرز.</li>
                <li>أدخل <strong>رقم الجلوس</strong> الخاص بك في حقل البحث بشكل صحيح، أو اكتب <strong>اسم الطالب كاملاً</strong> للبحث بالاسم.</li>
                <li>اضغط على زر <strong class="text-blue-600">"بحث عن النتيجة"</strong> لتظهر لك بطاقة درجاتك مفصلة بمجموعك الكلي ونسبتك المئوية وترتيبك.</li>
            </ol>

            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2 flex-wrap">
                <i class="fa-solid fa-list-check text-blue-500"></i>
                توزيع درجات مواد الثانوية العامة 2026 (الدرجات الصغرى والكبرى)
            </h3>
            <p class="text-gray-600 leading-relaxed mb-4">
                تتوزع درجات الشهادة الثانوية العامة بإجمالي <strong>410 درجات</strong> للمواد الأساسية التي تضاف للمجموع. وفيما يلي جدول مفصل يوضح الدرجات الكبرى وحد النجاح (الدرجة الصغرى) لكل مادة دراسية للشعبتين العلمية والأدبية:
            </p>

            <div class="overflow-x-auto mb-8">
                <table class="w-full text-sm text-gray-600 border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                    <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold">
                        <tr>
                            <th class="p-3 text-right">المادة</th>
                            <th class="p-3 text-center">الدرجة الكبرى</th>
                            <th class="p-3 text-center">الدرجة الصغرى (النجاح)</th>
                            <th class="p-3 text-right">ملاحظة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="bg-blue-50/50 font-bold"><td colspan="4" class="p-2 text-blue-800">أولاً: المواد المشتركة (لكافة الشعب)</td></tr>
                        <tr><td class="p-3">اللغة العربية</td><td class="p-3 text-center font-semibold">80</td><td class="p-3 text-center">40</td><td class="p-3 text-xs text-gray-500">تضاف للمجموع</td></tr>
                        <tr class="bg-gray-50"><td class="p-3">اللغة الأجنبية الأولى (الإنجليزية)</td><td class="p-3 text-center font-semibold">50</td><td class="p-3 text-center">25</td><td class="p-3 text-xs text-gray-500">تضاف للمجموع</td></tr>
                        <tr><td class="p-3">اللغة الأجنبية الثانية (فرنسي، ألماني، إلخ)</td><td class="p-3 text-center font-semibold">40</td><td class="p-3 text-center">20</td><td class="p-3 text-xs text-gray-500">تضاف للمجموع</td></tr>
                        
                        <tr class="bg-emerald-50/50 font-bold"><td colspan="4" class="p-2 text-emerald-800">ثانياً: مواد الشعبة العلمية (علوم)</td></tr>
                        <tr><td class="p-3">الأحياء</td><td class="p-3 text-center font-semibold">60</td><td class="p-3 text-center">30</td><td class="p-3 text-xs text-gray-500">تضاف للمجموع</td></tr>
                        <tr class="bg-gray-50"><td class="p-3">الجيولوجيا والعلوم البيئية</td><td class="p-3 text-center font-semibold">60</td><td class="p-3 text-center">30</td><td class="p-3 text-xs text-gray-500">تضاف للمجموع</td></tr>
                        <tr><td class="p-3">الفيزياء</td><td class="p-3 text-center font-semibold">60</td><td class="p-3 text-center">30</td><td class="p-3 text-xs text-gray-500">تضاف للمجموع لشعبتي العلوم والرياضة</td></tr>
                        <tr class="bg-gray-50"><td class="p-3">الكيمياء</td><td class="p-3 text-center font-semibold">60</td><td class="p-3 text-center">30</td><td class="p-3 text-xs text-gray-500">تضاف للمجموع لشعبتي العلوم والرياضة</td></tr>
                        
                        <tr class="bg-amber-50/50 font-bold"><td colspan="4" class="p-2 text-amber-800">ثالثاً: مواد الشعبة العلمية (رياضيات)</td></tr>
                        <tr><td class="p-3">الرياضيات البحتة (جبر وهندسة فراغية + تفاضل وتكامل)</td><td class="p-3 text-center font-semibold">60</td><td class="p-3 text-center">30</td><td class="p-3 text-xs text-gray-500">تضاف للمجموع</td></tr>
                        <tr class="bg-gray-50"><td class="p-3">الرياضيات التطبيقية (استاتيكا + ديناميكا)</td><td class="p-3 text-center font-semibold">60</td><td class="p-3 text-center">30</td><td class="p-3 text-xs text-gray-500">تضاف للمجموع</td></tr>
                        
                        <tr class="bg-purple-50/50 font-bold"><td colspan="4" class="p-2 text-purple-800">رابعاً: مواد الشعبة الأدبية</td></tr>
                        <tr><td class="p-3">التاريخ</td><td class="p-3 text-center font-semibold">60</td><td class="p-3 text-center">30</td><td class="p-3 text-xs text-gray-500">تضاف للمجموع</td></tr>
                        <tr class="bg-gray-50"><td class="p-3">الجغرافيا</td><td class="p-3 text-center font-semibold">60</td><td class="p-3 text-center">30</td><td class="p-3 text-xs text-gray-500">تضاف للمجموع</td></tr>
                        <tr><td class="p-3">الفلسفة والمنطق</td><td class="p-3 text-center font-semibold">60</td><td class="p-3 text-center">20</td><td class="p-3 text-xs text-gray-500">تضاف للمجموع</td></tr>
                        <tr class="bg-gray-50"><td class="p-3">علم النفس والاجتماع</td><td class="p-3 text-center font-semibold">60</td><td class="p-3 text-center">30</td><td class="p-3 text-xs text-gray-500">تضاف للمجموع</td></tr>
                        
                        <tr class="bg-red-50/50 font-bold"><td colspan="4" class="p-2 text-red-800">خامساً: مواد لا تضاف للمجموع (مواد النجاح والرسوب)</td></tr>
                        <tr><td class="p-3">التربية الدينية</td><td class="p-3 text-center font-semibold">25</td><td class="p-3 text-center">12.5</td><td class="p-3 text-xs text-gray-500">لا تضاف للمجموع الكلي</td></tr>
                        <tr class="bg-gray-50"><td class="p-3">التربية الوطنية</td><td class="p-3 text-center font-semibold">25</td><td class="p-3 text-center">12.5</td><td class="p-3 text-xs text-gray-500">لا تضاف للمجموع الكلي</td></tr>
                        <tr><td class="p-3">الاقتصاد والإحصاء</td><td class="p-3 text-center font-semibold">50</td><td class="p-3 text-center">25</td><td class="p-3 text-xs text-gray-500">لا تضاف للمجموع الكلي</td></tr>
                        
                        <tr class="bg-blue-600 text-white font-bold">
                            <td class="p-3">المجموع الكلي</td>
                            <td class="p-3 text-center">410</td>
                            <td class="p-3 text-center">205</td>
                            <td class="p-3 text-xs">نسبة النجاح الدنيا هي 50%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-calculator text-blue-500"></i>
                كيفية حساب النسبة المئوية لمجموعك الكلي
            </h3>
            <p class="text-gray-600 leading-relaxed mb-6">
                يعتمد نظام التنسيق والقبول بالجامعات على النسبة المئوية لمجموع الطالب بدلاً من الدرجات الخام فقط. يمكنك حساب النسبة المئوية لدرجاتك بسهولة فائقة باستخدام المعادلة الرياضية التالية:
            </p>
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 text-center mb-6">
                <div class="font-mono text-xl sm:text-2xl font-bold text-blue-700 mb-2">
                    النسبة المئوية = (المجموع الذي حصلت عليه ÷ 410) × 100
                </div>
                <div class="text-sm text-gray-500">
                    مثال: إذا كان مجموع درجاتك هو 369 درجة: (369 ÷ 410) = 0.9. ثم تضرب في 100 ليكون الناتج 90%.
                </div>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-blue-500"></i>
                تظلمات نتيجة الثانوية العامة 2026
            </h3>
            <p class="text-gray-600 leading-relaxed mb-4">
                تتيح وزارة التربية والتعليم والتعليم الفني خدمة تقديم التظلمات إلكترونياً للطلاب الذين يشعرون أن درجاتهم لا تعكس مستواهم الحقيقي. تبدأ مرحلة التظلمات عادةً خلال <strong>48 ساعة</strong> من تاريخ إعلان النتيجة رسمياً، وتستمر لمدة <strong>15 يوماً</strong>.
            </p>
            <p class="text-gray-600 leading-relaxed mb-4">
                يقوم الطالب بسداد الرسوم المقررة لكل مادة (والتي يتم تحديدها من قبل الوزارة) عبر منافذ الدفع الإلكترونية المعتمدة مثل فوري أو البريد المصري. بعد السداد، يقوم الطالب بحجز موعد إلكتروني للذهاب إلى مقر الكنترول الرئيسي التابع لمحافظته لمراجعة صورة من كراسة الإجابة (البابل شيت) وورقة الأسئلة، ومقارنة إجاباته مع نموذج الإجابة الرسمي لكتابة أي ملاحظات واستعادة حقه في حال وجود أي خطأ في رصد أو تجميع الدرجات.
            </p>

            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2 flex-wrap">
                <i class="fa-solid fa-graduation-cap text-blue-500"></i>
                مرحلة تنسيق الكليات والمعاهد للقبول بالجامعات 2026
            </h3>
            <p class="text-gray-600 leading-relaxed mb-6">
                بمجرد إعلان النتائج والحصول على درجاتك، تبدأ مرحلة التنسيق الإلكتروني عبر موقع التنسيق الرسمي التابع لوزارة التعليم العالي. ينقسم التنسيق لطلاب الثانوية العامة إلى <strong>ثلاث مراحل رئيسية</strong> بحسب المجموع:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100 text-center">
                    <span class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold mx-auto mb-3 flex-shrink-0">1</span>
                    <h4 class="font-bold text-gray-800 mb-2">المرحلة الأولى</h4>
                    <p class="text-xs text-gray-600 leading-relaxed">تضم الحاصلين على المجاميع المرتفعة، وتؤهل للالتحاق بكليات القمة مثل الطب، الهندسة، الصيدلة، والإعلام.</p>
                </div>
                <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100 text-center">
                    <span class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold mx-auto mb-3 flex-shrink-0">2</span>
                    <h4 class="font-bold text-gray-800 mb-2">المرحلة الثانية</h4>
                    <p class="text-xs text-gray-600 leading-relaxed">تضم شريحة متوسطي المجموع، وتستهدف كليات العلوم، الحاسبات، الآداب، التجارة، والتربية.</p>
                </div>
                <div class="bg-purple-50 rounded-2xl p-5 border border-purple-100 text-center">
                    <span class="w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold mx-auto mb-3 flex-shrink-0">3</span>
                    <h4 class="font-bold text-gray-800 mb-2">المرحلة الثالثة</h4>
                    <p class="text-xs text-gray-600 leading-relaxed">تشمل بقية الطلاب الناجحين وطلاب الدور الثاني، وتستهدف بقية الكليات والمعاهد العليا والمتوسطة.</p>
                </div>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-5 border-b-2 border-gray-100 pb-2">
                <i class="fa-solid fa-comments text-blue-500 ml-1"></i>
                الأسئلة الشائعة حول نتيجة الثانوية العامة 2026
            </h3>
            
            <div class="space-y-4">
                <div class="border-b border-gray-100 pb-4">
                    <h4 class="font-bold text-gray-800 mb-2">متى تظهر نتيجة الثانوية العامة 2026 في مصر؟</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">تظهر نتيجة الثانوية العامة 2026 بعد انتهاء أعمال التصحيح ورصد درجات الطلاب إلكترونياً واعتمادها رسمياً من وزير التربية والتعليم والتعليم الفني في المؤتمر الصحفي، ومن المتوقع إعلان النتيجة في أواخر شهر يوليو أو مطلع أغسطس 2026.</p>
                </div>
                <div class="border-b border-gray-100 pb-4">
                    <h4 class="font-bold text-gray-800 mb-2">كيف يمكنني الحصول على نتيجة الثانوية العامة 2026 برقم الجلوس والاسم؟</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">يمكنك الاستعلام الفوري عن النتيجة عبر منصة نتيجتي بالخطوات التالية: 1. الدخول على صفحة نتيجة الثانوية العامة في مصر. 2. كتابة رقم الجلوس الخاص بك في خانة البحث. 3. الضغط على زر "بحث عن النتيجة" لتظهر لك الدرجات بالتفصيل مع النسبة المئوية والمجموع الكلي.</p>
                </div>
                <div class="border-b border-gray-100 pb-4">
                    <h4 class="font-bold text-gray-800 mb-2">ما هو المجموع الكلي لدرجات الثانوية العامة 2026؟</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">المجموع الكلي لدرجات الثانوية العامة المصرية هو 410 درجات موزعة على المواد الأساسية للشعبتين العلمية والأدبية، وحد النجاح (درجة المرور) هو 50% من درجة كل مادة بشرط حضور الطالب للامتحان.</p>
                </div>
                <div class="border-b border-gray-100 pb-4">
                    <h4 class="font-bold text-gray-800 mb-2">كيف يتم حساب النسبة المئوية للثانوية العامة؟</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">يتم حساب النسبة المئوية عن طريق قسمة المجموع الذي حصل عليه الطالب على المجموع الكلي (410) ثم ضرب الناتج في 100. على سبيل المثال، إذا حصل الطالب على مجموع 369 درجة، تكون النسبة المئوية: (369 / 410) * 100 = 90%.</p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-2">ما هي خطوات تقديم تظلم على نتيجة الثانوية العامة؟</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">بعد إعلان النتيجة، تفتح وزارة التربية والتعليم باب التظلمات إلكترونياً لمدة 15 يوماً. يقوم الطالب بدفع الرسوم المقررة لكل مادة عبر منافذ الدفع المعتمدة، ثم يحدد موعداً للاطلاع على صورة من ورقة الإجابة (البابل شيت) وكتابة ملاحظاته في مقر الكنترول الرئيسي.</p>
                </div>
            </div>
        </article>
    </div>
    @endif

    <!-- كلمات البحث الشائعة -->
    <div class="max-w-4xl mx-auto mt-8 no-print">
        @include('partials.popular-keywords')
    </div>
</div>
@endsection
