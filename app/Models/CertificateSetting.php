<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CertificateSetting extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'page_title',
        'page_description',
        'background_image',
        'canvas_width',
        'canvas_height',
        // Name settings
        'name_font_family',
        'name_font_size',
        'name_position_x',
        'name_position_y',
        'name_position_x_female',
        'name_position_y_female',
        'primary_color',
        'secondary_color',
        'text_color',
        'font_family',
        // Line 1 Male
        'line1_text_male',
        'line1_font_family',
        'line1_font_size',
        'line1_color',
        'line1_position_x',
        'line1_position_y',
        // Line 1 Female
        'line1_text_female',
        'line1_font_family_female',
        'line1_font_size_female',
        'line1_color_female',
        'line1_position_x_female',
        'line1_position_y_female',
        // Line 2 Male
        'line2_text_male',
        'line2_font_family',
        'line2_font_size',
        'line2_color',
        'line2_position_x',
        'line2_position_y',
        // Line 2 Female
        'line2_text_female',
        'line2_font_family_female',
        'line2_font_size_female',
        'line2_color_female',
        'line2_position_x_female',
        'line2_position_y_female',
        // Line 3 Male
        'line3_text_male',
        'line3_font_family',
        'line3_font_size',
        'line3_color',
        'line3_position_x',
        'line3_position_y',
        // Line 3 Female
        'line3_text_female',
        'line3_font_family_female',
        'line3_font_size_female',
        'line3_color_female',
        'line3_position_x_female',
        'line3_position_y_female',
        // Line 4 Male
        'line4_text_male',
        'line4_font_family',
        'line4_font_size',
        'line4_color',
        'line4_position_x',
        'line4_position_y',
        // Line 4 Female
        'line4_text_female',
        'line4_font_family_female',
        'line4_font_size_female',
        'line4_color_female',
        'line4_position_x_female',
        'line4_position_y_female',
        // Line 5 Male
        'line5_text_male',
        'line5_font_family',
        'line5_font_size',
        'line5_color',
        'line5_position_x',
        'line5_position_y',
        // Line 5 Female
        'line5_text_female',
        'line5_font_family_female',
        'line5_font_size_female',
        'line5_color_female',
        'line5_position_x_female',
        'line5_position_y_female',
        // Line 6 Male
        'line6_text_male',
        'line6_font_family',
        'line6_font_size',
        'line6_color',
        'line6_position_x',
        'line6_position_y',
        // Line 6 Female
        'line6_text_female',
        'line6_font_family_female',
        'line6_font_size_female',
        'line6_color_female',
        'line6_position_x_female',
        'line6_position_y_female',
        // Signature Left
        'signature_left_text',
        'signature_left_font_family',
        'signature_left_font_size',
        'signature_left_color',
        'signature_left_position_x',
        'signature_left_position_y',
        // Signature Right
        'signature_right_text',
        'signature_right_font_family',
        'signature_right_font_size',
        'signature_right_color',
        'signature_right_position_x',
        'signature_right_position_y',
        // Other
        'show_date',
        'text_font_size',
        'small_text_font_size',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_date' => 'boolean',
        'canvas_width' => 'integer',
        'canvas_height' => 'integer',
        'name_position_x' => 'integer',
        'name_position_y' => 'integer',
        'name_position_x_female' => 'integer',
        'name_position_y_female' => 'integer',
        'name_font_size' => 'integer',
        'text_font_size' => 'integer',
        'small_text_font_size' => 'integer',
        // Line 1 positions
        'line1_font_size' => 'integer',
        'line1_position_x' => 'integer',
        'line1_position_y' => 'integer',
        'line1_font_size_female' => 'integer',
        'line1_position_x_female' => 'integer',
        'line1_position_y_female' => 'integer',
        // Line 2 positions
        'line2_font_size' => 'integer',
        'line2_position_x' => 'integer',
        'line2_position_y' => 'integer',
        'line2_font_size_female' => 'integer',
        'line2_position_x_female' => 'integer',
        'line2_position_y_female' => 'integer',
        // Line 3 positions
        'line3_font_size' => 'integer',
        'line3_position_x' => 'integer',
        'line3_position_y' => 'integer',
        'line3_font_size_female' => 'integer',
        'line3_position_x_female' => 'integer',
        'line3_position_y_female' => 'integer',
        // Line 4 positions
        'line4_font_size' => 'integer',
        'line4_position_x' => 'integer',
        'line4_position_y' => 'integer',
        'line4_font_size_female' => 'integer',
        'line4_position_x_female' => 'integer',
        'line4_position_y_female' => 'integer',
        // Line 5 positions
        'line5_font_size' => 'integer',
        'line5_position_x' => 'integer',
        'line5_position_y' => 'integer',
        'line5_font_size_female' => 'integer',
        'line5_position_x_female' => 'integer',
        'line5_position_y_female' => 'integer',
        // Line 6 positions
        'line6_font_size' => 'integer',
        'line6_position_x' => 'integer',
        'line6_position_y' => 'integer',
        'line6_font_size_female' => 'integer',
        'line6_position_x_female' => 'integer',
        'line6_position_y_female' => 'integer',
        // Signature positions
        'signature_left_font_size' => 'integer',
        'signature_left_position_x' => 'integer',
        'signature_left_position_y' => 'integer',
        'signature_right_font_size' => 'integer',
        'signature_right_position_x' => 'integer',
        'signature_right_position_y' => 'integer',
    ];

    /**
     * Get the active certificate settings
     */
    public static function getActive(): ?self
    {
        return Cache::remember('certificate_settings_active', 3600, function () {
            return self::where('is_active', true)->first();
        });
    }

    /**
     * Clear cache when settings are updated
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('certificate_settings_active');
        });

        static::deleted(function () {
            Cache::forget('certificate_settings_active');
        });
    }

    /**
     * Get available variable placeholders for texts
     */
    public static function getAvailableVariables(): array
    {
        return [
            '{student_name}' => 'اسم الطالب',
            '{school_name}' => 'المدرسة / المحافظة',
            '{exam_type}' => 'نوع النتيجة',
            '{total_score}' => 'المجموع',
            '{max_score}' => 'المجموع الكلي',
            '{percentage}' => 'النسبة المئوية',
            '{seat_number}' => 'رقم الجلوس',
            '{date}' => 'التاريخ',
        ];
    }

    /**
     * Get font options
     */
    public static function getFontOptions(): array
    {
        return [
            'Cairo' => 'Cairo - القاهرة',
            'Tajawal' => 'Tajawal - تجوال',
            'Amiri' => 'Amiri - أميري',
            'Almarai' => 'Almarai - المراعي',
            'Changa' => 'Changa - شانجا',
            'El Messiri' => 'El Messiri - المسيري',
            'Harmattan' => 'Harmattan - هارماتان',
            'Lateef' => 'Lateef - لطيف',
            'Mada' => 'Mada - مدى',
            'Markazi Text' => 'Markazi Text - مركزي',
            'Mirza' => 'Mirza - ميرزا',
            'Rakkas' => 'Rakkas - رقاص',
            'Reem Kufi' => 'Reem Kufi - ريم كوفي',
            'Scheherazade New' => 'Scheherazade - شهرزاد',
            'Noto Kufi Arabic' => 'Noto Kufi - نوتو كوفي',
            'Noto Naskh Arabic' => 'Noto Naskh - نوتو نسخ',
        ];
    }
}
