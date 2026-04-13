<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $years = [
            ['year' => '2023-2024', 'is_active' => false],
            ['year' => '2024-2025', 'is_active' => true],
            ['year' => '2025-2026', 'is_active' => false],
        ];

        foreach ($years as $year) {
            AcademicYear::create($year);
        }
    }
}
