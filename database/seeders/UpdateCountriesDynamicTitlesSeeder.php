<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class UpdateCountriesDynamicTitlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'code' => 'EG',
                'government_type' => 'جمهورية',
                'academic_year' => '2025 - 2026',
                'semester' => 'الفصل الدراسي الأول',
            ],
            [
                'code' => 'IQ',
                'government_type' => 'جمهورية',
                'academic_year' => '2025 - 2026',
                'semester' => null,
            ],
            [
                'code' => 'LY',
                'government_type' => 'دولة',
                'academic_year' => '2025 - 2026',
                'semester' => null,
            ],
            [
                'code' => 'KW',
                'government_type' => 'دولة',
                'academic_year' => '2025 - 2026',
                'semester' => null,
            ],
        ];

        foreach ($countries as $countryData) {
            Country::where('code', $countryData['code'])->update([
                'government_type' => $countryData['government_type'],
                'academic_year' => $countryData['academic_year'],
                'semester' => $countryData['semester'],
            ]);
        }

        $this->command->info('✅ Countries dynamic titles updated successfully!');
    }
}
