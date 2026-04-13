<x-filament-panels::page>
    {{-- شريط الإحصائيات العلوي --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- إجمالي الروابط --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">إجمالي الروابط</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($this->statistics['overview']['total_urls'] ?? 0) }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                    <x-heroicon-o-link class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        {{-- عدد الخرائط --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">عدد الخرائط</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($this->statistics['overview']['total_sitemaps'] ?? 0) }}</p>
                </div>
                <div class="bg-emerald-100 dark:bg-emerald-900 rounded-lg p-3">
                    <x-heroicon-o-map class="w-8 h-8 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>
        </div>

        {{-- الحالة --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">الحالة</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        @if($this->statistics['overview']['is_enabled'] ?? true)
                            <span class="flex items-center gap-2 text-green-600 dark:text-green-400">
                                <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                                نشط
                            </span>
                        @else
                            <span class="flex items-center gap-2 text-red-600 dark:text-red-400">
                                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                                معطل
                            </span>
                        @endif
                    </p>
                </div>
                <div class="bg-amber-100 dark:bg-amber-900 rounded-lg p-3">
                    <x-heroicon-o-signal class="w-8 h-8 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
        </div>

        {{-- آخر تحديث --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">آخر تحديث</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        @if($this->statistics['overview']['last_updated'] ?? null)
                            {{ $this->statistics['overview']['last_updated']->diffForHumans() }}
                        @else
                            لم يتم بعد
                        @endif
                    </p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900 rounded-lg p-3">
                    <x-heroicon-o-clock class="w-8 h-8 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
        </div>
    </div>

    {{-- تفاصيل الخرائط --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <x-heroicon-o-queue-list class="w-5 h-5" />
                تفاصيل خرائط الموقع
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">الخريطة</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">النوع</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">عدد الروابط</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">الحالة</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">عرض</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse($this->sitemaps ?? [] as $sitemap)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if(isset($sitemap['icon']))
                                        <x-dynamic-component :component="$sitemap['icon']" class="w-5 h-5 text-gray-400" />
                                    @endif
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $sitemap['label'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @switch($sitemap['type'])
                                        @case('pages') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @break
                                        @case('countries') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                                        @case('students') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 @break
                                        @case('governorates') bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200 @break
                                        @default bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @endswitch
                                ">
                                    {{ $sitemap['type'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                                    {{ number_format($sitemap['urls_count']) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($sitemap['enabled'] ?? true)
                                    <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400">
                                        <x-heroicon-s-check-circle class="w-5 h-5" />
                                        مفعل
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-red-600 dark:text-red-400">
                                        <x-heroicon-s-x-circle class="w-5 h-5" />
                                        معطل
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ $sitemap['url'] }}" target="_blank" 
                                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                                    عرض
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <x-heroicon-o-document-magnifying-glass class="w-12 h-12 mx-auto mb-2 opacity-50" />
                                لا توجد خرائط حالياً
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- إحصائيات المحتوى --}}
    @if(isset($this->statistics['content']))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 dark:bg-blue-900 rounded-lg p-2">
                    <x-heroicon-o-globe-alt class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">الدول</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $this->statistics['content']['countries']['active'] ?? 0 }}
                        <span class="text-sm font-normal text-gray-400">/ {{ $this->statistics['content']['countries']['total'] ?? 0 }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="bg-green-100 dark:bg-green-900 rounded-lg p-2">
                    <x-heroicon-o-academic-cap class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">أنواع الشهادات</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $this->statistics['content']['exam_types']['active'] ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="bg-amber-100 dark:bg-amber-900 rounded-lg p-2">
                    <x-heroicon-o-map-pin class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">المحافظات</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $this->statistics['content']['governorates']['active'] ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="bg-purple-100 dark:bg-purple-900 rounded-lg p-2">
                    <x-heroicon-o-users class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">نتائج الطلاب</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($this->statistics['content']['results']['total'] ?? 0) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- نتائج الطلاب حسب الدولة --}}
    @if(isset($this->statistics['content']['results']['by_country']) && count($this->statistics['content']['results']['by_country']) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 px-6 py-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <x-heroicon-o-chart-bar class="w-5 h-5" />
                توزيع النتائج حسب الدولة
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @php
                    $maxCount = max($this->statistics['content']['results']['by_country']);
                @endphp
                @foreach($this->statistics['content']['results']['by_country'] as $country => $count)
                    <div class="flex items-center gap-4">
                        <div class="w-24 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $country }}</div>
                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-6 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-end pr-2 text-xs text-white font-bold"
                                 style="width: {{ ($count / $maxCount) * 100 }}%">
                                {{ number_format($count) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- نموذج الإعدادات --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                إعدادات خرائط الموقع
            </h3>
        </div>
        <div class="p-6">
            <form wire:submit="save">
                {{ $this->form }}
                
                <div class="mt-6 flex justify-end">
                    <x-filament::button type="submit" size="lg">
                        <x-heroicon-o-check class="w-5 h-5 ml-2" />
                        حفظ الإعدادات
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
