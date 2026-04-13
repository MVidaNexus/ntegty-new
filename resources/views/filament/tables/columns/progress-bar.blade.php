<div class="flex items-center justify-center py-1">
    @php
        $record = $getRecord();
        $total = $record->records_count ?: 0;
        $processed = $record->processed_rows ?: 0;
        $successful = $record->successful_rows ?: 0;
        $status = $record->status;
        
        // Calculate percentage
        $percentage = $total > 0 ? round(($processed / $total) * 100) : 0;
        
        // Calculate stroke
        $radius = 18;
        $circumference = 2 * 3.14159 * $radius;
        $offset = $circumference - ($circumference * $percentage / 100);
    @endphp

    @if($status === 'pending')
        <div class="inline-flex items-center gap-1.5 px-2 py-1 bg-gray-50 rounded-full">
            <span class="relative flex h-2 w-2">
                <span class="absolute inline-flex h-full w-full rounded-full bg-gray-400 opacity-50"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-500"></span>
            </span>
            <span class="text-xs font-medium text-gray-600">قيد الانتظار</span>
        </div>
    @elseif($status === 'processing')
        <div class="inline-flex items-center gap-2.5 px-2.5 py-1.5 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg border border-emerald-200">
            <!-- Circular Progress -->
            <div class="relative inline-flex items-center justify-center">
                <svg class="w-11 h-11 -rotate-90">
                    <!-- Background circle -->
                    <circle 
                        cx="22" 
                        cy="22" 
                        r="{{ $radius }}" 
                        stroke="#e5e7eb" 
                        stroke-width="3.5" 
                        fill="none"
                    />
                    <!-- Progress circle with gradient -->
                    <circle 
                        cx="22" 
                        cy="22" 
                        r="{{ $radius }}" 
                        stroke="url(#gradient)" 
                        stroke-width="3.5" 
                        fill="none" 
                        stroke-dasharray="{{ $circumference }}"
                        stroke-dashoffset="{{ $offset }}"
                        stroke-linecap="round"
                        class="transition-all duration-700 ease-out"
                    />
                    <defs>
                        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#14b8a6;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                </svg>
                <!-- Percentage in center -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xs font-bold bg-gradient-to-br from-emerald-600 to-teal-600 bg-clip-text text-transparent">{{ $percentage }}%</span>
                </div>
            </div>
            <!-- Count info -->
            <div class="flex flex-col leading-tight">
                <span class="text-sm font-bold text-emerald-700">{{ number_format($processed) }}</span>
                <span class="text-[10px] text-emerald-600/70">من {{ number_format($total) }}</span>
            </div>
        </div>
    @elseif($status === 'completed')
        <div class="inline-flex items-center gap-2 px-2.5 py-1.5 bg-gradient-to-br from-emerald-50 to-green-50 rounded-lg border border-emerald-200">
            <div class="flex items-center justify-center w-8 h-8 bg-emerald-100 rounded-full">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="flex flex-col leading-tight">
                <span class="text-sm font-bold text-emerald-700">{{ number_format($successful) }}</span>
                <span class="text-[10px] text-emerald-600/70">سجل</span>
            </div>
        </div>
    @elseif($status === 'failed')
        <div class="inline-flex items-center gap-2 px-2.5 py-1.5 bg-red-50 rounded-lg border border-red-200">
            <div class="flex items-center justify-center w-8 h-8 bg-red-100 rounded-full">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-red-700">فشل</span>
        </div>
    @else
        <span class="text-gray-400 text-xs">-</span>
    @endif
</div>
