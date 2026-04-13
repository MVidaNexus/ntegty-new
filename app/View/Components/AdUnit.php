<?php

namespace App\View\Components;

use App\Models\AdSlot;
use App\Models\SiteSetting;
use Illuminate\View\Component;

class AdUnit extends Component
{
    public ?AdSlot $adSlot = null;
    public bool $adsenseEnabled = false;
    public string $publisherId = '';

    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $slug = null,
        public ?string $pageType = null,
        public ?string $position = null
    ) {
        $this->adsenseEnabled = SiteSetting::get('adsense_enabled', '0') === '1';
        $this->publisherId = SiteSetting::get('adsense_publisher_id', '');

        // الحصول على الإعلان
        if ($slug) {
            $this->adSlot = AdSlot::getBySlug($slug);
        } elseif ($pageType && $position) {
            $ads = AdSlot::getForPage($pageType, $position);
            $this->adSlot = $ads->first();
        }
    }

    /**
     * Determine if the component should be rendered.
     */
    public function shouldRender(): bool
    {
        // لا تعرض إذا كانت الإعلانات معطلة
        if (!$this->adsenseEnabled) {
            return false;
        }

        // لا تعرض إذا لا يوجد معرف ناشر ولا كود مخصص
        if (empty($this->publisherId) && (!$this->adSlot || $this->adSlot->ad_format !== 'custom')) {
            return false;
        }

        // لا تعرض إذا لا يوجد إعلان
        if (!$this->adSlot || !$this->adSlot->is_active) {
            return false;
        }

        // لا تعرض للمشرفين إذا كان الخيار مفعل
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
        return view('components.ad-unit');
    }
}
