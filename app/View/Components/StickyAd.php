<?php

namespace App\View\Components;

use App\Models\AdSlot;
use App\Models\SiteSetting;
use Illuminate\View\Component;

class StickyAd extends Component
{
    public ?AdSlot $adSlot = null;
    public bool $adsenseEnabled = false;
    public string $publisherId = '';

    public function __construct()
    {
        $this->adsenseEnabled = SiteSetting::get('adsense_enabled', '0') === '1';
        $this->publisherId = SiteSetting::get('adsense_publisher_id', '');
        
        // الحصول على الإعلان المعلق
        $ads = AdSlot::where('position', 'sticky_bottom')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();
        
        $this->adSlot = $ads;
    }

    public function shouldRender(): bool
    {
        if (!$this->adsenseEnabled) {
            return false;
        }

        if (!$this->adSlot) {
            return false;
        }

        if (SiteSetting::get('disable_ads_on_admin', '1') === '1' && auth()->check()) {
            return false;
        }

        return true;
    }

    public function render()
    {
        return view('components.sticky-ad');
    }
}
