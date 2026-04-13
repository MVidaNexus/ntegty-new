<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'name_ar' => 'مصر',
                'name_en' => 'Egypt',
                'code' => 'EG',
                'slug' => 'egypt',
                'flag_icon' => '🇪🇬',
            ],
            [
                'name_ar' => 'العراق',
                'name_en' => 'Iraq',
                'code' => 'IQ',
                'slug' => 'iraq',
                'flag_icon' => '🇮🇶',
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                $country
            );
        }
    }
}
