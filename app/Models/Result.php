<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'seat_number',
        'student_name',
        'governorate_id',
        'exam_type_id',
        'branch_id',
        'system_type',
        'semester',
        'academic_year_id',
        'upload_log_id',
        'subjects_data',
        'total_score',
        'status',
    ];

    // Semester constants
    public const SEMESTER_BOTH = 0;      // الترمين (مجمع)
    public const SEMESTER_FIRST = 1;     // الفصل الدراسي الأول
    public const SEMESTER_SECOND = 2;    // الفصل الدراسي الثاني
    
    public static function getSemesterOptions(): array
    {
        return [
            self::SEMESTER_BOTH => 'الترمين (مجمع)',
            self::SEMESTER_FIRST => 'الفصل الدراسي الأول',
            self::SEMESTER_SECOND => 'الفصل الدراسي الثاني',
        ];
    }
    
    public function getSemesterLabel(): string
    {
        return self::getSemesterOptions()[$this->semester] ?? 'غير محدد';
    }

    protected $casts = [
        'subjects_data' => \App\Casts\JsonWithUnicode::class,
        'total_score' => 'decimal:2',
    ];

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function uploadLog(): BelongsTo
    {
        return $this->belongsTo(UploadLog::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(ExamBranch::class, 'branch_id');
    }

    // Search scopes for optimization
    public function scopeSearchBySeatNumber($query, $seatNumber)
    {
        return $query->where('seat_number', $seatNumber);
    }

    public function scopeSearchByName($query, $name)
    {
        return $query->whereFullText('student_name', $name);
    }

    public function scopeByGovernorate($query, $governorateId)
    {
        return $query->where('governorate_id', $governorateId);
    }

    public function scopeByExamType($query, $examTypeId)
    {
        return $query->where('exam_type_id', $examTypeId);
    }

    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeBySystemType($query, $systemType)
    {
        return $query->where('system_type', $systemType);
    }

    /**
     * Get the calculated status based on passing score
     * Returns stored status if available, otherwise calculates from passing_score
     */
    public function getCalculatedStatusAttribute(): string
    {
        // If status is already set, return it
        if (!empty($this->status)) {
            return $this->status;
        }

        // If total_score is null or negative (absent), return غائب
        if ($this->total_score === null || $this->total_score < 0) {
            return 'غائب';
        }

        // Get passing score from exam type
        $examType = $this->examType;
        if (!$examType || !$examType->passing_score) {
            return 'غير محدد';
        }

        // Calculate status based on passing score
        if ($this->total_score >= $examType->passing_score) {
            return 'ناجح';
        }

        // Check for second round threshold
        if ($examType->second_round_threshold && $this->total_score >= $examType->second_round_threshold) {
            return 'دور ثاني';
        }

        return 'راسب';
    }

    /**
     * Check if student passed
     */
    public function isPassed(): bool
    {
        return $this->calculated_status === 'ناجح';
    }
}
