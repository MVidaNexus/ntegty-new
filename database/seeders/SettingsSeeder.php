<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'نتيجتي',
                'type' => 'text',
                'group' => 'general',
            ],
            [
                'key' => 'site_description',
                'value' => 'منصة عرض النتائج الدراسية',
                'type' => 'text',
                'group' => 'general',
            ],
            [
                'key' => 'logo',
                'value' => null,
                'type' => 'image',
                'group' => 'branding',
            ],
            [
                'key' => 'header_icon',
                'value' => null,
                'type' => 'image',
                'group' => 'branding',
            ],
            [
                'key' => 'favicon',
                'value' => null,
                'type' => 'image',
                'group' => 'branding',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
