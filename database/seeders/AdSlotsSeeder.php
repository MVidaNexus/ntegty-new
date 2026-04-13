<?php

namespace Database\Seeders;

use App\Models\AdSlot;
use Illuminate\Database\Seeder;

class AdSlotsSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [
            // إعلانات الصفحة الرئيسية
            [
                'name' => 'إعلان أسفل الهيدر - الرئيسية',
                'slug' => 'home-header-bottom',
                'page_type' => 'home',
                'position' => 'header_bottom',
                'ad_format' => 'auto',
                'is_active' => false,
                'sort_order' => 1,
                'description' => 'يظهر أسفل شريط التنقل في الصفحة الرئيسية',
            ],
            [
                'name' => 'إعلان قبل مربع البحث - الرئيسية',
                'slug' => 'home-before-search',
                'page_type' => 'home',
                'position' => 'before_search',
                'ad_format' => 'auto',
                'is_active' => false,
                'sort_order' => 2,
                'description' => 'يظهر قبل مربع البحث في الصفحة الرئيسية',
            ],
            [
                'name' => 'إعلان بعد مربع البحث - الرئيسية',
                'slug' => 'home-after-search',
                'page_type' => 'home',
                'position' => 'after_search',
                'ad_format' => 'auto',
                'is_active' => false,
                'sort_order' => 3,
                'description' => 'يظهر بعد مربع البحث في الصفحة الرئيسية',
            ],
            [
                'name' => 'إعلان أعلى الفوتر - الرئيسية',
                'slug' => 'home-footer-top',
                'page_type' => 'home',
                'position' => 'footer_top',
                'ad_format' => 'multiplex',
                'is_active' => false,
                'sort_order' => 4,
                'description' => 'يظهر قبل الفوتر في الصفحة الرئيسية',
            ],

            // إعلانات صفحات الدول
            [
                'name' => 'إعلان أسفل العنوان - صفحات الدول',
                'slug' => 'country-after-title',
                'page_type' => 'country',
                'position' => 'after_title',
                'ad_format' => 'auto',
                'is_active' => false,
                'sort_order' => 1,
                'description' => 'يظهر أسفل عنوان الصفحة في صفحات الدول',
            ],
            [
                'name' => 'إعلان قبل مربع البحث - صفحات الدول',
                'slug' => 'country-before-search',
                'page_type' => 'country',
                'position' => 'before_search',
                'ad_format' => 'auto',
                'is_active' => false,
                'sort_order' => 2,
                'description' => 'يظهر قبل مربع البحث في صفحات الدول',
            ],
            [
                'name' => 'إعلان داخل مربع البحث - صفحات الدول',
                'slug' => 'country-inside-search',
                'page_type' => 'country',
                'position' => 'inside_search',
                'ad_format' => 'in-article',
                'is_active' => false,
                'sort_order' => 3,
                'description' => 'يظهر داخل قسم البحث في صفحات الدول',
            ],
            [
                'name' => 'إعلان بعد مربع البحث - صفحات الدول',
                'slug' => 'country-after-search',
                'page_type' => 'country',
                'position' => 'after_search',
                'ad_format' => 'auto',
                'is_active' => false,
                'sort_order' => 4,
                'description' => 'يظهر بعد مربع البحث في صفحات الدول',
            ],
            [
                'name' => 'إعلان بين النتائج - صفحات الدول',
                'slug' => 'country-between-results',
                'page_type' => 'country',
                'position' => 'between_results',
                'ad_format' => 'in-feed',
                'is_active' => false,
                'sort_order' => 5,
                'description' => 'يظهر بين نتائج البحث في صفحات الدول',
            ],

            // إعلانات صفحات المحافظات
            [
                'name' => 'إعلان أسفل العنوان - المحافظات',
                'slug' => 'gov-after-title',
                'page_type' => 'governorate',
                'position' => 'after_title',
                'ad_format' => 'auto',
                'is_active' => false,
                'sort_order' => 1,
                'description' => 'يظهر أسفل عنوان الصفحة في صفحات المحافظات',
            ],
            [
                'name' => 'إعلان قبل مربع البحث - المحافظات',
                'slug' => 'gov-before-search',
                'page_type' => 'governorate',
                'position' => 'before_search',
                'ad_format' => 'auto',
                'is_active' => false,
                'sort_order' => 2,
                'description' => 'يظهر قبل مربع البحث في صفحات المحافظات',
            ],
            [
                'name' => 'إعلان داخل مربع البحث - المحافظات',
                'slug' => 'gov-inside-search',
                'page_type' => 'governorate',
                'position' => 'inside_search',
                'ad_format' => 'in-article',
                'is_active' => false,
                'sort_order' => 3,
                'description' => 'يظهر داخل قسم البحث في صفحات المحافظات',
            ],
            [
                'name' => 'إعلان بعد مربع البحث - المحافظات',
                'slug' => 'gov-after-search',
                'page_type' => 'governorate',
                'position' => 'after_search',
                'ad_format' => 'auto',
                'is_active' => false,
                'sort_order' => 4,
                'description' => 'يظهر بعد مربع البحث في صفحات المحافظات',
            ],

            // إعلانات صفحات النتائج
            [
                'name' => 'إعلان قبل النتيجة',
                'slug' => 'result-before-content',
                'page_type' => 'result',
                'position' => 'before_content',
                'ad_format' => 'auto',
                'is_active' => false,
                'sort_order' => 1,
                'description' => 'يظهر قبل عرض النتيجة',
            ],
            [
                'name' => 'إعلان بعد النتيجة',
                'slug' => 'result-after-content',
                'page_type' => 'result',
                'position' => 'after_content',
                'ad_format' => 'auto',
                'is_active' => false,
                'sort_order' => 2,
                'description' => 'يظهر بعد عرض النتيجة',
            ],
            [
                'name' => 'إعلان داخل بطاقة النتيجة',
                'slug' => 'result-in-article',
                'page_type' => 'result',
                'position' => 'inside_search',
                'ad_format' => 'in-article',
                'is_active' => false,
                'sort_order' => 3,
                'description' => 'يظهر داخل بطاقة النتيجة',
            ],

            // إعلانات عامة
            [
                'name' => 'إعلان معلق أسفل الصفحة',
                'slug' => 'sticky-bottom',
                'page_type' => 'global',
                'position' => 'sticky_bottom',
                'ad_format' => 'auto',
                'is_active' => false,
                'show_on_mobile' => true,
                'show_on_desktop' => true,
                'sort_order' => 1,
                'description' => 'إعلان ثابت يظهر أسفل الشاشة - يمكن للزائر إغلاقه',
            ],
            [
                'name' => 'إعلان أسفل الهيدر - كل الصفحات',
                'slug' => 'global-header-bottom',
                'page_type' => 'global',
                'position' => 'header_bottom',
                'ad_format' => 'auto',
                'is_active' => false,
                'sort_order' => 2,
                'description' => 'يظهر أسفل الهيدر في جميع الصفحات',
            ],
            [
                'name' => 'إعلان أعلى الفوتر - كل الصفحات',
                'slug' => 'global-footer-top',
                'page_type' => 'global',
                'position' => 'footer_top',
                'ad_format' => 'multiplex',
                'is_active' => false,
                'sort_order' => 3,
                'description' => 'يظهر قبل الفوتر في جميع الصفحات',
            ],
        ];

        foreach ($slots as $slot) {
            AdSlot::updateOrCreate(
                ['slug' => $slot['slug']],
                $slot
            );
        }

        $this->command->info('تم إنشاء ' . count($slots) . ' مكان إعلان');
    }
}
