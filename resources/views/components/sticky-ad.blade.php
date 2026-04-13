@if($adSlot)
    @php
        $hideMobile = !$adSlot->show_on_mobile ? 'hidden sm:block' : '';
        $hideDesktop = !$adSlot->show_on_desktop ? 'sm:hidden' : '';
    @endphp

    <div x-data="{ showStickyAd: true }" 
         x-show="showStickyAd"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-full"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-full"
         class="fixed bottom-0 left-0 right-0 z-40 {{ $hideMobile }} {{ $hideDesktop }} no-print"
         style="box-shadow: 0 -4px 20px rgba(0,0,0,0.15);">
        
        {{-- زر الإغلاق --}}
        <button @click="showStickyAd = false; localStorage.setItem('hideStickyAd', Date.now())" 
                class="absolute -top-8 right-2 sm:right-4 bg-slate-800 hover:bg-slate-700 text-white rounded-t-lg px-3 py-1 text-xs font-bold flex items-center gap-1 transition-colors"
                title="إغلاق الإعلان">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span class="hidden sm:inline">إغلاق</span>
        </button>

        {{-- محتوى الإعلان --}}
        <div class="bg-white border-t border-slate-200 py-2 px-2 sm:px-4"
             style="{{ $adSlot->custom_style }}">
            <div class="max-w-4xl mx-auto">
                @if($adSlot->ad_format === 'custom' && !empty($adSlot->custom_code))
                    {!! $adSlot->custom_code !!}
                @else
                    @php $slotId = $adSlot->slot_id ?: ''; @endphp
                    <ins class="adsbygoogle"
                         style="display:block"
                         data-ad-client="{{ $publisherId }}"
                         @if($slotId) data-ad-slot="{{ $slotId }}" @endif
                         data-ad-format="auto"
                         data-full-width-responsive="true"></ins>
                    <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
                @endif
            </div>
        </div>
    </div>

    {{-- سكربت لإخفاء الإعلان مؤقتاً --}}
    <script>
        document.addEventListener('alpine:init', () => {
            const hideTime = localStorage.getItem('hideStickyAd');
            if (hideTime) {
                const hoursSinceHide = (Date.now() - parseInt(hideTime)) / (1000 * 60 * 60);
                // أظهر الإعلان مرة أخرى بعد ساعة
                if (hoursSinceHide < 1) {
                    Alpine.store('stickyAdHidden', true);
                } else {
                    localStorage.removeItem('hideStickyAd');
                }
            }
        });
    </script>
@endif
