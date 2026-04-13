<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SitemapSetting extends Model
{
    protected $fillable = [
        'is_enabled',
        'auto_generate',
        'urls_per_sitemap',
        'cache_hours',
        'include_pages',
        'include_countries',
        'include_exam_types',
        'include_governorates',
        'include_branches',
        'include_students',
        'include_schools',
        'include_administrations',
        'include_top_students',
        'priority_home',
        'priority_countries',
        'priority_exam_types',
        'priority_governorates',
        'priority_students',
        'priority_schools',
        'changefreq_home',
        'changefreq_countries',
        'changefreq_students',
        'total_urls',
        'total_sitemaps',
        'sitemaps_stats',
        'last_generated_at',
        'last_submitted_at',
        'custom_urls',
        'excluded_patterns',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'auto_generate' => 'boolean',
        'include_pages' => 'boolean',
        'include_countries' => 'boolean',
        'include_exam_types' => 'boolean',
        'include_governorates' => 'boolean',
        'include_branches' => 'boolean',
        'include_students' => 'boolean',
        'include_schools' => 'boolean',
        'include_administrations' => 'boolean',
        'include_top_students' => 'boolean',
        'urls_per_sitemap' => 'integer',
        'cache_hours' => 'integer',
        'total_urls' => 'integer',
        'total_sitemaps' => 'integer',
        'priority_home' => 'decimal:1',
        'priority_countries' => 'decimal:1',
        'priority_exam_types' => 'decimal:1',
        'priority_governorates' => 'decimal:1',
        'priority_students' => 'decimal:1',
        'priority_schools' => 'decimal:1',
        'sitemaps_stats' => 'array',
        'custom_urls' => 'array',
        'excluded_patterns' => 'array',
        'last_generated_at' => 'datetime',
        'last_submitted_at' => 'datetime',
    ];

    /**
     * الحصول على الإعدادات الحالية (مع كاش)
     */
    public static function getSettings(): ?self
    {
        return Cache::remember('sitemap_settings', 3600, function () {
            return self::first();
        });
    }

    /**
     * مسح الكاش عند التحديث
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('sitemap_settings');
            // مسح كاش خرائط الموقع أيضاً
            self::clearSitemapCache();
        });
    }

    /**
     * مسح كاش خرائط الموقع
     */
    public static function clearSitemapCache(): void
    {
        $cacheKeys = [
            'sitemap:index',
            'sitemap:pages',
            'sitemap:countries',
            'sitemap:exam-types',
            'sitemap:branches',
            'sitemap:study-systems',
            'sitemap:top-students',
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        // مسح خرائط المحافظات
        $countries = Country::where('is_active', true)->pluck('slug');
        foreach ($countries as $slug) {
            Cache::forget("sitemap:governorates-{$slug}");
        }
    }

    /**
     * الحصول على خيارات تردد التحديث
     */
    public static function getChangefreqOptions(): array
    {
        return [
            'always' => 'دائماً',
            'hourly' => 'كل ساعة',
            'daily' => 'يومياً',
            'weekly' => 'أسبوعياً',
            'monthly' => 'شهرياً',
            'yearly' => 'سنوياً',
            'never' => 'أبداً',
        ];
    }
}
