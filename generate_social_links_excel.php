<?php
/**
 * Script to generate Excel file with all social media links
 * Shows where each link appears and where to manage it
 */

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$baseUrl = 'https://ntegty.com';
$dashboardUrl = $baseUrl . '/dashboard';

// Collect all social links requirements
$socialLinks = [];

// ========================================
// 1. الفوتر - أزرار السوشيال العامة
// ========================================
$socialLinks[] = [
    'location' => 'الفوتر (جميع الصفحات)',
    'platform' => 'فيسبوك',
    'purpose' => 'زر الفيسبوك في الفوتر',
    'control_page' => $dashboardUrl . '/homepage-settings',
    'control_name' => 'إعدادات الصفحة الرئيسية → الفوتر',
    'setting_key' => 'footer_facebook_url',
    'current_value' => \App\Models\SiteSetting::get('footer_facebook_url', ''),
];

$socialLinks[] = [
    'location' => 'الفوتر (جميع الصفحات)',
    'platform' => 'تيليجرام',
    'purpose' => 'زر التيليجرام في الفوتر',
    'control_page' => $dashboardUrl . '/homepage-settings',
    'control_name' => 'إعدادات الصفحة الرئيسية → الفوتر',
    'setting_key' => 'footer_telegram_url',
    'current_value' => \App\Models\SiteSetting::get('footer_telegram_url', ''),
];

$socialLinks[] = [
    'location' => 'الفوتر (جميع الصفحات)',
    'platform' => 'واتساب',
    'purpose' => 'زر الواتساب في الفوتر',
    'control_page' => $dashboardUrl . '/homepage-settings',
    'control_name' => 'إعدادات الصفحة الرئيسية → الفوتر',
    'setting_key' => 'footer_whatsapp_url',
    'current_value' => \App\Models\SiteSetting::get('footer_whatsapp_url', ''),
];

// ========================================
// 2. الصفحة الرئيسية - أزرار السوشيال
// ========================================
$socialLinks[] = [
    'location' => 'الصفحة الرئيسية',
    'platform' => 'واتساب',
    'purpose' => 'زر جروب الواتساب في الصفحة الرئيسية',
    'control_page' => $dashboardUrl . '/homepage-social-settings',
    'control_name' => 'سوشيال الصفحة الرئيسية',
    'setting_key' => 'homepage_whatsapp_url',
    'current_value' => \App\Models\Setting::where('key', 'homepage_whatsapp_url')->value('value') ?? '',
];

$socialLinks[] = [
    'location' => 'الصفحة الرئيسية',
    'platform' => 'تيليجرام',
    'purpose' => 'زر قناة التيليجرام في الصفحة الرئيسية',
    'control_page' => $dashboardUrl . '/homepage-social-settings',
    'control_name' => 'سوشيال الصفحة الرئيسية',
    'setting_key' => 'homepage_telegram_url',
    'current_value' => \App\Models\Setting::where('key', 'homepage_telegram_url')->value('value') ?? '',
];

$socialLinks[] = [
    'location' => 'الصفحة الرئيسية',
    'platform' => 'فيسبوك (صفحة)',
    'purpose' => 'زر صفحة الفيسبوك في الصفحة الرئيسية',
    'control_page' => $dashboardUrl . '/homepage-social-settings',
    'control_name' => 'سوشيال الصفحة الرئيسية',
    'setting_key' => 'homepage_facebook_url',
    'current_value' => \App\Models\Setting::where('key', 'homepage_facebook_url')->value('value') ?? '',
];

$socialLinks[] = [
    'location' => 'الصفحة الرئيسية',
    'platform' => 'فيسبوك (جروب)',
    'purpose' => 'زر جروب الفيسبوك في الصفحة الرئيسية',
    'control_page' => $dashboardUrl . '/homepage-social-settings',
    'control_name' => 'سوشيال الصفحة الرئيسية',
    'setting_key' => 'homepage_facebook_group_url',
    'current_value' => \App\Models\Setting::where('key', 'homepage_facebook_group_url')->value('value') ?? '',
];

