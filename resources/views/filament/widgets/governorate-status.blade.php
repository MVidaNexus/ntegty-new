<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-map class="w-6 h-6 text-primary-500" />
                <span>حالة المحافظات</span>
            </div>
        </x-slot>

        @php
            $stats = $this->getStats();
            $governorates = $this->getGovernorates();
        @endphp

        <!-- Progress Overview -->
        <div class="mb-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-600 dark:text-gray-300">تقدم اعتماد النتائج</span>
                <span class="text-lg font-black text-primary-600">{{ $stats['declared'] }} / {{ $stats['total'] }}</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-green-500 h-full rounded-full transition-all flex items-center justify-center"
                     style="width: {{ $stats['percent'] }}%">
                    @if($stats['percent'] > 10)
                        <span class="text-[10px] font-bold text-white">{{ $stats['percent'] }}%</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                <span class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    معتمدة: {{ $stats['declared'] }}
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                    قيد الانتظار: {{ $stats['pending'] }}
                </span>
            </div>
        </div>

        <!-- Governorates Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            @foreach($governorates as $gov)
                <div class="relative rounded-xl border-2 p-3 text-center transition-all hover:shadow-lg
                    {{ $gov['is_declared'] 
                        ? 'bg-gradient-to-br from-emerald-50 to-green-50 border-emerald-200 hover:border-emerald-400' 
                        : 'bg-gradient-to-br from-gray-50 to-slate-50 border-gray-200 hover:border-gray-400' }}">
                    
                    @if($gov['is_declared'])
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-br from-emerald-500 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                            <x-heroicon-m-check class="w-4 h-4 text-white" />
                        </div>
                    @endif
                    
                    <div class="w-12 h-12 mx-auto mb-2 rounded-full 
                        {{ $gov['is_declared'] ? 'bg-emerald-100' : 'bg-gray-100' }}
                        flex items-center justify-center">
                        @if($gov['logo'])
                            <img src="{{ asset('storage/' . $gov['logo']) }}" alt="{{ $gov['name'] }}" class="w-8 h-8 object-contain">
                        @else
                            <x-heroicon-o-building-office-2 class="w-6 h-6 {{ $gov['is_declared'] ? 'text-emerald-500' : 'text-gray-400' }}" />
                        @endif
                    </div>
                    
                    <h4 class="font-bold text-sm {{ $gov['is_declared'] ? 'text-emerald-700' : 'text-gray-700' }} truncate">
                        {{ $gov['name'] }}
                    </h4>
                    
                    <p class="text-xs {{ $gov['is_declared'] ? 'text-emerald-500' : 'text-gray-400' }} mt-1">
                        {{ number_format($gov['results_count']) }} نتيجة
                    </p>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
