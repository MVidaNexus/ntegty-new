<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add homepage social links settings
        $settings = [
            ['key' => 'homepage_whatsapp_url', 'value' => 'https://chat.whatsapp.com/YOUR_GROUP_LINK'],
            ['key' => 'homepage_whatsapp_label', 'value' => 'جروب واتساب'],
            ['key' => 'homepage_whatsapp_active', 'value' => '1'],
            
            ['key' => 'homepage_telegram_url', 'value' => 'https://t.me/ntegty'],
            ['key' => 'homepage_telegram_label', 'value' => 'قناة تليجرام'],
            ['key' => 'homepage_telegram_active', 'value' => '1'],
            
            ['key' => 'homepage_facebook_url', 'value' => 'https://facebook.com/YOUR_PAGE'],
            ['key' => 'homepage_facebook_label', 'value' => 'صفحة فيسبوك'],
            ['key' => 'homepage_facebook_active', 'value' => '1'],
            
            ['key' => 'homepage_facebook_group_url', 'value' => 'https://facebook.com/groups/YOUR_GROUP'],
            ['key' => 'homepage_facebook_group_label', 'value' => 'جروب فيسبوك'],
            ['key' => 'homepage_facebook_group_active', 'value' => '1'],
        ];
        
        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'homepage_whatsapp_url', 'homepage_whatsapp_label', 'homepage_whatsapp_active',
            'homepage_telegram_url', 'homepage_telegram_label', 'homepage_telegram_active',
            'homepage_facebook_url', 'homepage_facebook_label', 'homepage_facebook_active',
            'homepage_facebook_group_url', 'homepage_facebook_group_label', 'homepage_facebook_group_active',
        ])->delete();
    }
};
