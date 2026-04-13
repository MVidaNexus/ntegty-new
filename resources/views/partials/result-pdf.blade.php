{{-- PDF Viewer Result View --}}
<div class="max-w-6xl mx-auto">
    @php
        // استخدام المصدر الممرر (محافظة أو نوع امتحان)
        $pdfSource = $source ?? $examType;
        
        $pdfPath = $pdfSource->pdf_file_path ?? '';
        $hasPdf = !empty($pdfPath);
        $pdfUrl = $hasPdf ? asset('uploads/' . $pdfPath) : '';
    @endphp

    @if($hasPdf)
        <!-- Professional Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-lg mb-4">
                <i class="fa-solid fa-file-pdf text-white text-4xl"></i>
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-gray-800 mb-2">
                {{ $title }}
            </h1>
            <div class="flex items-center justify-center gap-4 mt-3">
                <span class="text-gray-500 text-sm">
                    <i class="fa-solid fa-keyboard ml-1"></i>
                    اضغط Ctrl+F للبحث
                </span>
                <a href="{{ $pdfUrl }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors text-sm">
                    <i class="fa-solid fa-external-link"></i>
                    فتح في صفحة جديدة
                </a>
            </div>
        </div>

        <!-- PDF Viewer Container -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden" x-data="{ loading: true }">
            <!-- Loading Indicator -->
            <div x-show="loading" class="flex items-center justify-center py-20 bg-gray-50">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 mb-4">
                        <svg class="animate-spin h-12 w-12 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600 font-medium">جاري تحميل ملف النتيجة...</p>
                </div>
            </div>

            <!-- PDF iFrame -->
            <iframe 
                src="{{ $pdfUrl }}#toolbar=1&navpanes=1&scrollbar=1" 
                class="w-full border-0"
                style="min-height: 85vh; height: 900px;"
                @load="loading = false"
                x-show="!loading"
                x-transition
            ></iframe>
        </div>

        <!-- Mobile Tip -->
        <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-xl sm:hidden">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-mobile-screen text-amber-500 text-xl mt-0.5"></i>
                <div>
                    <h4 class="font-bold text-amber-800 mb-1">على الهاتف؟</h4>
                    <p class="text-amber-700 text-sm">
                        اضغط على "فتح في صفحة جديدة" لعرض أفضل.
                    </p>
                </div>
            </div>
        </div>
    @else
        {{-- No PDF configured --}}
        <div class="text-center py-20">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-2xl mb-4">
                <i class="fa-solid fa-file-circle-xmark text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-700 mb-2">لم يتم رفع ملف النتيجة</h3>
            <p class="text-gray-500 text-sm">يرجى المحاولة لاحقاً</p>
        </div>
    @endif

    <!-- Popular Searches -->

</div>
