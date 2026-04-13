<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class SocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'url',
        'label',
        'scope_type',
        'scope_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Available platforms with icons and colors
     */
    public static array $platforms = [
        'telegram' => [
            'name' => 'تيليجرام',
            'icon' => 'fa-brands fa-telegram',
            'color' => 'bg-sky-500 hover:bg-sky-600',
            'text_color' => 'text-sky-500',
        ],
        'whatsapp' => [
            'name' => 'واتساب',
            'icon' => 'fa-brands fa-whatsapp',
            'color' => 'bg-green-500 hover:bg-green-600',
            'text_color' => 'text-green-500',
        ],
        'facebook' => [
            'name' => 'فيسبوك',
            'icon' => 'fa-brands fa-facebook-f',
            'color' => 'bg-blue-600 hover:bg-blue-700',
            'text_color' => 'text-blue-600',
        ],
        'instagram' => [
            'name' => 'انستجرام',
            'icon' => 'fa-brands fa-instagram',
            'color' => 'bg-gradient-to-br from-purple-500 via-pink-500 to-orange-400 hover:from-purple-600 hover:via-pink-600 hover:to-orange-500',
            'text_color' => 'text-pink-500',
        ],
        'twitter' => [
            'name' => 'تويتر / X',
            'icon' => 'fa-brands fa-x-twitter',
            'color' => 'bg-black hover:bg-gray-800',
            'text_color' => 'text-black',
        ],
        'youtube' => [
            'name' => 'يوتيوب',
            'icon' => 'fa-brands fa-youtube',
            'color' => 'bg-red-600 hover:bg-red-700',
            'text_color' => 'text-red-600',
        ],
        'tiktok' => [
            'name' => 'تيك توك',
            'icon' => 'fa-brands fa-tiktok',
            'color' => 'bg-black hover:bg-gray-800',
            'text_color' => 'text-black',
        ],
        'linkedin' => [
            'name' => 'لينكد إن',
            'icon' => 'fa-brands fa-linkedin-in',
            'color' => 'bg-blue-700 hover:bg-blue-800',
            'text_color' => 'text-blue-700',
        ],
        'snapchat' => [
            'name' => 'سناب شات',
            'icon' => 'fa-brands fa-snapchat',
            'color' => 'bg-yellow-400 hover:bg-yellow-500',
            'text_color' => 'text-yellow-500',
        ],
        'website' => [
            'name' => 'موقع ويب',
            'icon' => 'fa-solid fa-globe',
            'color' => 'bg-gray-600 hover:bg-gray-700',
            'text_color' => 'text-gray-600',
        ],
    ];

    /**
     * Get platform options for select
     */
    public static function getPlatformOptions(): array
    {
        $options = [];
        foreach (self::$platforms as $key => $platform) {
            $options[$key] = $platform['name'];
        }
        return $options;
    }

    /**
     * Get platform info
     */
    public function getPlatformInfo(): array
    {
        return self::$platforms[$this->platform] ?? [
            'name' => $this->platform,
            'icon' => 'fa-solid fa-link',
            'color' => 'bg-gray-500 hover:bg-gray-600',
            'text_color' => 'text-gray-500',
        ];
    }

    /**
     * Get icon class
     */
    public function getIconClass(): string
    {
        return $this->getPlatformInfo()['icon'];
    }

    /**
     * Get button color class
     */
    public function getColorClass(): string
    {
        return $this->getPlatformInfo()['color'];
    }

    /**
     * Get display label
     */
    public function getDisplayLabel(): string
    {
        return $this->label ?? $this->getPlatformInfo()['name'];
    }

    /**
     * Relationship: Country (when scope_type = country)
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'scope_id');
    }

    /**
     * Relationship: ExamType (when scope_type = exam_type)
     */
    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class, 'scope_id');
    }

    /**
     * Get scope name for display
     */
    public function getScopeName(): string
    {
        return match($this->scope_type) {
            'default' => 'الموقع بالكامل',
            'country' => $this->country?->name_ar ?? 'دولة غير معروفة',
            'exam_type' => $this->examType?->name_ar . ' - ' . ($this->examType?->country?->name_ar ?? '') ?? 'شهادة غير معروفة',
        };
    }

    /**
     * Scope: Active links only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Default links
     */
    public function scopeDefault($query)
    {
        return $query->where('scope_type', 'default');
    }

    /**
     * Scope: Country links
     */
    public function scopeForCountry($query, int $countryId)
    {
        return $query->where('scope_type', 'country')->where('scope_id', $countryId);
    }

    /**
     * Scope: ExamType links
     */
    public function scopeForExamType($query, int $examTypeId)
    {
        return $query->where('scope_type', 'exam_type')->where('scope_id', $examTypeId);
    }

    /**
     * Get social links for a specific page context
     * Priority: ExamType > Country > Default
     */
    public static function getForContext(?int $examTypeId = null, ?int $countryId = null): Collection
    {
        // First check for exam type specific links
        if ($examTypeId) {
            $links = self::active()->forExamType($examTypeId)->orderBy('sort_order')->get();
            if ($links->isNotEmpty()) {
                return $links;
            }
        }

        // Then check for country specific links
        if ($countryId) {
            $links = self::active()->forCountry($countryId)->orderBy('sort_order')->get();
            if ($links->isNotEmpty()) {
                return $links;
            }
        }

        // Fall back to default links
        return self::active()->default()->orderBy('sort_order')->get();
    }
}
