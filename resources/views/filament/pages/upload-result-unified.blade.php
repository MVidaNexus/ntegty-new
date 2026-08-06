<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Info --}}
        <div class="p-4 bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-xl border border-primary-200 dark:border-primary-700">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-primary-500/20 rounded-lg">
                    <x-heroicon-o-academic-cap class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-primary-900 dark:text-primary-100">إدارة النتائج الموحدة</h2>
                    <p class="text-sm text-primary-700 dark:text-primary-300">
                        من هنا يمكنك رفع أو تحديث طريقة عرض النتائج لأي شهادة
                    </p>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <x-heroicon-o-document-text class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">ملفات Excel</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            {{ \App\Models\UploadLog::where('status', 'completed')->count() }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <x-heroicon-o-document class="w-5 h-5 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">شهادات PDF</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            {{ \App\Models\ExamType::where('result_service_type', 'pdf')->count() }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <x-heroicon-o-globe-alt class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">روابط Embed</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            {{ \App\Models\ExamType::where('result_service_type', 'embed')->count() }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                        <x-heroicon-o-table-cells class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">جداول محافظات</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            {{ \App\Models\ExamType::where('result_service_type', 'governorate_table')->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <form wire:submit="create">
                {{ $this->form }}
            </form>
        </div>

        {{-- Help Section --}}
        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                <x-heroicon-o-question-mark-circle class="w-5 h-5 text-gray-500" />
                مساعدة سريعة
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="font-medium text-gray-700 dark:text-gray-300 mb-1"><i class="fa-solid fa-chart-column text-blue-500"></i> متى استخدم Excel؟</p>
                    <p class="text-gray-600 dark:text-gray-400">
                        عندما تريد أن يبحث الطلاب برقم الجلوس ويحصلوا على نتيجتهم. مناسب للنتائج التفصيلية.
                    </p>
                </div>
                <div>
                    <p class="font-medium text-gray-700 dark:text-gray-300 mb-1"><i class="fa-solid fa-file-lines text-slate-500"></i> متى استخدم PDF؟</p>
                    <p class="text-gray-600 dark:text-gray-400">
                        عندما تريد عرض ملف كامل للتصفح أو التحميل. مناسب للكشوف الرسمية.
                    </p>
                </div>
                <div>
                    <p class="font-medium text-gray-700 dark:text-gray-300 mb-1"><i class="fa-solid fa-globe text-blue-500"></i> متى استخدم iFrame؟</p>
                    <p class="text-gray-600 dark:text-gray-400">
                        عندما تريد تضمين موقع الوزارة أو مصدر خارجي داخل صفحتك.
                    </p>
                </div>
                <div>
                    <p class="font-medium text-gray-700 dark:text-gray-300 mb-1"><i class="fa-solid fa-clipboard-list text-emerald-500"></i> متى استخدم جدول المحافظات؟</p>
                    <p class="text-gray-600 dark:text-gray-400">
                        عندما تصدر النتيجة تدريجياً لكل محافظة ملف منفصل (مثل الإعدادية).
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
