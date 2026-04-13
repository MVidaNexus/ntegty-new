<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ExamType;
use Illuminate\Database\Seeder;

class ExamTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $egypt = Country::where('code', 'EG')->first();
        $iraq = Country::where('code', 'IQ')->first();

        // Egyptian Exam Types
        $egyptianExams = [
            [
                'name_ar' => 'الشهادة الإعدادية',
                'name_en' => 'Preparatory Certificate',
                'code' => 'eg_preparatory',
                'slug' => 'prep',
                'level' => 'preparatory',
            ],
            [
                'name_ar' => 'الثانوية العامة',
                'name_en' => 'Secondary Certificate',
                'code' => 'eg_secondary',
                'slug' => 'secondary',
                'level' => 'secondary',
            ],
            [
                'name_ar' => 'دبلوم تجاري',
                'name_en' => 'Commercial Diploma',
                'code' => 'eg_diploma_commercial',
                'slug' => 'diploma-commercial',
                'level' => 'secondary',
            ],
            [
                'name_ar' => 'دبلوم صناعي',
                'name_en' => 'Industrial Diploma',
                'code' => 'eg_diploma_industrial',
                'slug' => 'diploma-industrial',
                'level' => 'secondary',
            ],
            [
                'name_ar' => 'دبلوم زراعي',
                'name_en' => 'Agricultural Diploma',
                'code' => 'eg_diploma_agricultural',
                'slug' => 'diploma-agricultural',
                'level' => 'secondary',
            ],
            [
                'name_ar' => 'دبلوم فندقي',
                'name_en' => 'Hotel Diploma',
                'code' => 'eg_diploma_hotel',
                'slug' => 'diploma-hotel',
                'level' => 'secondary',
            ],
        ];

        foreach ($egyptianExams as $exam) {
            ExamType::updateOrCreate(
                ['code' => $exam['code']],
                [
                    'country_id' => $egypt->id,
                    'name_ar' => $exam['name_ar'],
                    'name_en' => $exam['name_en'],
                    'slug' => $exam['slug'],
                    'level' => $exam['level'],
                ]
            );
        }

        // Iraqi Exam Types
        $iraqiExams = [
            [
                'name_ar' => 'السادس الإعدادي',
                'name_en' => 'Sixth Preparatory',
                'code' => 'iq_sixth_preparatory',
                'slug' => 'prep',
                'level' => 'preparatory',
            ],
        ];

        foreach ($iraqiExams as $exam) {
            ExamType::updateOrCreate(
                ['code' => $exam['code']],
                [
                    'country_id' => $iraq->id,
                    'name_ar' => $exam['name_ar'],
                    'name_en' => $exam['name_en'],
                    'slug' => $exam['slug'],
                    'level' => $exam['level'],
                ]
            );
        }
    }
}
