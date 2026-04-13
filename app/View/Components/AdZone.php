<?php

namespace App\View\Components;

use App\Models\AdSlot;
use App\Models\SiteSetting;
use Illuminate\View\Component;
use Illuminate\Database\Eloquent\Collection;

class AdZone extends Component
{
    public Collection $adSlots;
    public bool $adsenseEnabled = false;
    public string $publisherId = '';

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $pageType,
        public string $position
    ) {
        $this->adsenseEnabled = SiteSetting::get('adsense_enabled', '0') === '1';
        $this->publisherId = SiteSetting::get('adsense_publisher_id', '');

        // الحصول على جميع الإعلانات في هذا الموقع
        $this->adSlots = AdSlot::getForPage($pageType, $position);
    }

    /**
     * Determine if the component should be rendered.
     */
    public function shouldRender(): bool
    {
        if (!$this->adsenseEnabled) {
            return false;
        }

        if ($this->adSlots->isEmpty()) {
            return false;
        }

        if (SiteSetting::get('disable_ads_on_admin', '1') === '1' && auth()->check()) {
            return false;
        }

        return true;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.ad-zone');
    }
}
