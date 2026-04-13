<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ColumnMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_type_id',
        'mapping_name',
        'column_map',
    ];

    protected $casts = [
        'column_map' => \App\Casts\JsonWithUnicode::class,
    ];

    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class);
    }
}
