@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumbs -->
    @if(isset($breadcrumbs))
    <nav class="mb-6 text-sm">
        <ol class="flex items-center gap-2 text-gray-600 dark:text-slate-400">
            @foreach($breadcrumbs as $index => $crumb)
                @if($index > 0)
                    <li><i class="fa-solid fa-chevron-left text-xs mx-2"></i></li>
                @endif
                <li>
                    @if(isset($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="hover:text-blue-600 dark:hover:text-emerald-400">{{ $crumb['name'] }}</a>
                    @else
                        <span class="text-gray-800 dark:text-slate-200 font-semibold">{{ $crumb['name'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
    @endif

    @php
        $serviceType = $diplomaExamType->result_service_type ?? 'search';
        $embedCode = $diplomaExamType->embed_code ?? '';
        $pdfPath = $diplomaExamType->pdf_file_path ?? '';
        
        // تحويل الشُعب لـ JSON للـ JavaScript
        $branchesJson = $branches->map(function($branch) {
            return [
                'id' => $branch->id,
                'code' => $branch->code,
                'slug' => $branch->slug,
                'name_ar' => $branch->name_ar,
                'icon' => $branch->icon ?? 'fa-graduation-cap',
                'color' => $branch->color ?? 'emerald',
                'total_score' => $branch->total_score,
                'passing_score' => $branch->passing_score,
            ];
        })->toArray();
    @endphp

    <!-- Search Section -->
    <div class="w-full max-w-6xl mx-auto px-3" x-data="searchDiplomas()">
        <div class="bg-gradient-to-br from-white to-emerald-50 dark:from-slate-800 dark:to-slate-900 rounded-2xl sm:rounded-3xl shadow-2xl p-5 sm:p-8 md:p-10 border border-emerald-100 dark:border-slate-700 transition-colors duration-300">
            <!-- Header -->
            <div class="text-center mb-6 sm:mb-8 no-print">
                <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg mb-3 sm:mb-4">
                    <i class="fa-solid fa-graduation-cap text-white text-3xl sm:text-4xl"></i>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-gray-800 dark:text-white mb-2 sm:mb-3 leading-relaxed text-center px-2">
                    نتيجة الدبلومات الفنية في {{ $egypt->getDynamicTitle(false, false) }}
                </h1>
                
                <!-- Result Timer -->
                <div class="mb-4 sm:mb-6">
                    <x-result-timer country="egypt" type="diplomas" />
                </div>
            </div>

            @if($serviceType === 'search')
                {{-- ========== Search Mode ========== --}}
                
                <!-- Search Form -->
                <form @submit.prevent="search" class="space-y-4 sm:space-y-6 no-print">
                    <!-- Diploma Branch Selection (Dropdown style as requested) -->
                    <div class="mb-4 sm:mb-6">
                        <label class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-slate-300 mb-2 sm:mb-3">
                            <span class="flex items-center gap-2">
                                <i class="fa-solid fa-list-check text-emerald-500"></i>
                                نوع الدبلوم الفني
                            </span>
                        </label>
                        <select @change="selectedBranchId = parseInt($event.target.value); const b = branches.find(x => x.id === selectedBranchId); selectedBranchCode = b ? b.code : '';"
                                class="w-full px-4 py-3 sm:px-6 sm:py-4 border-2 border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 focus:outline-none text-base sm:text-lg transition-all bg-white font-bold">
                            <option value="">-- اختر نوع الدبلوم (تجاري - صناعي - زراعي - فندقي) --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        @if(isset($showYearFilter) && $showYearFilter && isset($academicYears) && $academicYears->count())
                        <div class="w-full sm:w-1/3">
                            <label class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-slate-300 mb-2 sm:mb-3">
                                السنة الدراسية
                            </label>
                            <select x-model="academicYearId" 
                                    class="w-full px-4 py-3 sm:px-6 sm:py-4 border-2 border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 focus:outline-none text-base sm:text-lg transition-all bg-white">
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->year }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="w-full {{ (isset($showYearFilter) && $showYearFilter) ? 'sm:w-2/3' : '' }}">
                            <label class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-slate-300 mb-2 sm:mb-3">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    رقم الجلوس أو الاسم
                                </span>
                            </label>
                            <input type="text" 
                                   x-model="query" 
                                   required
                                   placeholder="أدخل رقم الجلوس أو الاسم..."
                                   class="w-full px-4 py-3 sm:px-6 sm:py-4 border-2 border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 focus:outline-none text-base sm:text-lg transition-all">
                        </div>
                    </div>

                    <button type="submit" 
                            :disabled="loading || !selectedBranchId"
                            class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold py-3 sm:py-4 px-6 sm:px-8 rounded-xl transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-lg hover:shadow-xl">
                        <span x-show="!loading" class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span class="text-base sm:text-xl">بحث عن النتيجة</span>
                        </span>
                        <span x-show="loading" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm sm:text-base">جاري البحث...</span>
                        </span>
                    </button>

                    <!-- Validation Message -->
                    <div x-show="!selectedBranchId" class="text-center text-amber-600 dark:text-amber-400 text-sm font-medium">
                        <i class="fa-solid fa-triangle-exclamation ml-1"></i>
                        يرجى اختيار نوع الدبلوم أولاً
                    </div>

                    <!-- Search Tips -->
                    <div class="mt-4 p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/50 rounded-xl text-sm text-emerald-800 dark:text-emerald-300">
                        <div class="flex items-center gap-2 mb-2 font-bold">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>تنويهات هامة للبحث:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-emerald-700/90 dark:text-emerald-400/90 leading-relaxed font-medium">
                            <li>اختر نوع الدبلوم (تجاري - صناعي - زراعي - فندقي) قبل البدء بالبحث.</li>
                            <li>عند البحث بالاسم، يجب كتابة <strong>الاسم ثلاثي</strong> على الأقل.</li>
                            <li>يفضل البحث برقم الجلوس للحصول على نتيجة فورية دقيقة.</li>
                        </ul>
                    </div>
                </form>

                <!-- Results -->
                <div x-show="results.length > 0" class="mt-6 sm:mt-10 space-y-4 sm:space-y-6" x-cloak x-transition>
                    <div class="border-t-2 border-emerald-200 dark:border-slate-700 pt-4 sm:pt-6">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white mb-4 sm:mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                            </svg>
                            النتائج
                            <span class="px-2 py-1 bg-emerald-100 dark:bg-slate-800 text-emerald-700 dark:text-emerald-400 text-xs rounded-full font-bold" x-text="results.length + ' نتيجة'"></span>
                        </h2>
                        
                        <template x-for="result in results" :key="result.id">
                            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-6 md:p-8 border-2 border-emerald-100 dark:border-slate-800 shadow-lg hover:shadow-xl transition-shadow mb-4">
                                <!-- Total Score -->
                                <div class="mb-4 sm:mb-5">
                                    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl p-4 text-center text-white shadow-md">
                                        <p class="text-sm font-medium mb-1 opacity-90">المجموع الكلي</p>
                                        <p class="text-4xl sm:text-5xl font-black" x-text="result.total_score"></p>
                                    </div>
                                </div>

                                <!-- Student Info -->
                                <div class="mb-4 sm:mb-5 pb-4 border-b border-gray-200 dark:border-slate-800">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-10 h-10 bg-emerald-100 dark:bg-slate-800 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-user text-emerald-600 dark:text-emerald-400"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white" x-text="result.student_name"></h3>
                                            <p class="text-sm text-gray-500 dark:text-slate-400">
                                                رقم الجلوس: <span class="font-bold text-gray-800 dark:text-white" x-text="result.seat_number"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-3 text-sm" x-show="result.school">
                                        <span class="flex items-center gap-1 bg-gray-100 dark:bg-slate-800 px-3 py-1 rounded-full text-slate-700 dark:text-slate-300">
                                            <i class="fa-solid fa-school text-gray-500 dark:text-slate-400"></i>
                                            <span x-text="result.school"></span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Subjects Grid -->
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-4">
                                    <template x-for="(score, subject) in result.subjects" :key="subject">
                                        <div class="bg-gray-50 dark:bg-slate-800/50 rounded-lg p-3 text-center border border-gray-100 dark:border-slate-700">
                                            <p class="text-xs text-gray-500 dark:text-slate-400 mb-1" x-text="subject"></p>
                                            <p class="text-lg font-bold text-gray-800 dark:text-slate-200" x-text="score"></p>
                                        </div>
                                    </template>
                                </div>

                                <!-- Status & Certificate -->
                                <div class="flex flex-wrap items-center justify-between gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-slate-800">
                                    <span class="px-4 py-1.5 rounded-full text-sm font-bold flex items-center gap-1"
                                          :class="result.status === 'ناجح' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400'">
                                        <span class="w-2 h-2 rounded-full" :class="result.status === 'ناجح' ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                        <span x-text="result.status"></span>
                                    </span>
                                    
                                    <template x-if="result.status === 'ناجح' || result.status.includes('نجح')">
                                        <a :href="'{{ route('certificate.index') }}?name=' + encodeURIComponent(result.student_name) + '&score=' + result.total_score + '&type=' + encodeURIComponent(result.branch || 'دبلوم فني') + '&seat=' + result.seat_number + '&status=ناجح'"
                                           target="_blank"
                                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-xs sm:text-sm font-bold rounded-xl transition-all hover:scale-105 shadow-md">
                                            <i class="fa-solid fa-certificate"></i>
                                            <span>شهادة تقدير 🎓</span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Error Message -->
                <div x-show="error" x-text="error" x-transition x-cloak
                     class="mt-6 p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 text-rose-700 dark:text-rose-400 text-center font-medium"></div>

            @elseif($serviceType === 'pdf')
                {{-- ========== PDF Mode ========== --}}
                @if($pdfPath)
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-lg mb-3">
                            <i class="fa-solid fa-file-pdf text-white text-3xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-2">نتيجة الدبلومات الفنية</h2>
                        <div class="flex items-center justify-center gap-4">
                            <span class="text-gray-500 dark:text-slate-400 text-sm">
                                <i class="fa-solid fa-keyboard ml-1"></i>
                                اضغط Ctrl+F للبحث
                            </span>
                            <a href="{{ asset('uploads/' . $pdfPath) }}" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors text-sm">
                                <i class="fa-solid fa-external-link"></i>
                                فتح في صفحة جديدة
                            </a>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-gray-200 dark:border-slate-800 overflow-hidden">
                        <iframe src="{{ asset('uploads/' . $pdfPath) }}#toolbar=1&navpanes=1" 
                                class="w-full border-0" style="min-height: 80vh; height: 800px;"></iframe>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500 dark:text-slate-400">
                        <i class="fa-solid fa-file-circle-xmark text-5xl mb-4 opacity-50"></i>
                        <p class="font-bold">لم يتم رفع ملف النتيجة بعد</p>
                    </div>
                @endif

            @elseif($serviceType === 'embed')
                {{-- ========== Embed Mode ========== --}}
                @if($embedCode)
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg mb-3">
                            <i class="fa-solid fa-globe text-white text-3xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">نتيجة الدبلومات الفنية</h2>
                    </div>

                    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-gray-200 dark:border-slate-800 overflow-hidden">
                        @if(str_starts_with($embedCode, 'http'))
                            <iframe src="{{ $embedCode }}" class="w-full border-0" style="min-height: 80vh;"></iframe>
                        @else
                            {!! $embedCode !!}
                        @endif
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500 dark:text-slate-400">
                        <i class="fa-solid fa-code text-5xl mb-4 opacity-50"></i>
                        <p class="font-bold">لم يتم تكوين رابط النتيجة بعد</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

@push('scripts')
<script>
function searchDiplomas() {
    return {
        query: '',
        selectedBranchId: null,
        selectedBranchCode: '',
        branches: @json($branches),
        academicYearId: '{{ \App\Models\AcademicYear::where("is_active", true)->value("id") ?? "" }}',
        loading: false,
        results: [],
        error: '',
        
        selectBranch(id, code) {
            this.selectedBranchId = id;
            this.selectedBranchCode = code;
        },
        
        getSelectedBranch() {
            return this.branches.find(b => b.id === this.selectedBranchId);
        },
        
        async search() {
            if (!this.selectedBranchId) {
                this.error = 'يرجى اختيار نوع الدبلوم أولاً';
                return;
            }
            
            this.loading = true;
            this.error = '';
            this.results = [];
            
            try {
                const response = await fetch('{{ route("search") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        query: this.query,
                        exam_type_id: '{{ $diplomaExamType ? $diplomaExamType->id : "" }}',
                        academic_year_id: this.academicYearId,
                        branch: this.selectedBranchCode,
                        branch_id: this.selectedBranchId
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.results = data.results;
                    if (this.results.length === 0) {
                        this.error = 'لم يتم العثور على نتائج. تأكد من صحة البيانات المدخلة.';
                    }
                } else {
                    this.error = data.message || 'حدث خطأ أثناء البحث';
                }
            } catch (error) {
                this.error = 'حدث خطأ أثناء البحث. يرجى المحاولة مرة أخرى.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endpush

{{-- Content Section for SEO --}}
@if(isset($diplomaExamType) && $diplomaExamType->show_content_section && ($diplomaExamType->content_title || $diplomaExamType->content_body))
<div class="w-full max-w-6xl mx-auto mt-12 px-3">
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-6 md:p-10 border border-gray-100 dark:border-slate-800 transition-colors duration-300">
        @if($diplomaExamType->content_title)
        <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 dark:text-white mb-5 pb-3 border-b-2 border-gray-100 dark:border-slate-800">{{ $diplomaExamType->content_title }}</h2>
        @endif
        @if($diplomaExamType->content_intro)
        <p class="text-gray-600 dark:text-slate-300 mb-6 text-base md:text-lg leading-relaxed">{{ $diplomaExamType->content_intro }}</p>
        @endif
        @if($diplomaExamType->content_body)
        <div class="prose prose-base md:prose-lg max-w-none text-gray-700 dark:text-slate-300 leading-loose
                    prose-headings:font-bold prose-headings:text-gray-800 dark:prose-headings:text-white prose-headings:mt-6 prose-headings:mb-3
                    prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-emerald-500 prose-h2:pr-4 prose-h2:py-1
                    prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-emerald-700 dark:prose-h3:text-emerald-400
                    prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                    prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                    prose-a:text-emerald-600 dark:prose-a:text-emerald-400 prose-a:hover:text-emerald-700 dark:prose-a:hover:text-emerald-300">
            {!! $diplomaExamType->getFormattedContentBody() !!}
        </div>
        @endif
    </div>
</div>
@endif

<!-- Rich SEO Article — Technical Diplomas 800+ words — Updated July 2026 -->
<div class="max-w-4xl mx-auto mt-12 mb-6">
    <article class="bg-white dark:bg-slate-900 rounded-3xl shadow-lg border border-gray-100 dark:border-slate-800 p-8 md:p-12 transition-colors duration-300">
        <h2 class="text-2xl md:text-3xl font-black text-gray-800 dark:text-white mb-5 border-r-4 border-emerald-500 pr-4">
            نتيجة الدبلومات الفنية 2026 — بوابتك للمستقبل المهني والأكاديمي
        </h2>

        <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/50 rounded-2xl p-4 mb-6">
            <p class="text-emerald-800 dark:text-emerald-400 font-bold text-sm mb-1"><i class="fa-solid fa-bullhorn text-emerald-500"></i> يوليو 2026 — بدء إعلان النتائج</p>
            <p class="text-gray-700 dark:text-slate-300 text-sm leading-relaxed">أعلنت وزارة التربية والتعليم والتعليم الفني عن بدء إعلان نتائج الدبلومات الفنية (تجاري، صناعي، زراعي، فندقي) للدور الأول. ابحث عن نتيجتك الآن بالاسم ورقم الجلوس.</p>
        </div>

        <p class="text-gray-600 dark:text-slate-355 leading-relaxed mb-6 text-base md:text-lg">
            التعليم الفني هو عصب التنمية الاقتصادية في مصر، ومن هنا تكمن أهمية الدبلومات الفنية التي تستقطب سنوياً ما يقرب من <strong>60% من إجمالي طلاب المرحلة الثانوية</strong>. في عام 2026، ومع التوجه الاستراتيجي للدولة نحو دعم التنافسية الصناعية والتكنولوجية، حظي طلاب الدبلومات الفنية باهتمام غير مسبوق عبر تحديث المناهج وربط التخصصات بالاحتياجات الحقيقية للمصانع والشركات.
        </p>

        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3">ما الجديد في دبلومات مصر 2026؟</h3>
        <p class="text-gray-600 dark:text-slate-300 leading-relaxed mb-4">
            شهدت امتحانات هذا العام تطبيق <strong>منظومة الجدارات المهنية</strong> في أكثر من 80% من المدارس الفنية، وهي المنظومة التي تركز على تقييم المهارات العملية للطالب بشكل مستمر طوال العام الدراسي بدلاً من الاعتماد الكلي على الامتحان النظري النهائي. كما شملت التطويرات إدخال تخصصات جديدة مثل صيانة السيارات الكهربائية، الطاقة الشمسية، البرمجة وتطبيقات الويب، وخدمات اللوجستيات.
        </p>
        <p class="text-gray-600 dark:text-slate-300 leading-relaxed mb-6">
            تُسهل نتيجتي للطلاب الاستعلام الفوري عن النتيجة فور اعتمادها من وزير التربية والتعليم، حيث نوفر روابط فحص سريعة تدعم البحث بالاسم أو رقم الجلوس أو اسم الشعبة لضمان راحة البال لجميع الأهالي.
        </p>

        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3">تخصصات وشعب الدبلومات الفنية المتاحة</h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-emerald-50 dark:bg-emerald-950/20 rounded-2xl p-5 border border-emerald-100 dark:border-emerald-900/40">
                <div class="text-3xl mb-2"><i class="fa-solid fa-industry text-slate-600 dark:text-slate-400"></i></div>
                <h4 class="font-bold text-gray-800 dark:text-white mb-1 text-sm">الدبلوم الصناعي</h4>
                <p class="text-gray-600 dark:text-slate-400 text-xs leading-relaxed">يشمل التخصصات الهندسية كالميكانيكا، الكهرباء، التبريد والتكييف، الكترونيات السيارات، والطاقة المتجددة.</p>
            </div>
            <div class="bg-blue-50 dark:bg-blue-950/20 rounded-2xl p-5 border border-blue-100 dark:border-blue-900/40">
                <div class="text-3xl mb-2"><i class="fa-solid fa-chart-column text-blue-500 dark:text-blue-400"></i></div>
                <h4 class="font-bold text-gray-800 dark:text-white mb-1 text-sm">الدبلوم التجاري</h4>
                <p class="text-gray-600 dark:text-slate-400 text-xs leading-relaxed">يغطي مجالات التسويق، السكرتارية الطبية والقانونية، الإدارة المالية، وتكنولوجيا المعلومات المكتبية.</p>
            </div>
            <div class="bg-green-50 dark:bg-emerald-950/20 rounded-2xl p-5 border border-green-100 dark:border-emerald-900/40">
                <div class="text-3xl mb-2"><i class="fa-solid fa-wheat-awn text-amber-500 dark:text-amber-400"></i></div>
                <h4 class="font-bold text-gray-800 dark:text-white mb-1 text-sm">الدبلوم الزراعي</h4>
                <p class="text-gray-600 dark:text-slate-400 text-xs leading-relaxed">يركز على الإنتاج الحيواني والداجني، الصناعات الغذائية، تكنولوجيا الري، واستصلاح الأراضي الزراعية.</p>
            </div>
            <div class="bg-orange-50 dark:bg-amber-950/20 rounded-2xl p-5 border border-orange-100 dark:border-amber-900/40">
                <div class="text-3xl mb-2"><i class="fa-solid fa-hotel text-emerald-600 dark:text-emerald-400"></i></div>
                <h4 class="font-bold text-gray-800 dark:text-white mb-1 text-sm">الدبلوم الفندقي</h4>
                <p class="text-gray-600 dark:text-slate-400 text-xs leading-relaxed">يؤهل للعمل في قطاع الفنادق والضيافة، ويشمل فنون الطهي، الإرشاد السياحي، وإدارة المكاتب الفندقية.</p>
            </div>
            <div class="bg-pink-50 dark:bg-rose-950/20 rounded-2xl p-5 border border-pink-100 dark:border-rose-900/40">
                <div class="text-3xl mb-2"><i class="fa-solid fa-heart text-red-500"></i>️</div>
                <h4 class="font-bold text-gray-800 dark:text-white mb-1 text-sm">التمريض والدبلوم الصحي</h4>
                <p class="text-gray-600 dark:text-slate-400 text-xs leading-relaxed">يختص بتأهيل الكوادر المساعدة في المستشفيات والمراكز العلاجية تحت إشراف وزارة الصحة والسكان.</p>
            </div>
            <div class="bg-purple-50 dark:bg-purple-950/20 rounded-2xl p-5 border border-purple-100 dark:border-purple-900/40">
                <div class="text-3xl mb-2"><i class="fa-solid fa-palette text-pink-500"></i></div>
                <h4 class="font-bold text-gray-800 dark:text-white mb-1 text-sm">المدارس الفنية للبنات</h4>
                <p class="text-gray-600 dark:text-slate-400 text-xs leading-relaxed">تخصصات تصميم الأزياء، الملابس الجاهزة، الحرف اليدوية، والتربية المنزلية المتكاملة.</p>
            </div>
        </div>

        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3">مواعيد ظهور النتائج ونسب النجاح المتوقعة</h3>
        <p class="text-gray-600 dark:text-slate-300 leading-relaxed mb-4">
            وفق الجدول الزمني المحدد من قطاع التعليم الفني بالوزارة، انتهت الامتحانات التحريرية والعملية في منتصف يونيو 2026. وتجري حالياً عمليات تصحيح كراسات الإجابة بدقة بالغة في الكنترولات الرئيسية لجميع القطاعات (القاهرة، طنطا، دمنهور، قنا، المنيا). ومن المتوقع إعلان النتائج رسمياً خلال النصف الثاني من يوليو 2026. وتشير المؤشرات الأولية للعينات العشوائية المصححة إلى ارتفاع ملحوظ في نسب النجاح مقارنة بالعام الماضي.
        </p>
    </article>
</div>
@endsection
