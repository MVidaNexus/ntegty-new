<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class JsonWithUnicode implements CastsAttributes
{
    /**
     * Cast the given value (from database to PHP).
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }
        
        $decoded = json_decode($value, true);
        
        // If decoded successfully as array, return it
        if (is_array($decoded)) {
            return $decoded;
        }
        
        // If decoded as string (old format: "value1,value2"), parse it
        if (is_string($decoded)) {
            return array_map('trim', explode(',', $decoded));
        }
        
        // Fallback: try to split original value by comma if not valid JSON
        if (is_string($value)) {
            $trimmed = trim($value, '"\'');
            return array_map('trim', explode(',', $trimmed));
        }
        
        return null;
    }

    /**
     * Prepare the given value for storage (from PHP to database).
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }
        
        // If it's a string (comma-separated), convert to array first
        if (is_string($value)) {
            $value = array_map('trim', explode(',', $value));
        }
        
        // Always use JSON_UNESCAPED_UNICODE to store Arabic text properly
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
