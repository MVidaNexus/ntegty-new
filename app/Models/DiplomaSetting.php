<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiplomaSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'use_unified_service',
        'unified_service_type',
        'unified_embed_code',
        'unified_pdf_path',
    ];

    protected $casts = [
        'use_unified_service' => 'boolean',
    ];

    /**
     * Get country name in Arabic
     */
    public function getCountryNameAttribute(): string
    {
        return match($this->country_code) {
            'eg' => 'مصر',
            'iq' => 'العراق',
            'ly' => 'ليبيا',
            default => $this->country_code,
        };
    }

    /**
     * Get settings for a country
     */
    public static function forCountry(string $countryCode): ?self
    {
        return static::where('country_code', $countryCode)->first();
    }

    /**
     * Check if unified service is enabled and of specific type
     */
    public function isUnifiedSearch(): bool
    {
        return $this->use_unified_service && $this->unified_service_type === 'search';
    }

    public function isUnifiedEmbed(): bool
    {
        return $this->use_unified_service && $this->unified_service_type === 'embed';
    }

    public function isUnifiedPdf(): bool
    {
        return $this->use_unified_service && $this->unified_service_type === 'pdf';
    }

    /**
     * Get service type label
     */
    public function getServiceTypeLabel(): string
    {
        if (!$this->use_unified_service) {
            return 'تحكم فردي لكل شعبة';
        }

        return match($this->unified_service_type) {
            'search' => 'بحث موحد',
            'embed' => 'إيفريم موحد',
            'pdf' => 'ملف PDF موحد',
            default => 'غير محدد',
        };
    }
}
