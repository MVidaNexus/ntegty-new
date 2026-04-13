<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
