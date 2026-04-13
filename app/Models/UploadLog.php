<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class UploadLog extends Model
{
    use HasFactory;

    // أنواع الرفع المتاحة
    public const TYPE_EXCEL = 'excel';
    public const TYPE_PDF = 'pdf';
    public const TYPE_EMBED = 'embed';
    public const TYPE_GOVERNORATE_TABLE = 'governorate_table';
    public const TYPE_GOVERNORATE_FILE = 'governorate_file';

    protected $fillable = [
        'user_id',
        'batch_name',
        'filename',
        'file_path',
        'file_type',
        'academic_year_id',
        'exam_type_id',
        'branch_id',
        'system_type',
        'semester',
        'governorate_id',
        'records_count',
        'processed_rows',
        'successful_rows',
        'failed_rows',
        'status',
        'upload_type',
        'error_message',
        'notes',
        'mapping_data',
        'extra_data',
    ];

    protected $casts = [
        'mapping_data' => \App\Casts\JsonWithUnicode::class,
        'extra_data' => \App\Casts\JsonWithUnicode::class,
    ];

    /**
     * الحصول على تسمية نوع الرفع بالعربية
     */
    public function getUploadTypeLabelAttribute(): string
    {
        return match($this->upload_type) {
            self::TYPE_EXCEL => 'ملف Excel',
            self::TYPE_PDF => 'ملف PDF',
            self::TYPE_EMBED => 'رابط خارجي / iFrame',
            self::TYPE_GOVERNORATE_TABLE => 'جدول المحافظات',
            self::TYPE_GOVERNORATE_FILE => 'ملف محافظة',
            default => $this->upload_type ?? 'غير محدد',
        };
    }

    /**
     * الحصول على أيقونة نوع الرفع
     */
    public function getUploadTypeIconAttribute(): string
    {
        return match($this->upload_type) {
            self::TYPE_EXCEL => '📊',
            self::TYPE_PDF => '📄',
            self::TYPE_EMBED => '🌐',
            self::TYPE_GOVERNORATE_TABLE => '📋',
            self::TYPE_GOVERNORATE_FILE => '🗂️',
            default => '📁',
        };
    }

    /**
     * الحصول على لون نوع الرفع
     */
    public function getUploadTypeColorAttribute(): string
    {
        return match($this->upload_type) {
            self::TYPE_EXCEL => 'info',
            self::TYPE_PDF => 'success',
            self::TYPE_EMBED => 'warning',
            self::TYPE_GOVERNORATE_TABLE => 'primary',
            self::TYPE_GOVERNORATE_FILE => 'gray',
            default => 'gray',
        };
    }

    protected static function booted()
    {
        static::deleting(function ($uploadLog) {
            // 1. Delete the file from storage
            if ($uploadLog->file_path && Storage::disk('local')->exists($uploadLog->file_path)) {
                Storage::disk('local')->delete($uploadLog->file_path);
            }

            // 2. Delete associated results (if any)
            // Since we added ON DELETE CASCADE in migration, this might be redundant but safe
            // However, if we want to be explicit or if foreign keys are disabled:
            $uploadLog->results()->delete();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class);
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(ExamBranch::class, 'branch_id');
    }
}
