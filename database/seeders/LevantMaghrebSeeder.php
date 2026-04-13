<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ExamType;
use App\Models\Governorate;
use Illuminate\Database\Seeder;

class LevantMaghrebSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'code' => 'LB',
                'name_ar' => 'لبنان',
                'name_en' => 'Lebanon',
                'flag_icon' => '🇱🇧',
                'slug' => 'lebanon',
                'exam_types' => [
                    [
                        'code' => 'lb_baccalaureate',
                        'name_ar' => 'نتائج البكالوريا',
                        'name_en' => 'Baccalaureate Results',
                        'slug' => 'baccalaureate',
                        'level' => 'secondary'
                    ]
                ],
                'regions' => [
                    ['name_ar' => 'بيروت', 'name_en' => 'Beirut'],
                    ['name_ar' => 'جبل لبنان', 'name_en' => 'Mount Lebanon'],
                    ['name_ar' => 'الشمال', 'name_en' => 'North'],
                    ['name_ar' => 'الجنوب', 'name_en' => 'South'],
                    ['name_ar' => 'البقاع', 'name_en' => 'Beqaa'],
                    ['name_ar' => 'النبطية', 'name_en' => 'Nabatieh'],
                    ['name_ar' => 'عكار', 'name_en' => 'Akkar'],
                    ['name_ar' => 'بعلبك الهرمل', 'name_en' => 'Baalbek-Hermel'],
                ]
            ],
            [
                'code' => 'MA',
                'name_ar' => 'المغرب',
                'name_en' => 'Morocco',
                'flag_icon' => '🇲🇦',
                'slug' => 'morocco',
                'exam_types' => [
                    [
                        'code' => 'ma_baccalaureate',
                        'name_ar' => 'نتائج البكالوريا',
                        'name_en' => 'Baccalaureate Results',
                        'slug' => 'baccalaureate',
                        'level' => 'secondary'
                    ]
                ],
                'regions' => [
                    ['name_ar' => 'طنجة تطوان الحسيمة', 'name_en' => 'Tanger-Tetouan-Al Hoceima'],
                    ['name_ar' => 'الشرق', 'name_en' => 'Oriental'],
                    ['name_ar' => 'فاس مكناس', 'name_en' => 'Fès-Meknès'],
                    ['name_ar' => 'الرباط سلا القنيطرة', 'name_en' => 'Rabat-Salé-Kénitra'],
                    ['name_ar' => 'بني ملال خنيفرة', 'name_en' => 'Béni Mellal-Khénifra'],
                    ['name_ar' => 'الدار البيضاء سطات', 'name_en' => 'Casablanca-Settat'],
                    ['name_ar' => 'مراكش آسفي', 'name_en' => 'Marrakech-Safi'],
                    ['name_ar' => 'درعة تافيلالت', 'name_en' => 'Drâa-Tafilalet'],
                    ['name_ar' => 'سوس ماسة', 'name_en' => 'Souss-Massa'],
                    ['name_ar' => 'كلميم واد نون', 'name_en' => 'Guelmim-Oued Noun'],
                    ['name_ar' => 'العيون الساقية الحمراء', 'name_en' => 'Laâyoune-Sakia El Hamra'],
                    ['name_ar' => 'الداخلة وادي الذهب', 'name_en' => 'Dakhla-Oued Ed-Dahab'],
                ]
            ],
        ];

        foreach ($countries as $countryData) {
            $country = Country::updateOrCreate(
                ['code' => $countryData['code']],
                [
                    'name_ar' => $countryData['name_ar'],
                    'name_en' => $countryData['name_en'],
                    'flag_icon' => $countryData['flag_icon'],
                    'slug' => $countryData['slug'],
                    'is_active' => true,
                ]
            );

            foreach ($countryData['exam_types'] as $type) {
                ExamType::updateOrCreate(
                    ['code' => $type['code']],
                    [
                        'country_id' => $country->id,
                        'name_ar' => $type['name_ar'],
                        'name_en' => $type['name_en'],
                        'slug' => $type['slug'],
                        'level' => $type['level'],
                    ]
                );
            }

            foreach ($countryData['regions'] as $region) {
                Governorate::firstOrCreate(
                    [
                        'country_id' => $country->id,
                        'name_en' => $region['name_en']
                    ],
                    [
                        'name_ar' => $region['name_ar']
                    ]
                );
            }
        }
    }
}
