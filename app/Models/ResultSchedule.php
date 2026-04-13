<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'exam_type_id',
        'governorate_id',
        'expected_date',
        'note',
        'is_active',
    ];

    protected $casts = [
        'expected_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class);
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    /**
     * Get schedule for specific country, exam type, and optionally governorate
     */
    public static function getSchedule($countrySlug, $examTypeCode = null, $governorateSlug = null)
    {
        $query = self::where('is_active', true)
            ->whereHas('country', function ($q) use ($countrySlug) {
                $q->where('slug', $countrySlug);
            });

        // If governorate is specified, try to find specific schedule first
        if ($governorateSlug) {
            $governorateSchedule = (clone $query)
                ->whereHas('governorate', function ($q) use ($governorateSlug) {
                    $q->where('slug', $governorateSlug);
                })
                ->orderBy('expected_date', 'desc')
                ->first();
            
            if ($governorateSchedule) {
                return $governorateSchedule;
            }
        }

        // Try to find exam type specific schedule
        if ($examTypeCode) {
            // Map common aliases to actual codes
            $codeMap = [
                'diplomas' => 'diploma',
                'preparatory' => 'preparatory',
                'secondary' => 'secondary',
            ];
            $searchCode = $codeMap[$examTypeCode] ?? $examTypeCode;
            
            $examTypeSchedule = (clone $query)
                ->whereNull('governorate_id')
                ->whereHas('examType', function ($q) use ($searchCode) {
                    $q->where('code', 'like', '%' . $searchCode . '%');
                })
                ->orderBy('expected_date', 'desc')
                ->first();
            
            if ($examTypeSchedule) {
                return $examTypeSchedule;
            }
        }

        // Fallback to country-level schedule
        return $query->whereNull('governorate_id')
            ->whereNull('exam_type_id')
            ->orderBy('expected_date', 'desc')
            ->first();
    }

    /**
     * Check if result is declared (governorate declared or past expected date)
     */
    public function isDeclared(): bool
    {
        // If governorate is set and declared
        if ($this->governorate && $this->governorate->is_declared) {
            return true;
        }

        return false;
    }
}
