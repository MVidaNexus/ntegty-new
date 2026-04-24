<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'slug',
        'flag_icon',
        'is_active',
        'government_type',
        'academic_year',
        'semester',
        'telegram_url',
        // SEO fields
        'seo_title',
        'seo_description',
        'seo_keywords',
        // Content fields
        'content_title',
        'content_intro',
        'content_body',
        'show_content_section',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $casts = [
        'is_active' => 'boolean',
        'show_content_section' => 'boolean',
    ];

    public function governorates(): HasMany
    {
        return $this->hasMany(Governorate::class);
    }

    public function examTypes(): HasMany
    {
        return $this->hasMany(ExamType::class);
    }

    /**
     * Get dynamic page title based on country settings
     * 
     * @param bool $includePrefix Whether to include "نتائج شهادات" prefix
     * @param bool $includeSemester Whether to include semester (e.g. for mid-year vs end-year)
     */
    public function getDynamicTitle($includePrefix = true, $includeSemester = true): string
    {
        $title = '';
        
        if ($includePrefix) {
            $title = 'نتائج شهادات ';
        }
        
        // Add country name only (without government type)
        $title .= $this->name_ar;
        
        // Add academic year
        $title .= ' ' . $this->academic_year;
        
        // Add semester if exists and requested
        if ($this->semester && $includeSemester) {
            $title .= ' ' . $this->semester;
        }
        
        return $title;
    }
}
