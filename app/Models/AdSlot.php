<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AdSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'page_type',
        'position',
        'ad_format',
        'ad_layout',
        'slot_id',
        'custom_channel',
        'is_active',
        'show_on_mobile',
        'show_on_desktop',
        'custom_style',
        'custom_code',
        'sort_order',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_mobile' => 'boolean',
        'show_on_desktop' => 'boolean',
    ];

    // أنواع الصفحات
    public const PAGE_TYPES = [
        'home' => 'الصفحة الرئيسية',
        'country' => 'صفحات الدول',
        'governorate' => 'صفحات المحافظات',
        'result' => 'صفحات النتائج',
        'global' => 'جميع الصفحات',
    ];

    // أماكن الإعلانات
    public const POSITIONS = [
        'header_top' => 'أعلى الهيدر',
        'header_bottom' => 'أسفل الهيدر',
        'before_title' => 'قبل العنوان الرئيسي',
        'after_title' => 'بعد العنوان الرئيسي',
        'before_search' => 'قبل مربع البحث',
        'inside_search' => 'داخل مربع البحث',
        'after_search' => 'بعد مربع البحث',
        'before_content' => 'قبل المحتوى',
        'after_content' => 'بعد المحتوى',
        'sidebar' => 'الشريط الجانبي',
        'between_results' => 'بين النتائج',
        'footer_top' => 'أعلى الفوتر',
        'sticky_bottom' => 'إعلان معلق بالأسفل',
        'popup' => 'إعلان منبثق',
    ];

    // أشكال الإعلانات
    public const AD_FORMATS = [
        'auto' => 'تلقائي (Auto)',
        'display' => 'إعلان عرض (Display)',
        'in-article' => 'داخل المقال (In-Article)',
        'in-feed' => 'في الموجز (In-Feed)',
        'multiplex' => 'متعدد (Multiplex)',
        'custom' => 'كود مخصص',
    ];

    /**
     * الحصول على إعلانات صفحة معينة
     */
    public static function getForPage(string $pageType, ?string $position = null): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "ad_slots_{$pageType}" . ($position ? "_{$position}" : '');
        
        return Cache::remember($cacheKey, 3600, function () use ($pageType, $position) {
            $query = self::where('is_active', true)
                ->where(function ($q) use ($pageType) {
                    $q->where('page_type', $pageType)
                      ->orWhere('page_type', 'global');
                })
                ->orderBy('sort_order');
            
            if ($position) {
                $query->where('position', $position);
            }
            
            return $query->get();
        });
    }

    /**
     * الحصول على إعلان معين
     */
    public static function getBySlug(string $slug): ?self
    {
        return Cache::remember("ad_slot_{$slug}", 3600, function () use ($slug) {
            return self::where('slug', $slug)
                ->where('is_active', true)
                ->first();
        });
    }

    /**
     * تنظيف الكاش عند التحديث
     */
    protected static function booted()
    {
        static::saved(function () {
            self::clearCache();
        });
        
        static::deleted(function () {
            self::clearCache();
        });
    }

    public static function clearCache(): void
    {
        $pageTypes = array_keys(self::PAGE_TYPES);
        $positions = array_keys(self::POSITIONS);
        
        foreach ($pageTypes as $type) {
            Cache::forget("ad_slots_{$type}");
            foreach ($positions as $pos) {
                Cache::forget("ad_slots_{$type}_{$pos}");
            }
        }
        
        // Clear all individual slug caches
        foreach (self::pluck('slug') as $slug) {
            Cache::forget("ad_slot_{$slug}");
        }
    }

    /**
     * هل يجب عرض الإعلان حسب نوع الجهاز
     */
    public function shouldShow(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // التحقق من نوع الجهاز (يتم عبر CSS/JS في الـ frontend)
        return true;
    }

    /**
     * الحصول على كود الإعلان
     */
    public function getAdCode(): string
    {
        // إذا كان هناك كود مخصص
        if ($this->ad_format === 'custom' && !empty($this->custom_code)) {
            return $this->custom_code;
        }

        // الحصول على معرف الناشر من الإعدادات
        $publisherId = SiteSetting::get('adsense_publisher_id', '');
        
        if (empty($publisherId)) {
            return '<!-- AdSense Publisher ID not configured -->';
        }

        $slotId = $this->slot_id ?: '';
        $channel = $this->custom_channel ?: '';
        
        // بناء كود AdSense
        $deviceClass = $this->getDeviceClasses();
        $customStyle = $this->custom_style ?: '';
        
        $code = "<div class=\"ad-container ad-{$this->slug} {$deviceClass}\" style=\"{$customStyle}\">\n";
        
        switch ($this->ad_format) {
            case 'display':
                $code .= $this->getDisplayAdCode($publisherId, $slotId);
                break;
            case 'in-article':
                $code .= $this->getInArticleAdCode($publisherId, $slotId);
                break;
            case 'in-feed':
                $code .= $this->getInFeedAdCode($publisherId, $slotId);
                break;
            case 'multiplex':
                $code .= $this->getMultiplexAdCode($publisherId, $slotId);
                break;
            default: // auto
                $code .= $this->getAutoAdCode($publisherId, $slotId);
        }
        
        $code .= "</div>\n";
        
        return $code;
    }

    protected function getDeviceClasses(): string
    {
        $classes = [];
        if (!$this->show_on_mobile) {
            $classes[] = 'hidden-mobile';
        }
        if (!$this->show_on_desktop) {
            $classes[] = 'hidden-desktop';
        }
        return implode(' ', $classes);
    }

    protected function getAutoAdCode(string $publisherId, string $slotId): string
    {
        $slotAttr = $slotId ? "data-ad-slot=\"{$slotId}\"" : '';
        return <<<HTML
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="{$publisherId}"
     {$slotAttr}
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
HTML;
    }

    protected function getDisplayAdCode(string $publisherId, string $slotId): string
    {
        return <<<HTML
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="{$publisherId}"
     data-ad-slot="{$slotId}"
     data-ad-format="rectangle,horizontal"
     data-full-width-responsive="true"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
HTML;
    }

    protected function getInArticleAdCode(string $publisherId, string $slotId): string
    {
        return <<<HTML
<ins class="adsbygoogle"
     style="display:block; text-align:center;"
     data-ad-layout="in-article"
     data-ad-format="fluid"
     data-ad-client="{$publisherId}"
     data-ad-slot="{$slotId}"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
HTML;
    }

    protected function getInFeedAdCode(string $publisherId, string $slotId): string
    {
        $layout = $this->ad_layout ?: '-fb';
        return <<<HTML
<ins class="adsbygoogle"
     style="display:block"
     data-ad-format="fluid"
     data-ad-layout-key="{$layout}"
     data-ad-client="{$publisherId}"
     data-ad-slot="{$slotId}"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
HTML;
    }

    protected function getMultiplexAdCode(string $publisherId, string $slotId): string
    {
        return <<<HTML
<ins class="adsbygoogle"
     style="display:block"
     data-ad-format="autorelaxed"
     data-ad-client="{$publisherId}"
     data-ad-slot="{$slotId}"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
HTML;
    }
}
