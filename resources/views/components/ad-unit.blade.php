@if($adSlot)
    @php
        $deviceClasses = [];
        if (!$adSlot->show_on_mobile) {
            $deviceClasses[] = 'hidden sm:block';
        }
        if (!$adSlot->show_on_desktop) {
            $deviceClasses[] = 'sm:hidden';
        }
        $deviceClass = implode(' ', $deviceClasses);
        $uniqueId = 'ad-' . $adSlot->slug . '-' . uniqid();
    @endphp

    <div class="ad-container ad-{{ $adSlot->slug }} {{ $deviceClass }} my-4" 
         id="{{ $uniqueId }}"
         style="{{ $adSlot->custom_style }}"
         data-ad-position="{{ $adSlot->position }}"
         data-ad-page="{{ $adSlot->page_type }}"
         data-ad-lazy="true">
        
        @if($adSlot->ad_format === 'custom' && !empty($adSlot->custom_code))
            {{-- كود إعلان مخصص - يتم تحميله مباشرة --}}
            {!! $adSlot->custom_code !!}
        @else
            {{-- إعلان AdSense - Lazy Load --}}
            @php
                $slotId = $adSlot->slot_id ?: '';
                $slotAttr = $slotId ? "data-ad-slot=\"{$slotId}\"" : '';
            @endphp

            @switch($adSlot->ad_format)
                @case('display')
                    <ins class="adsbygoogle ad-lazy-ins"
                         style="display:block"
                         data-ad-client="{{ $publisherId }}"
                         data-ad-slot="{{ $slotId }}"
                         data-ad-format="rectangle,horizontal"
                         data-full-width-responsive="true"></ins>
                    @break

                @case('in-article')
                    <ins class="adsbygoogle ad-lazy-ins"
                         style="display:block; text-align:center;"
                         data-ad-layout="in-article"
                         data-ad-format="fluid"
                         data-ad-client="{{ $publisherId }}"
                         data-ad-slot="{{ $slotId }}"></ins>
                    @break

                @case('in-feed')
                    @php $layout = $adSlot->ad_layout ?: '-fb'; @endphp
                    <ins class="adsbygoogle ad-lazy-ins"
                         style="display:block"
                         data-ad-format="fluid"
                         data-ad-layout-key="{{ $layout }}"
                         data-ad-client="{{ $publisherId }}"
                         data-ad-slot="{{ $slotId }}"></ins>
                    @break

                @case('multiplex')
                    <ins class="adsbygoogle ad-lazy-ins"
                         style="display:block"
                         data-ad-format="autorelaxed"
                         data-ad-client="{{ $publisherId }}"
                         data-ad-slot="{{ $slotId }}"></ins>
                    @break

                @default
                    {{-- Auto format --}}
                    <ins class="adsbygoogle ad-lazy-ins"
                         style="display:block"
                         data-ad-client="{{ $publisherId }}"
                         {!! $slotAttr !!}
                         data-ad-format="auto"
                         data-full-width-responsive="true"></ins>
            @endswitch
        @endif
    </div>
@endif

{{-- Lazy Load Script (يتم تحميله مرة واحدة فقط) --}}
@once
@push('scripts')
<script>
(function() {
    'use strict';
    
    // دالة التحميل المتتابع (Sequential Loading)
    function loadAdsSequentially() {
        // ننتظر قليلاً للتأكد من أن المكتبة قد تم تحميلها
        if (typeof window.adsbygoogle === 'undefined') {
            setTimeout(loadAdsSequentially, 100);
            return;
        }

        // البحث عن كل وحدات الإعلانات التي لم يتم تحميلها بعد
        // نبحث عن العناصر التي لها كلاس adsbygoogle ولكن ليس لها check-status
        const ads = document.querySelectorAll('.ad-lazy-ins:not([data-adsbygoogle-status="done"])');
        
        if (ads.length === 0) return;

        console.log(`Found ${ads.length} ads to load sequentially.`);

        let index = 0;

        function pushNextAd() {
            if (index >= ads.length) return;

            const ad = ads[index];
            
            // تحقق إضافي لتجنب تكرار التحميل إذا قام AdSense بملئه بالفعل
            if (!ad.getAttribute('data-adsbygoogle-status')) {
                try {
                    (adsbygoogle = window.adsbygoogle || []).push({});
                    // لا نضع علامة هنا لأن AdSense سيضع data-adsbygoogle-status="done" عند الانتهاء
                    // لكننا ننتظر وقتاً كافياً بين كل طلب
                } catch (e) {
                    console.error('AdSense push error:', e);
                }
            }

            index++;
            // ننتظر 100ms قبل طلب الإعلان التالي
            setTimeout(pushNextAd, 100);
        }

        // البدء في السلسلة
        pushNextAd();
    }
    
    // تشغيل عند تحميل الصفحة
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadAdsSequentially);
    } else {
        loadAdsSequentially();
    }
    
    // إعادة التشغيل عند تحميل محتوى جديد (للـ SPA أو Ajax)
    window.setupLazyAds = loadAdsSequentially;
})();
</script>
@endpush
@endonce
