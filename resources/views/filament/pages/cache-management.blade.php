<x-filament-panels::page>
    {{-- Cache Toggle Button --}}
    <div class="mb-6">
        @php
            $cacheEnabled = \App\Models\Setting::get('cache_enabled', true);
        @endphp
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    @if($cacheEnabled)
                        <div class="p-3 bg-success-100 dark:bg-success-900 rounded-full">
                            <x-heroicon-o-check-circle class="w-8 h-8 text-success-600 dark:text-success-400" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">الكاش مُفعّل</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">الموقع يعمل بأقصى سرعة</p>
                        </div>
                    @else
                        <div class="p-3 bg-danger-100 dark:bg-danger-900 rounded-full">
                            <x-heroicon-o-x-circle class="w-8 h-8 text-danger-600 dark:text-danger-400" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">الكاش مُعطّل</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">كل طلب يذهب للداتابيز مباشرة</p>
                        </div>
                    @endif
                </div>
                
                <div>
                    @if($cacheEnabled)
                        <x-filament::button 
                            wire:click="toggleCache"
                            color="danger"
                            icon="heroicon-o-pause"
                            size="lg"
                        >
                            إيقاف الكاش
                        </x-filament::button>
                    @else
                        <x-filament::button 
                            wire:click="toggleCache"
                            color="success"
                            icon="heroicon-o-play"
                            size="lg"
                        >
                            تفعيل الكاش
                        </x-filament::button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Driver Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-primary-100 dark:bg-primary-900 rounded-lg">
                    <x-heroicon-o-server class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Driver</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white uppercase">{{ $stats['driver'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Memory Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-success-100 dark:bg-success-900 rounded-lg">
                    <x-heroicon-o-cpu-chip class="w-6 h-6 text-success-600 dark:text-success-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">الذاكرة المستخدمة</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['memory_used'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Keys Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-warning-100 dark:bg-warning-900 rounded-lg">
                    <x-heroicon-o-key class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">الصفحات المخزنة</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_keys'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        {{-- Hit Rate Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-info-100 dark:bg-info-900 rounded-lg">
                    <x-heroicon-o-chart-bar class="w-6 h-6 text-info-600 dark:text-info-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">نسبة الإصابة</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['hit_rate'] ?? '0%' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">وقت التشغيل</p>
            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['uptime'] ?? 'N/A' }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Hits / Misses</p>
            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                <span class="text-success-600">{{ number_format($stats['hits'] ?? 0) }}</span> / 
                <span class="text-danger-600">{{ number_format($stats['misses'] ?? 0) }}</span>
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">ذروة الذاكرة</p>
            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['memory_peak'] ?? 'N/A' }}</p>
        </div>
    </div>

    {{-- Cache Settings Form --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 mb-6">
        <form wire:submit.prevent="saveCacheSettings">
            {{ $this->form }}
            
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex gap-3">
                <x-filament::button type="submit" icon="heroicon-o-check">
                    حفظ الإعدادات
                </x-filament::button>
                
                <x-filament::button 
                    type="button" 
                    color="gray" 
                    icon="heroicon-o-arrow-path"
                    wire:click="resetCacheSettings"
                    wire:confirm="هل تريد إعادة الإعدادات للقيم الافتراضية؟"
                >
                    إعادة الافتراضي
                </x-filament::button>
            </div>
        </form>
    </div>

    {{-- Categories Grid --}}
    @php
        $categoryLabels = [
            'results' => ['النتائج', 'heroicon-o-document-text', 'primary'],
            'exam_types' => ['الشهادات', 'heroicon-o-academic-cap', 'success'],
            'countries' => ['الدول', 'heroicon-o-globe-alt', 'info'],
            'governorates' => ['المحافظات', 'heroicon-o-map-pin', 'warning'],
            'branches' => ['الشُعب', 'heroicon-o-rectangle-group', 'danger'],
            'settings' => ['الإعدادات', 'heroicon-o-cog-6-tooth', 'gray'],
            'stats' => ['الإحصائيات', 'heroicon-o-chart-pie', 'purple'],
            'pages' => ['الصفحات', 'heroicon-o-document', 'cyan'],
            'page_cache' => ['كاش HTTP', 'heroicon-o-globe-americas', 'success'],
            'other' => ['أخرى', 'heroicon-o-squares-2x2', 'gray'],
        ];
    @endphp

    @if(!empty($categories))
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">فئات الكاش</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
            @foreach($categories as $category => $count)
                @php
                    $label = $categoryLabels[$category] ?? [$category, 'heroicon-o-folder', 'gray'];
                @endphp
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $label[0] }}</span>
                        <span class="text-xs bg-{{ $label[2] }}-100 text-{{ $label[2] }}-700 dark:bg-{{ $label[2] }}-900 dark:text-{{ $label[2] }}-300 px-2 py-0.5 rounded-full">{{ $count }}</span>
                    </div>
                    @if($count > 0)
                    <button 
                        type="button"
                        wire:click="clearCategory('{{ $category }}')"
                        wire:confirm="هل تريد مسح كاش {{ $label[0] }}؟"
                        class="text-danger-600 hover:text-danger-800 dark:text-danger-400"
                    >
                        <x-heroicon-o-trash class="w-4 h-4" />
                    </button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Info Box --}}
    <div class="bg-info-50 dark:bg-info-900/20 rounded-xl p-4 border border-info-200 dark:border-info-800">
        <div class="flex gap-3">
            <x-heroicon-o-information-circle class="w-5 h-5 text-info-600 flex-shrink-0 mt-0.5" />
            <div class="text-sm text-info-800 dark:text-info-200">
                <p class="font-semibold mb-1">معلومات عن الكاش:</p>
                <ul class="list-disc list-inside space-y-1 text-info-700 dark:text-info-300">
                    <li><strong>تحميل صفحات للكاش:</strong> يقوم بتحميل الصفحات الأساسية مسبقاً لتسريع الموقع</li>
                    <li><strong>نسبة الإصابة:</strong> كلما زادت كان الأداء أفضل (الصفحات تُقرأ من الكاش)</li>
                    <li><strong>مدة الكاش:</strong> يمكنك تعديل مدة كل نوع من الصفحات من الإعدادات أعلاه</li>
                    <li><strong>بدون كاش:</strong> اختر "بدون كاش" لإيقاف الكاش لنوع معين من الصفحات</li>
                </ul>
            </div>
        </div>
    </div>
</x-filament-panels::page>
