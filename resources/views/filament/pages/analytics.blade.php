<x-filament-panels::page>
    @php
        $stats = $this->getStats();
        $hourlyData = $this->getHourlyData();
        $pageTypes = $this->getPageTypeStats();
        $pageTypeLabels = [
            'home' => 'الرئيسية',
            'search' => 'البحث',
            'result' => 'نتيجة طالب',
            'governorate' => 'محافظة',
            'all_results' => 'جميع النتائج',
            'country' => 'دولة',
            'other' => 'أخرى',
        ];
    @endphp

    <!-- Time Period Selector -->
    <div class="mb-6 flex items-center gap-4">
        <span class="text-sm text-gray-500">عرض إحصائيات:</span>
        <div class="flex gap-2">
            @foreach([7 => 'أسبوع', 14 => 'أسبوعين', 30 => 'شهر', 90 => '3 أشهر'] as $value => $label)
                <button 
                    wire:click="setDays({{ $value }})"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all
                        {{ $this->days == $value 
                            ? 'bg-primary-500 text-white shadow-lg' 
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Main Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <x-heroicon-o-eye class="w-5 h-5" />
                </div>
                <span class="text-blue-100 text-sm">إجمالي المشاهدات</span>
            </div>
            <p class="text-4xl font-black">{{ number_format($stats['total_views'] ?? 0) }}</p>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <x-heroicon-o-users class="w-5 h-5" />
                </div>
                <span class="text-emerald-100 text-sm">زوار فريدون</span>
            </div>
            <p class="text-4xl font-black">{{ number_format($stats['unique_visitors'] ?? 0) }}</p>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <x-heroicon-o-sun class="w-5 h-5" />
                </div>
                <span class="text-amber-100 text-sm">زيارات اليوم</span>
            </div>
            <p class="text-4xl font-black">{{ number_format($stats['today_views'] ?? 0) }}</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <x-heroicon-o-calculator class="w-5 h-5" />
                </div>
                <span class="text-purple-100 text-sm">متوسط يومي</span>
            </div>
            @php
                $avgDaily = $this->days > 0 ? round(($stats['total_views'] ?? 0) / $this->days) : 0;
            @endphp
            <p class="text-4xl font-black">{{ number_format($avgDaily) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Daily Views Chart -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-chart-bar class="w-5 h-5 text-primary-500" />
                    الزيارات اليومية
                </div>
            </x-slot>
            
            <div class="space-y-2">
                @php
                    $dailyViews = $stats['daily_views'] ?? [];
                    $maxViews = max($dailyViews) ?: 1;
                @endphp
                @foreach($dailyViews as $date => $count)
                    @php $percent = round(($count / $maxViews) * 100); @endphp
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500 w-20">{{ \Carbon\Carbon::parse($date)->format('m/d') }}</span>
                        <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-full h-4 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-full rounded-full transition-all" style="width: {{ $percent }}%"></div>
                        </div>
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-200 w-16 text-left">{{ number_format($count) }}</span>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <!-- Hourly Distribution -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5 text-amber-500" />
                    توزيع الزيارات بالساعة
                </div>
            </x-slot>
            
            <div class="grid grid-cols-12 gap-1 h-40 items-end">
                @php $maxHourly = max($hourlyData) ?: 1; @endphp
                @for($hour = 0; $hour < 24; $hour++)
                    @php 
                        $count = $hourlyData[$hour] ?? 0;
                        $height = round(($count / $maxHourly) * 100);
                    @endphp
                    <div class="flex flex-col items-center group">
                        <div class="w-full bg-gradient-to-t from-amber-500 to-orange-400 rounded-t transition-all hover:from-amber-600 hover:to-orange-500 relative"
                             style="height: {{ max($height, 5) }}%"
                             title="{{ $hour }}:00 - {{ number_format($count) }} زيارة">
                            <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold text-gray-600 dark:text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">
                                {{ $count }}
                            </span>
                        </div>
                        @if($hour % 4 == 0)
                            <span class="text-[10px] text-gray-400 mt-1">{{ $hour }}</span>
                        @endif
                    </div>
                @endfor
            </div>
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Page Types -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-5 h-5 text-blue-500" />
                    أنواع الصفحات
                </div>
            </x-slot>
            
            <div class="space-y-3">
                @php $totalPages = array_sum($pageTypes) ?: 1; @endphp
                @foreach($pageTypes as $type => $count)
                    @php $percent = round(($count / $totalPages) * 100); @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ $pageTypeLabels[$type] ?? $type }}</span>
                            <span class="text-sm font-bold">{{ $percent }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <!-- Devices -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-device-phone-mobile class="w-5 h-5 text-emerald-500" />
                    الأجهزة
                </div>
            </x-slot>
            
            @php
                $devices = $stats['by_device'] ?? [];
                $totalDevices = array_sum($devices) ?: 1;
                $deviceLabels = ['mobile' => 'موبايل', 'tablet' => 'تابلت', 'desktop' => 'كمبيوتر'];
                $deviceColors = ['mobile' => 'emerald', 'tablet' => 'purple', 'desktop' => 'blue'];
            @endphp
            
            <div class="flex items-center justify-center gap-4 py-4">
                @foreach($devices as $device => $count)
                    @php $percent = round(($count / $totalDevices) * 100); @endphp
                    <div class="text-center">
                        <div class="w-20 h-20 rounded-full bg-{{ $deviceColors[$device] ?? 'gray' }}-100 dark:bg-{{ $deviceColors[$device] ?? 'gray' }}-900/30 flex items-center justify-center mb-2 mx-auto">
                            <span class="text-2xl font-black text-{{ $deviceColors[$device] ?? 'gray' }}-600">{{ $percent }}%</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $deviceLabels[$device] ?? $device }}</p>
                        <p class="text-xs text-gray-400">{{ number_format($count) }}</p>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <!-- Browsers -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-globe-alt class="w-5 h-5 text-amber-500" />
                    المتصفحات
                </div>
            </x-slot>
            
            <div class="space-y-2">
                @php $totalBrowsers = array_sum($stats['by_browser'] ?? []) ?: 1; @endphp
                @foreach(($stats['by_browser'] ?? []) as $browser => $count)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-800 last:border-0">
                        <span class="text-sm text-gray-600 dark:text-gray-300">{{ $browser }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400">{{ round(($count / $totalBrowsers) * 100) }}%</span>
                            <span class="text-sm font-bold">{{ number_format($count) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>

    <!-- Popular Governorates -->
    <x-filament::section class="mt-6">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-map class="w-5 h-5 text-red-500" />
                أكثر المحافظات زيارة
            </div>
        </x-slot>
        
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach(($stats['popular_governorates'] ?? []) as $index => $gov)
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl p-4 text-center border border-gray-200 dark:border-gray-700">
                    <div class="w-10 h-10 mx-auto mb-2 rounded-full 
                        {{ $index == 0 ? 'bg-gradient-to-br from-yellow-400 to-amber-500' : ($index == 1 ? 'bg-gradient-to-br from-gray-300 to-gray-400' : ($index == 2 ? 'bg-gradient-to-br from-orange-400 to-amber-600' : 'bg-gradient-to-br from-blue-400 to-indigo-500')) }}
                        text-white flex items-center justify-center font-black">
                        {{ $index + 1 }}
                    </div>
                    <h4 class="font-bold text-gray-800 dark:text-gray-200 text-sm">{{ $gov['name'] }}</h4>
                    <p class="text-lg font-black text-primary-600 mt-1">{{ number_format($gov['count']) }}</p>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-panels::page>