// ========================================
// 3. السوشيال الافتراضي (يظهر في صفحات النتائج)
// ========================================
$defaultLinks = \App\Models\SocialLink::where('scope_type', 'default')->orderBy('sort_order')->get();
if ($defaultLinks->isEmpty()) {
    $socialLinks[] = [
        'location' => 'صفحات النتائج (افتراضي)',
        'platform' => 'جميع المنصات',
        'purpose' => 'أزرار السوشيال الافتراضية في صفحات النتائج',
        'control_page' => $dashboardUrl . '/default-social-links',
        'control_name' => 'السوشيال الافتراضي',
        'setting_key' => 'SocialLink (scope_type=default)',
        'current_value' => '⚠️ لا يوجد روابط مضافة',
    ];
} else {
    foreach ($defaultLinks as $link) {
        $socialLinks[] = [
            'location' => 'صفحات النتائج (افتراضي)',
            'platform' => \App\Models\SocialLink::$platforms[$link->platform]['name'] ?? $link->platform,
            'purpose' => 'زر ' . ($link->label ?? \App\Models\SocialLink::$platforms[$link->platform]['name'] ?? $link->platform) . ' في صفحات النتائج',
            'control_page' => $dashboardUrl . '/default-social-links',
            'control_name' => 'السوشيال الافتراضي',
            'setting_key' => 'SocialLink ID: ' . $link->id,
            'current_value' => $link->url,
        ];
    }
}

// ========================================
// 4. روابط السوشيال لكل دولة
// ========================================
$countries = \App\Models\Country::where('is_active', true)->get();
foreach ($countries as $country) {
    $socialLinks[] = [
        'location' => 'صفحات دولة ' . $country->name_ar,
        'platform' => 'تيليجرام',
        'purpose' => 'زر تيليجرام خاص بدولة ' . $country->name_ar,
        'control_page' => $dashboardUrl . '/countries/' . $country->id . '/edit',
        'control_name' => 'الدول → ' . $country->name_ar . ' → تعديل',
        'setting_key' => 'countries.telegram_url',
        'current_value' => $country->telegram_url ?? '⚠️ غير مضاف',
    ];
}

// ========================================
// 5. السوشيال الخاص بكل نوع امتحان
// ========================================
$examTypes = \App\Models\ExamType::all();
foreach ($examTypes as $exam) {
    $examLinks = \App\Models\SocialLink::where('scope_type', 'exam_type')
        ->where('scope_id', $exam->id)
        ->orderBy('sort_order')
        ->get();
    
    if ($examLinks->isEmpty()) {
        $socialLinks[] = [
            'location' => 'صفحات ' . $exam->name_ar,
            'platform' => 'جميع المنصات',
            'purpose' => 'أزرار السوشيال الخاصة بـ ' . $exam->name_ar,
            'control_page' => $dashboardUrl . '/social-links',
            'control_name' => 'روابط السوشيال → إضافة جديد (scope_type=exam_type, scope_id=' . $exam->id . ')',
            'setting_key' => 'SocialLink (exam_type)',
            'current_value' => '⚠️ يستخدم الافتراضي - يمكن إضافة روابط مخصصة',
        ];
    } else {
        foreach ($examLinks as $link) {
            $socialLinks[] = [
                'location' => 'صفحات ' . $exam->name_ar,
                'platform' => \App\Models\SocialLink::$platforms[$link->platform]['name'] ?? $link->platform,
                'purpose' => 'زر ' . ($link->label ?? \App\Models\SocialLink::$platforms[$link->platform]['name'] ?? $link->platform) . ' في ' . $exam->name_ar,
                'control_page' => $dashboardUrl . '/social-links/' . $link->id . '/edit',
                'control_name' => 'روابط السوشيال → ' . $link->label,
                'setting_key' => 'SocialLink ID: ' . $link->id,
                'current_value' => $link->url,
            ];
        }
    }
}

// ========================================
// 6. صفحة اتصل بنا
// ========================================
$socialLinks[] = [
    'location' => 'صفحة اتصل بنا',
    'platform' => 'واتساب',
    'purpose' => 'زر الواتساب في صفحة اتصل بنا',
    'control_page' => $dashboardUrl . '/homepage-settings',
    'control_name' => 'إعدادات الصفحة الرئيسية → الفوتر (يستخدم نفس الروابط)',
    'setting_key' => 'footer_whatsapp_url',
    'current_value' => \App\Models\SiteSetting::get('footer_whatsapp_url', '') ?: '⚠️ غير مضاف',
];

$socialLinks[] = [
    'location' => 'صفحة اتصل بنا',
    'platform' => 'فيسبوك',
    'purpose' => 'زر الفيسبوك في صفحة اتصل بنا',
    'control_page' => $dashboardUrl . '/homepage-settings',
    'control_name' => 'إعدادات الصفحة الرئيسية → الفوتر (يستخدم نفس الروابط)',
    'setting_key' => 'footer_facebook_url',
    'current_value' => \App\Models\SiteSetting::get('footer_facebook_url', '') ?: '⚠️ غير مضاف',
];

