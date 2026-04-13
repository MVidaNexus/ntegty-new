<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_type_id',
        'name_ar',
        'name_en',
        'code',
        'slug',
        'icon',
        'color',
        'total_score',
        'passing_score',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'passing_score' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class);
    }

    /**
     * Get results for this branch
     */
    public function results(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Result::class, 'branch_id');
    }

    /**
     * حساب حالة الطالب بناءً على المجموع
     */
    public function calculateStatus(float $score): string
    {
        if (!$this->passing_score) {
            return 'غير محدد';
        }

        if ($score >= $this->passing_score) {
            return 'ناجح';
        }

        return 'راسب';
    }
}
