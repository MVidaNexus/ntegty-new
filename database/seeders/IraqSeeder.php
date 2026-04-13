<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ExamType;
use App\Models\Governorate;
use Illuminate\Database\Seeder;

class IraqSeeder extends Seeder
{
    public function run(): void
    {
        // Create Iraq country
        $iraq = Country::updateOrCreate(
            ['code' => 'IQ'],
            [
                'name_ar' => 'العراق',
                'name_en' => 'Iraq',
                'flag_path' => null,
                'is_active' => true,
            ]
        );

        // Create Exam Types for Iraq
        $examTypes = [
            [
                'name_ar' => 'السادس الإعدادي',
                'name_en' => 'Sixth Preparatory',
                'code' => 'iq_prep_6th',
                'slug' => 'prep/6th',
                'level' => 'preparatory',
            ],
            [
                'name_ar' => 'الثالث المتوسط',
                'name_en' => 'Third Intermediate',
                'code' => 'iq_prep_9th',
                'slug' => 'prep/9th',
                'level' => 'preparatory',
            ],
            [
                'name_ar' => 'البكالوريا',
                'name_en' => 'Baccalaureate',
                'code' => 'iq_secondary',
                'slug' => 'secondary',
                'level' => 'secondary',
            ],
        ];

        foreach ($examTypes as $examTypeData) {
            ExamType::updateOrCreate(
                [
                    'country_id' => $iraq->id,
                    'code' => $examTypeData['code']
                ],
                $examTypeData
            );
        }

        // Create Governorates (Provinces) for Iraq
        $governorates = [
            ['name_ar' => 'بغداد', 'name_en' => 'Baghdad'],
            ['name_ar' => 'البصرة', 'name_en' => 'Basra'],
            ['name_ar' => 'نينوى', 'name_en' => 'Nineveh'],
            ['name_ar' => 'الأنبار', 'name_en' => 'Anbar'],
            ['name_ar' => 'أربيل', 'name_en' => 'Erbil'],
            ['name_ar' => 'السليمانية', 'name_en' => 'Sulaymaniyah'],
            ['name_ar' => 'دهوك', 'name_en' => 'Dohuk'],
            ['name_ar' => 'كركوك', 'name_en' => 'Kirkuk'],
            ['name_ar' => 'ديالى', 'name_en' => 'Diyala'],
            ['name_ar' => 'صلاح الدين', 'name_en' => 'Salah ad-Din'],
            ['name_ar' => 'واسط', 'name_en' => 'Wasit'],
            ['name_ar' => 'ميسان', 'name_en' => 'Maysan'],
            ['name_ar' => 'ذي قار', 'name_en' => 'Dhi Qar'],
            ['name_ar' => 'القادسية', 'name_en' => 'Al-Qadisiyyah'],
            ['name_ar' => 'بابل', 'name_en' => 'Babylon'],
            ['name_ar' => 'كربلاء', 'name_en' => 'Karbala'],
            ['name_ar' => 'النجف', 'name_en' => 'Najaf'],
            ['name_ar' => 'المثنى', 'name_en' => 'Al-Muthanna'],
        ];

        foreach ($governorates as $governorateData) {
            Governorate::updateOrCreate(
                [
                    'country_id' => $iraq->id,
                    'name_en' => $governorateData['name_en']
                ],
                [
                    'name_ar' => $governorateData['name_ar'],
                    'logo_path' => null,
                ]
            );
        }

        $this->command->info('Iraq data seeded successfully!');
    }
}
