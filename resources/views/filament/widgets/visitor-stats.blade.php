<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-chart-bar class="w-6 h-6 text-primary-500" />
                <span>إحصائيات الزيارات</span>
                <span class="text-xs text-gray-400 font-normal">(آخر 7 أيام)</span>
            </div>
        </x-slot>

        @php
            $stats = $this->getStats();
            $chartData = $this->getDailyChartData();
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <!-- Total Views -->
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">إجمالي المشاهدات</p>
                        <p class="text-3xl font-black mt-1">{{ number_format($stats['total_views'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <x-heroicon-o-eye class="w-6 h-6" />
                    </div>
                </div>
            </div>

            <!-- Unique Visitors -->
            <div class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100 text-sm font-medium">زوار فريدون</p>
                        <p class="text-3xl font-black mt-1">{{ number_format($stats['unique_visitors'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <x-heroicon-o-users class="w-6 h-6" />
                    </div>
                </div>
            </div>

            <!-- Today Views -->
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm font-medium">زيارات اليوم</p>
                        <p class="text-3xl font-black mt-1">{{ number_format($stats['today_views'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <x-heroicon-o-sun class="w-6 h-6" />
                    </div>
                </div>
            </div>

            <!-- Yesterday Views -->
            <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">زيارات أمس</p>
                        <p class="text-3xl font-black mt-1">{{ number_format($stats['yesterday_views'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <x-heroicon-o-moon class="w-6 h-6" />
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Devices Chart -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                <h4 class="font-bold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <x-heroicon-o-device-phone-mobile class="w-5 h-5 text-blue-500" />
                    الأجهزة
                </h4>
                <div class="space-y-3">
                    @php
                        $devices = $stats['by_device'] ?? [];
                        $totalDevices = array_sum($devices) ?: 1;
                        $deviceIcons = ['mobile' => 'device-phone-mobile', 'tablet' => 'device-tablet', 'desktop' => 'computer-desktop'];
                        $deviceNames = ['mobile' => 'موبايل', 'tablet' => 'تابلت', 'desktop' => 'كمبيوتر'];
                        $deviceColors = ['mobile' => 'blue', 'tablet' => 'purple', 'desktop' => 'emerald'];
                    @endphp
                    @forelse($devices as $device => $count)
                        @php $percent = round(($count / $totalDevices) * 100); @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-600 dark:text-gray-300 flex items-center gap-1">
                                    @if(isset($deviceIcons[$device]))
                                        <x-dynamic-component :component="'heroicon-o-' . $deviceIcons[$device]" class="w-4 h-4" />
                                    @endif
                                    {{ $deviceNames[$device] ?? $device }}
                                </span>
                                <span class="text-sm font-bold">{{ $percent }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-{{ $deviceColors[$device] ?? 'gray' }}-500 h-2 rounded-full transition-all" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm text-center py-4">لا توجد بيانات</p>
                    @endforelse
                </div>
            </div>

            <!-- Browsers Chart -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                <h4 class="font-bold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <x-heroicon-o-globe-alt class="w-5 h-5 text-amber-500" />
                    المتصفحات
                </h4>
                <div class="space-y-3">
                    @php
                        $browsers = $stats['by_browser'] ?? [];
                        $totalBrowsers = array_sum($browsers) ?: 1;
                        $browserColors = ['Chrome' => 'blue', 'Firefox' => 'orange', 'Safari' => 'sky', 'Edge' => 'green', 'Opera' => 'red'];
                    @endphp
                    @forelse($browsers as $browser => $count)
                        @php $percent = round(($count / $totalBrowsers) * 100); @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ $browser }}</span>
                                <span class="text-sm font-bold">{{ number_format($count) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-{{ $browserColors[$browser] ?? 'gray' }}-500 h-2 rounded-full transition-all" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm text-center py-4">لا توجد بيانات</p>
                    @endforelse
                </div>
            </div>

            <!-- Popular Governorates -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                <h4 class="font-bold text-gray-700 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <x-heroicon-o-map-pin class="w-5 h-5 text-red-500" />
                    أكثر المحافظات زيارة
                </h4>
                <div class="space-y-2">
                    @forelse(($stats['popular_governorates'] ?? []) as $index => $gov)
                        <div class="flex items-center justify-between py-2 {{ $index > 0 ? 'border-t border-gray-200 dark:border-gray-700' : '' }}">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-gradient-to-br from-red-400 to-rose-600 text-white text-xs flex items-center justify-center font-bold">
                                    {{ $index + 1 }}
                                </span>
                                <span class="text-sm text-gray-700 dark:text-gray-200">{{ $gov['name'] }}</span>
                            </div>
                            <span class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ number_format($gov['count']) }}</span>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm text-center py-4">لا توجد بيانات</p>
                    @endforelse
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
