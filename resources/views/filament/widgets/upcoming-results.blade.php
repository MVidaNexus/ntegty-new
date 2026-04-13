<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-clock style="width: 1.5rem; height: 1.5rem;" class="text-primary-500" />
                <span>مواعيد النتائج القادمة</span>
            </div>
        </x-slot>

        <div class="space-y-4">
            @forelse($this->getUpcomingResults() as $result)
                <div class="flex items-center justify-between p-4 rounded-xl border-2 transition-all hover:shadow-md
                    {{ $result['is_soon'] ? 'bg-gradient-to-r from-amber-50 to-orange-50 border-amber-200' : 'bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200' }}">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-14 h-14 rounded-xl flex flex-col items-center justify-center
                            {{ $result['is_soon'] ? 'bg-gradient-to-br from-amber-500 to-orange-600' : 'bg-gradient-to-br from-blue-500 to-indigo-600' }} text-white shadow-lg">
                            <span class="text-xl font-black leading-none">{{ $result['days'] }}</span>
                            <span class="text-[10px] font-medium">يوم</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">{{ $result['name'] }}</h4>
                            <div class="flex items-center gap-3 mt-1 text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    <x-heroicon-m-calendar-days style="width: 1rem; height: 1rem;" />
                                    {{ $result['date'] }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <x-heroicon-m-clock style="width: 1rem; height: 1rem;" />
                                    {{ $result['time'] }}
                                </span>
                            </div>
                            @if($result['note'])
                                <p class="text-xs text-gray-400 mt-1">{{ $result['note'] }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-left">
                        <div class="px-4 py-2 rounded-lg {{ $result['is_soon'] ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }} font-bold text-sm">
                            <div class="flex items-center gap-1">
                                <x-heroicon-m-fire style="width: 1rem; height: 1rem;" />
                                باقي {{ $result['remaining'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <x-heroicon-o-calendar style="width: 3rem; height: 3rem;" class="mx-auto mb-3 opacity-50" />
                    <p>لا توجد مواعيد قادمة حالياً</p>
                </div>
            @endforelse
        </div>

        @if(count($this->getDeclaredResults()) > 0)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-bold text-gray-700 mb-3 flex items-center gap-2">
                    <x-heroicon-o-check-badge style="width: 1.25rem; height: 1.25rem;" class="text-emerald-500" />
                    النتائج المعتمدة حالياً
                </h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($this->getDeclaredResults() as $declared)
                        <div class="inline-flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-xl hover:shadow-md hover:border-emerald-400 transition-all">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center flex-shrink-0">
                                <x-heroicon-s-check style="width: 1.25rem; height: 1.25rem;" class="text-white" />
                            </div>
                            <div class="min-w-0">
                                <h5 class="font-bold text-gray-800 text-sm truncate">{{ $declared['name'] }}</h5>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span class="flex items-center gap-0.5">
                                        <x-heroicon-m-clock style="width: 0.75rem; height: 0.75rem;" />
                                        {{ $declared['declared_at'] }}
                                    </span>
                                    @if($declared['country'])
                                        <span class="flex items-center gap-0.5 px-1.5 py-0.5 bg-gray-100 rounded text-gray-600">
                                            <x-heroicon-m-map-pin style="width: 0.75rem; height: 0.75rem;" />
                                            {{ $declared['country'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse flex-shrink-0"></span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