$socialLinks[] = [
    'location' => 'صفحة اتصل بنا',
    'platform' => 'تيليجرام',
    'purpose' => 'زر التيليجرام في صفحة اتصل بنا',
    'control_page' => $dashboardUrl . '/homepage-settings',
    'control_name' => 'إعدادات الصفحة الرئيسية → الفوتر (يستخدم نفس الروابط)',
    'setting_key' => 'footer_telegram_url',
    'current_value' => \App\Models\SiteSetting::get('footer_telegram_url', '') ?: '⚠️ غير مضاف',
];

// ========================================
// 7. زر التيليجرام العائم (Floating)
// ========================================
$socialLinks[] = [
    'location' => 'زر تيليجرام عائم (جميع الصفحات)',
    'platform' => 'تيليجرام',
    'purpose' => 'زر التيليجرام العائم أسفل يسار الشاشة',
    'control_page' => $dashboardUrl . '/default-social-links أو الدولة المحددة',
    'control_name' => 'يظهر من السوشيال الافتراضي أو تيليجرام الدولة',
    'setting_key' => 'telegram_url (من الدولة أو الإعدادات)',
    'current_value' => 'يعتمد على الصفحة المعروضة',
];

echo "جاري إنشاء ملف Excel لـ " . count($socialLinks) . " رابط سوشيال...\n";

// Create Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setRightToLeft(true);
$sheet->setTitle('روابط السوشيال');

// Set headers
$headers = [
    'A1' => 'مكان الظهور',
    'B1' => 'المنصة',
    'C1' => 'الغرض',
    'D1' => 'صفحة التحكم',
    'E1' => 'اسم الإعداد',
    'F1' => 'القيمة الحالية',
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Style headers
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];
$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(30);

// Set column widths
$sheet->getColumnDimension('A')->setWidth(35);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(45);
$sheet->getColumnDimension('D')->setWidth(50);
$sheet->getColumnDimension('E')->setWidth(45);
$sheet->getColumnDimension('F')->setWidth(50);

$row = 2;
$lastLocation = '';
$locationColors = [
    'الفوتر' => 'E0E7FF',
    'الصفحة الرئيسية' => 'FEF3C7',
    'صفحات النتائج' => 'D1FAE5',
    'صفحات دولة' => 'FEE2E2',
    'صفحات ' => 'DBEAFE',
    'صفحة اتصل بنا' => 'F3E8FF',
    'زر تيليجرام عائم' => 'ECFDF5',
];

foreach ($socialLinks as $link) {
    $sheet->setCellValue('A' . $row, $link['location']);
    $sheet->setCellValue('B' . $row, $link['platform']);
    $sheet->setCellValue('C' . $row, $link['purpose']);
    $sheet->setCellValue('D' . $row, $link['control_page']);
    $sheet->setCellValue('E' . $row, $link['control_name']);
    $sheet->setCellValue('F' . $row, $link['current_value']);
    
    // Style row
    $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    
    // Color by location
    $bgColor = 'FFFFFF';
    foreach ($locationColors as $prefix => $color) {
        if (strpos($link['location'], $prefix) !== false) {
            $bgColor = $color;
            break;
        }
    }
    
    $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]]
    ]);
    
    // Highlight missing values
    if (strpos($link['current_value'], '⚠️') !== false || empty($link['current_value'])) {
        $sheet->getStyle('F' . $row)->applyFromArray([
            'font' => ['color' => ['rgb' => 'DC2626'], 'bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']]
        ]);
    }
    
    $sheet->getRowDimension($row)->setRowHeight(25);
    
    $row++;
}

// Freeze first row
$sheet->freezePane('A2');

// Auto-filter
$sheet->setAutoFilter('A1:F' . ($row - 1));

// Save file
$filename = 'social_links_' . date('Y-m-d_H-i-s') . '.xlsx';
$filepath = __DIR__ . '/storage/app/public/' . $filename;

$writer = new Xlsx($spreadsheet);
$writer->save($filepath);

echo "\n✅ تم إنشاء الملف بنجاح!\n";
echo "📁 الرابط: https://ntegty.com/uploads/" . $filename . "\n";
echo "📊 عدد الروابط: " . ($row - 2) . "\n";
echo "\n=== ملخص ===\n";
echo "- روابط الفوتر: 3\n";
echo "- روابط الصفحة الرئيسية: 4\n";
echo "- روابط افتراضية: " . $defaultLinks->count() . "\n";
echo "- روابط الدول: " . $countries->count() . "\n";
echo "- روابط أنواع الامتحانات: " . $examTypes->count() . "\n";
