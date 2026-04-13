<?php
/**
 * Script to generate Excel file with all website pages
 * Excludes student result pages
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

// Collect all public pages (excluding student pages and dashboard)
$pages = [];

// 1. Homepage
$pages[] = ['url' => '/', 'type' => 'home'];

// 2. Static pages
$pages[] = ['url' => '/contact', 'type' => 'static'];
$pages[] = ['url' => '/privacy', 'type' => 'static'];
$pages[] = ['url' => '/terms', 'type' => 'static'];
$pages[] = ['url' => '/sitemap', 'type' => 'static'];

// 3. Egypt main pages
$pages[] = ['url' => '/egypt', 'type' => 'country'];
$pages[] = ['url' => '/egypt/preparatory', 'type' => 'exam'];
$pages[] = ['url' => '/egypt/secondary', 'type' => 'exam'];
$pages[] = ['url' => '/egypt/diplomas', 'type' => 'exam'];
$pages[] = ['url' => '/egypt/azhar/primary', 'type' => 'exam'];
$pages[] = ['url' => '/egypt/azhar/preparatory', 'type' => 'exam'];
$pages[] = ['url' => '/egypt/azhar/secondary', 'type' => 'exam'];

// 4. Secondary branches
$secondaryExam = \App\Models\ExamType::where('code', 'eg_secondary')->first();
if ($secondaryExam) {
    $branches = $secondaryExam->branches()->where('is_active', true)->get();
    foreach ($branches as $branch) {
        $pages[] = ['url' => '/egypt/secondary/' . $branch->code, 'type' => 'branch'];
        $pages[] = ['url' => '/egypt/secondary/' . $branch->code . '/all', 'type' => 'branch_results'];
    }
}

// 5. Secondary all results pages
$pages[] = ['url' => '/egypt/secondary/all', 'type' => 'results'];
$pages[] = ['url' => '/egypt/secondary/all?system_type=old', 'type' => 'results'];
$pages[] = ['url' => '/egypt/secondary/all?system_type=new', 'type' => 'results'];

// 6. Governorate pages (preparatory)
$governorates = \App\Models\Governorate::where('country_id', function($q) {
    $q->select('id')->from('countries')->where('code', 'EG');
})->get();

foreach ($governorates as $gov) {
    $pages[] = ['url' => '/egypt/preparatory/' . $gov->slug, 'type' => 'governorate'];
    $pages[] = ['url' => '/egypt/preparatory/' . $gov->slug . '/all', 'type' => 'governorate_results'];
    $pages[] = ['url' => '/egypt/preparatory/' . $gov->slug . '/top', 'type' => 'governorate_top'];
}

// 7. Other countries (if any)
$countries = \App\Models\Country::where('is_active', true)->where('code', '!=', 'EG')->get();
foreach ($countries as $country) {
    $pages[] = ['url' => '/' . $country->slug, 'type' => 'country'];
    
    // Get exam types for this country
    $examTypes = \App\Models\ExamType::where('country_id', $country->id)->get();
    foreach ($examTypes as $exam) {
        $pages[] = ['url' => '/' . $country->slug . '/' . $exam->slug, 'type' => 'exam'];
    }
}

echo "جاري جمع بيانات " . count($pages) . " صفحة...\n";

// Function to fetch page data
function fetchPageData($url) {
    $fullUrl = 'https://ntegty.com' . $url;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode != 200 || !$html) {
        return ['title' => 'خطأ في التحميل', 'content' => ''];
    }
    
    // Extract title
    $title = '';
    if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $html, $matches)) {
        $title = html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
    }
    
    // Extract content - look for main content areas
    $content = '';
    
    // Try to get meta description first
    if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\']([^"\']+)["\']/i', $html, $matches)) {
        $content = html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
    }
    
    // Also try og:description
    if (empty($content) && preg_match('/<meta\s+property=["\']og:description["\']\s+content=["\']([^"\']+)["\']/i', $html, $matches)) {
        $content = html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
    }
    
    // Get main content from page (h1, h2, paragraphs)
    $mainContent = [];
    
    // Get h1
    if (preg_match_all('/<h1[^>]*>([^<]+)<\/h1>/iu', $html, $matches)) {
        foreach ($matches[1] as $h1) {
            $h1 = strip_tags($h1);
            $h1 = trim(preg_replace('/\s+/', ' ', $h1));
            if (!empty($h1)) {
                $mainContent[] = '[H1] ' . $h1;
            }
        }
    }
    
    // Get h2
    if (preg_match_all('/<h2[^>]*>([^<]+)<\/h2>/iu', $html, $matches)) {
        foreach (array_slice($matches[1], 0, 5) as $h2) {
            $h2 = strip_tags($h2);
            $h2 = trim(preg_replace('/\s+/', ' ', $h2));
            if (!empty($h2)) {
                $mainContent[] = '[H2] ' . $h2;
            }
        }
    }
    
    if (!empty($mainContent)) {
        $content .= "\n\n--- العناوين ---\n" . implode("\n", $mainContent);
    }
    
    return [
        'title' => $title,
        'content' => $content
    ];
}

// Create Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setRightToLeft(true);

// Set headers
$sheet->setCellValue('A1', 'الرابط');
$sheet->setCellValue('B1', 'عنوان الصفحة');
$sheet->setCellValue('C1', 'محتوى الصفحة');

// Style headers
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];
$sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

// Set column widths
$sheet->getColumnDimension('A')->setWidth(60);
$sheet->getColumnDimension('B')->setWidth(80);
$sheet->getColumnDimension('C')->setWidth(100);

$row = 2;
$total = count($pages);

foreach ($pages as $index => $page) {
    $url = $page['url'];
    $fullUrl = $baseUrl . $url;
    
    echo "(" . ($index + 1) . "/$total) جاري معالجة: $url\n";
    
    $data = fetchPageData($url);
    
    $sheet->setCellValue('A' . $row, $fullUrl);
    $sheet->setCellValue('B' . $row, $data['title']);
    $sheet->setCellValue('C' . $row, $data['content']);
    
    // Style row
    $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
        'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    
    if ($row % 2 == 0) {
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']]
        ]);
    }
    
    $sheet->getRowDimension($row)->setRowHeight(-1); // Auto height
    
    $row++;
    
    // Small delay to not overload server
    usleep(100000); // 0.1 second
}

// Freeze first row
$sheet->freezePane('A2');

// Save file
$filename = 'website_pages_' . date('Y-m-d_H-i-s') . '.xlsx';
$filepath = __DIR__ . '/storage/app/' . $filename;

$writer = new Xlsx($spreadsheet);
$writer->save($filepath);

echo "\n✅ تم إنشاء الملف بنجاح!\n";
echo "📁 المسار: $filepath\n";
echo "📊 عدد الصفحات: " . ($row - 2) . "\n";
