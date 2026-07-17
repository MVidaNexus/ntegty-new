<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'content',
        'image_path',
        'summary',
        'is_published',
        'published_at',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope a query to only include published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Helper to get category name in Arabic.
     */
    public function getCategoryNameArAttribute(): string
    {
        return match($this->category) {
            'results' => 'نتائج الامتحانات',
            'alternatives' => 'بدائل الثانوية',
            'capabilities' => 'اختبارات القدرات',
            'grades' => 'توزيع الدرجات',
            default => 'أخبار تعليمية',
        };
    }
}
