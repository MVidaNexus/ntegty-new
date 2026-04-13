<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ExamType;
use App\Models\Governorate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewCountriesSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'code' => 'PS',
                'name_ar' => 'فلسطين',
                'name_en' => 'Palestine',
                'flag_icon' => '🇵🇸',
                'slug' => 'palestine',
                'exam_types' => [
                    [
                        'code' => 'ps_tawjihi',
                        'name_ar' => 'نتائج التوجيهي',
                        'name_en' => 'Tawjihi Results',
                        'slug' => 'secondary',
                        'level' => 'secondary'
                    ]
                ],
                'regions' => [
                    // West Bank
                    ['name_ar' => 'القدس', 'name_en' => 'Jerusalem'],
                    ['name_ar' => 'رام الله والبيرة', 'name_en' => 'Ramallah and Al-Bireh'],
                    ['name_ar' => 'نابلس', 'name_en' => 'Nablus'],
                    ['name_ar' => 'الخليل', 'name_en' => 'Hebron'],
                    ['name_ar' => 'جنين', 'name_en' => 'Jenin'],
                    ['name_ar' => 'طولكرم', 'name_en' => 'Tulkarm'],
                    ['name_ar' => 'بيت لحم', 'name_en' => 'Bethlehem'],
                    ['name_ar' => 'قلقيلية', 'name_en' => 'Qalqilya'],
                    ['name_ar' => 'سلفيت', 'name_en' => 'Salfit'],
                    ['name_ar' => 'أريحا', 'name_en' => 'Jericho'],
                    ['name_ar' => 'طوباس', 'name_en' => 'Tubas'],
                    // Gaza Strip
                    ['name_ar' => 'شمال غزة', 'name_en' => 'North Gaza'],
                    ['name_ar' => 'غزة', 'name_en' => 'Gaza'],
                    ['name_ar' => 'دير البلح', 'name_en' => 'Deir al-Balah'],
                    ['name_ar' => 'خان يونس', 'name_en' => 'Khan Yunis'],
                    ['name_ar' => 'رفح', 'name_en' => 'Rafah'],
                ]
            ],
            [
                'code' => 'JO',
                'name_ar' => 'الأردن',
                'name_en' => 'Jordan',
                'flag_icon' => '🇯🇴',
                'slug' => 'jordan',
                'exam_types' => [
                    [
                        'code' => 'jo_tawjihi',
                        'name_ar' => 'نتائج التوجيهي',
                        'name_en' => 'Tawjihi Results',
                        'slug' => 'secondary',
                        'level' => 'secondary'
                    ]
                ],
                'regions' => [
                    ['name_ar' => 'عمان', 'name_en' => 'Amman'],
                    ['name_ar' => 'إربد', 'name_en' => 'Irbid'],
                    ['name_ar' => 'الزرقاء', 'name_en' => 'Zarqa'],
                    ['name_ar' => 'المفرق', 'name_en' => 'Mafraq'],
                    ['name_ar' => 'البلقاء', 'name_en' => 'Balqa'],
                    ['name_ar' => 'الكرك', 'name_en' => 'Karak'],
                    ['name_ar' => 'جرش', 'name_en' => 'Jerash'],
                    ['name_ar' => 'مادبا', 'name_en' => 'Madaba'],
                    ['name_ar' => 'عجلون', 'name_en' => 'Ajloun'],
                    ['name_ar' => 'العقبة', 'name_en' => 'Aqaba'],
                    ['name_ar' => 'معان', 'name_en' => 'Maan'],
                    ['name_ar' => 'الطفيلة', 'name_en' => 'Tafilah'],
                ]
            ],
            [
                'code' => 'SY',
                'name_ar' => 'سوريا',
                'name_en' => 'Syria',
                'flag_icon' => '🇸🇾',
                'slug' => 'syria',
                'exam_types' => [
                    [
                        'code' => 'sy_baccalaureate',
                        'name_ar' => 'نتائج البكالوريا',
                        'name_en' => 'Baccalaureate Results',
                        'slug' => 'baccalaureate',
                        'level' => 'secondary'
                    ]
                ],
                'regions' => [
                    ['name_ar' => 'دمشق', 'name_en' => 'Damascus'],
                    ['name_ar' => 'ريف دمشق', 'name_en' => 'Rif Dimashq'],
                    ['name_ar' => 'حلب', 'name_en' => 'Aleppo'],
                    ['name_ar' => 'حمص', 'name_en' => 'Homs'],
                    ['name_ar' => 'حماة', 'name_en' => 'Hama'],
                    ['name_ar' => 'اللاذقية', 'name_en' => 'Latakia'],
                    ['name_ar' => 'طرطوس', 'name_en' => 'Tartus'],
                    ['name_ar' => 'إدلب', 'name_en' => 'Idlib'],
                    ['name_ar' => 'درعا', 'name_en' => 'Daraa'],
                    ['name_ar' => 'السويداء', 'name_en' => 'As-Suwayda'],
                    ['name_ar' => 'القنيطرة', 'name_en' => 'Quneitra'],
                    ['name_ar' => 'دير الزور', 'name_en' => 'Deir ez-Zor'],
                    ['name_ar' => 'الحسكة', 'name_en' => 'Al-Hasakah'],
                    ['name_ar' => 'الرقة', 'name_en' => 'Raqqa'],
                ]
            ],
            [
                'code' => 'TN',
                'name_ar' => 'تونس',
                'name_en' => 'Tunisia',
                'flag_icon' => '🇹🇳',
                'slug' => 'tunisia',
                'exam_types' => [
                    [
                        'code' => 'tn_baccalaureate',
                        'name_ar' => 'نتائج البكالوريا',
                        'name_en' => 'Baccalaureate Results',
                        'slug' => 'baccalaureate',
                        'level' => 'secondary'
                    ]
                ],
                'regions' => [
                    ['name_ar' => 'تونس', 'name_en' => 'Tunis'],
                    ['name_ar' => 'أريانة', 'name_en' => 'Ariana'],
                    ['name_ar' => 'بن عروس', 'name_en' => 'Ben Arous'],
                    ['name_ar' => 'منوبة', 'name_en' => 'Manouba'],
                    ['name_ar' => 'نابل', 'name_en' => 'Nabeul'],
                    ['name_ar' => 'زغوان', 'name_en' => 'Zaghouan'],
                    ['name_ar' => 'بنزرت', 'name_en' => 'Bizerte'],
                    ['name_ar' => 'باجة', 'name_en' => 'Beja'],
                    ['name_ar' => 'جندوبة', 'name_en' => 'Jendouba'],
                    ['name_ar' => 'الكاف', 'name_en' => 'Kef'],
                    ['name_ar' => 'سليانة', 'name_en' => 'Siliana'],
                    ['name_ar' => 'سوسة', 'name_en' => 'Sousse'],
                    ['name_ar' => 'المنستير', 'name_en' => 'Monastir'],
                    ['name_ar' => 'المهدية', 'name_en' => 'Mahdia'],
                    ['name_ar' => 'صفاقس', 'name_en' => 'Sfax'],
                    ['name_ar' => 'القيروان', 'name_en' => 'Kairouan'],
                    ['name_ar' => 'القصرين', 'name_en' => 'Kasserine'],
                    ['name_ar' => 'سيدي بوزيد', 'name_en' => 'Sidi Bouzid'],
                    ['name_ar' => 'قابس', 'name_en' => 'Gabes'],
                    ['name_ar' => 'مدنين', 'name_en' => 'Medenine'],
                    ['name_ar' => 'تطاوين', 'name_en' => 'Tataouine'],
                    ['name_ar' => 'قفصة', 'name_en' => 'Gafsa'],
                    ['name_ar' => 'توزر', 'name_en' => 'Tozeur'],
                    ['name_ar' => 'قبلي', 'name_en' => 'Kebili'],
                ]
            ],
            [
                'code' => 'DZ',
                'name_ar' => 'الجزائر',
                'name_en' => 'Algeria',
                'flag_icon' => '🇩🇿',
                'slug' => 'algeria',
                'exam_types' => [
                    [
                        'code' => 'dz_baccalaureate',
                        'name_ar' => 'نتائج البكالوريا',
                        'name_en' => 'Baccalaureate Results',
                        'slug' => 'baccalaureate',
                        'level' => 'secondary'
                    ]
                ],
                'regions' => [
                    ['name_ar' => 'أدرار', 'name_en' => 'Adrar'],
                    ['name_ar' => 'الشلف', 'name_en' => 'Chlef'],
                    ['name_ar' => 'الأغواط', 'name_en' => 'Laghouat'],
                    ['name_ar' => 'أم البواقي', 'name_en' => 'Oum El Bouaghi'],
                    ['name_ar' => 'باتنة', 'name_en' => 'Batna'],
                    ['name_ar' => 'بجاية', 'name_en' => 'Bejaia'],
                    ['name_ar' => 'بسكرة', 'name_en' => 'Biskra'],
                    ['name_ar' => 'بشار', 'name_en' => 'Bechar'],
                    ['name_ar' => 'البليدة', 'name_en' => 'Blida'],
                    ['name_ar' => 'البويرة', 'name_en' => 'Bouira'],
                    ['name_ar' => 'تمنراست', 'name_en' => 'Tamanrasset'],
                    ['name_ar' => 'تبسة', 'name_en' => 'Tebessa'],
                    ['name_ar' => 'تلمسان', 'name_en' => 'Tlemcen'],
                    ['name_ar' => 'تيارت', 'name_en' => 'Tiaret'],
                    ['name_ar' => 'تيزي وزو', 'name_en' => 'Tizi Ouzou'],
                    ['name_ar' => 'الجزائر', 'name_en' => 'Algiers'],
                    ['name_ar' => 'الجلفة', 'name_en' => 'Djelfa'],
                    ['name_ar' => 'جيجل', 'name_en' => 'Jijel'],
                    ['name_ar' => 'سطيف', 'name_en' => 'Setif'],
                    ['name_ar' => 'سعيدة', 'name_en' => 'Saida'],
                    ['name_ar' => 'سكيكدة', 'name_en' => 'Skikda'],
                    ['name_ar' => 'سيدي بلعباس', 'name_en' => 'Sidi Bel Abbes'],
                    ['name_ar' => 'عنابة', 'name_en' => 'Annaba'],
                    ['name_ar' => 'قالمة', 'name_en' => 'Guelma'],
                    ['name_ar' => 'قسنطينة', 'name_en' => 'Constantine'],
                    ['name_ar' => 'المدية', 'name_en' => 'Medea'],
                    ['name_ar' => 'مستغانم', 'name_en' => 'Mostaganem'],
                    ['name_ar' => 'المسيلة', 'name_en' => 'Msila'],
                    ['name_ar' => 'معسكر', 'name_en' => 'Mascara'],
                    ['name_ar' => 'ورقلة', 'name_en' => 'Ouargla'],
                    ['name_ar' => 'وهران', 'name_en' => 'Oran'],
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
                        'name_ar' => $region['name_ar'],
                        'slug' => Str::slug($region['name_en']),
                        'logo_path' => null, // Placeholder logic can be added later
                    ]
                );
            }
        }
    }
}
