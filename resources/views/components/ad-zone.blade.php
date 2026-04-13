@foreach($adSlots as $adSlot)
    @if($adSlot->is_active)
        <x-ad-unit :slug="$adSlot->slug" />
    @endif
@endforeach
