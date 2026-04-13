<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ExamType;
use App\Models\Governorate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LibyaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Libya Country
        $libya = Country::updateOrCreate(
            ['code' => 'LY'],
            [
                'name_ar' => 'ليبيا',
                'name_en' => 'Libya',
                'flag_icon' => '🇱🇾',
                'slug' => 'libya',
                'is_active' => true,
            ]
        );

        // 2. Create Exam Type (Preparatory)
        // Note: In Libya it's often called "Certificate of Basic Education" (شهادة التعليم الأساسي) 
        // but user asked for "الشهادة الإعدادية" specifically.
        $examType = ExamType::updateOrCreate(
            ['code' => 'ly_preparatory'],
            [
                'country_id' => $libya->id,
                'name_ar' => 'الشهادة الإعدادية',
                'name_en' => 'Preparatory Certificate',
                'slug' => 'prep',
                'level' => 'preparatory',
            ]
        );

        // 3. Create Governorates (Districts/Baladiyas)
        $regions = [
            ['name_ar' => 'طرابلس', 'name_en' => 'Tripoli'],
            ['name_ar' => 'بنغازي', 'name_en' => 'Benghazi'],
            ['name_ar' => 'مصراتة', 'name_en' => 'Misrata'],
            ['name_ar' => 'الزاوية', 'name_en' => 'Zawiya'],
            ['name_ar' => 'سبها', 'name_en' => 'Sabha'],
            ['name_ar' => 'المرج', 'name_en' => 'Marj'],
            ['name_ar' => 'درنة', 'name_en' => 'Derna'],
            ['name_ar' => 'طبرق', 'name_en' => 'Tobruk'],
            ['name_ar' => 'سرت', 'name_en' => 'Sirte'],
            ['name_ar' => 'الجبل الأخضر', 'name_en' => 'Jabal al Akhdar'],
            ['name_ar' => 'الكفرة', 'name_en' => 'Kufra'],
            ['name_ar' => 'غريان', 'name_en' => 'Gharyan'],
        ];

        foreach ($regions as $region) {
            // Fetch Logo if possible or use default logic later
            $logoPath = null; 
            // We can reuse the logic from GovernorateSeeder if we refactor, 
            // but for now let's just create them. 
            
            Governorate::firstOrCreate(
                [
                    'country_id' => $libya->id, 
                    'name_en' => $region['name_en']
                ],
                [
                    'name_ar' => $region['name_ar'],
                    'slug' => Str::slug($region['name_en']),
                    'logo_path' => $logoPath,
                ]
            );
        }
    }
}
