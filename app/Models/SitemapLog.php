<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitemapLog extends Model
{
    protected $fillable = [
        'sitemap_name',
        'sitemap_type',
        'urls_count',
        'file_size',
        'generation_time',
        'status',
        'error_message',
        'generated_at',
    ];

    protected $casts = [
        'urls_count' => 'integer',
        'file_size' => 'integer',
        'generation_time' => 'decimal:2',
        'generated_at' => 'datetime',
    ];

    /**
     * العلاقة مع الأنواع
     */
    public static function getTypeLabels(): array
    {
        return [
            'index' => 'الفهرس الرئيسي',
            'pages' => 'الصفحات الثابتة',
            'countries' => 'الدول',
            'exam-types' => 'أنواع الشهادات',
            'governorates' => 'المحافظات',
            'branches' => 'الشعب والفروع',
            'students' => 'نتائج الطلاب',
            'schools' => 'المدارس',
            'administrations' => 'الإدارات',
            'top-students' => 'الأوائل',
        ];
    }

    /**
     * الحصول على اسم النوع بالعربي
     */
    public function getTypeLabel(): string
    {
        return self::getTypeLabels()[$this->sitemap_type] ?? $this->sitemap_type;
    }

    /**
     * حجم الملف بصيغة مقروءة
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) return '-';
        
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
