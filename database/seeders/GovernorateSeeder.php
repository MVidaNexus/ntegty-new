<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $egypt = Country::where('code', 'EG')->first();
        $iraq = Country::where('code', 'IQ')->first();

        // Egyptian Governorates (27)
        $egyptianGovernorates = [
            ['name_ar' => 'القاهرة', 'name_en' => 'Cairo'],
            ['name_ar' => 'الجيزة', 'name_en' => 'Giza'],
            ['name_ar' => 'الإسكندرية', 'name_en' => 'Alexandria'],
            ['name_ar' => 'الدقهلية', 'name_en' => 'Dakahlia'],
            ['name_ar' => 'البحر الأحمر', 'name_en' => 'Red Sea'],
            ['name_ar' => 'البحيرة', 'name_en' => 'Beheira'],
            ['name_ar' => 'الفيوم', 'name_en' => 'Fayoum'],
            ['name_ar' => 'الغربية', 'name_en' => 'Gharbia'],
            ['name_ar' => 'الإسماعيلية', 'name_en' => 'Ismailia'],
            ['name_ar' => 'المنوفية', 'name_en' => 'Monufia'],
            ['name_ar' => 'المنيا', 'name_en' => 'Minya'],
            ['name_ar' => 'القليوبية', 'name_en' => 'Qalyubia'],
            ['name_ar' => 'الوادي الجديد', 'name_en' => 'New Valley'],
            ['name_ar' => 'السويس', 'name_en' => 'Suez'],
            ['name_ar' => 'اسوان', 'name_en' => 'Aswan'],
            ['name_ar' => 'اسيوط', 'name_en' => 'Asyut'],
            ['name_ar' => 'بني سويف', 'name_en' => 'Beni Suef'],
            ['name_ar' => 'بورسعيد', 'name_en' => 'Port Said'],
            ['name_ar' => 'دمياط', 'name_en' => 'Damietta'],
            ['name_ar' => 'الشرقية', 'name_en' => 'Sharqia'],
            ['name_ar' => 'جنوب سيناء', 'name_en' => 'South Sinai'],
            ['name_ar' => 'كفر الشيخ', 'name_en' => 'Kafr El Sheikh'],
            ['name_ar' => 'مطروح', 'name_en' => 'Matrouh'],
            ['name_ar' => 'الأقصر', 'name_en' => 'Luxor'],
            ['name_ar' => 'قنا', 'name_en' => 'Qena'],
            ['name_ar' => 'شمال سيناء', 'name_en' => 'North Sinai'],
            ['name_ar' => 'سوهاج', 'name_en' => 'Sohag'],
        ];

        foreach ($egyptianGovernorates as $gov) {
            $logoPath = $this->fetchGovernorateLogoFromWikipedia($gov['name_en']);
            
            Governorate::create([
                'country_id' => $egypt->id,
                'name_ar' => $gov['name_ar'],
                'name_en' => $gov['name_en'],
                'slug' => \Illuminate\Support\Str::slug($gov['name_en']),
                'logo_path' => $logoPath,
            ]);
        }

        // Iraqi Provinces (18)
        $iraqiProvinces = [
            ['name_ar' => 'بغداد', 'name_en' => 'Baghdad'],
            ['name_ar' => 'البصرة', 'name_en' => 'Basra'],
            ['name_ar' => 'نينوى', 'name_en' => 'Nineveh'],
            ['name_ar' => 'الأنبار', 'name_en' => 'Anbar'],
            ['name_ar' => 'أربيل', 'name_en' => 'Erbil'],
            ['name_ar' => 'كركوك', 'name_en' => 'Kirkuk'],
            ['name_ar' => 'النجف', 'name_en' => 'Najaf'],
            ['name_ar' => 'كربلاء', 'name_en' => 'Karbala'],
            ['name_ar' => 'بابل', 'name_en' => 'Babylon'],
            ['name_ar' => 'ديالى', 'name_en' => 'Diyala'],
            ['name_ar' => 'ذي قار', 'name_en' => 'Dhi Qar'],
            ['name_ar' => 'المثنى', 'name_en' => 'Al-Muthanna'],
            ['name_ar' => 'القادسية', 'name_en' => 'Al-Qadisiyyah'],
            ['name_ar' => 'ميسان', 'name_en' => 'Maysan'],
            ['name_ar' => 'واسط', 'name_en' => 'Wasit'],
            ['name_ar' => 'صلاح الدين', 'name_en' => 'Saladin'],
            ['name_ar' => 'السليمانية', 'name_en' => 'Sulaymaniyah'],
            ['name_ar' => 'دهوك', 'name_en' => 'Dohuk'],
        ];

        foreach ($iraqiProvinces as $gov) {
            $logoPath = $this->fetchGovernorateLogoFromWikipedia($gov['name_en']);
            
            Governorate::create([
                'country_id' => $iraq->id,
                'name_ar' => $gov['name_ar'],
                'name_en' => $gov['name_en'],
                'slug' => \Illuminate\Support\Str::slug($gov['name_en']),
                'logo_path' => $logoPath,
            ]);
        }
    }

    /**
     * Fetch governorate logo from Wikimedia Commons
     */
    private function fetchGovernorateLogoFromWikipedia(string $governorateName): ?string
    {
        try {
            // Search for coat of arms on Wikimedia Commons
            $searchUrl = "https://commons.wikimedia.org/w/api.php";
            $response = Http::get($searchUrl, [
                'action' => 'query',
                'format' => 'json',
                'list' => 'search',
                'srsearch' => "{$governorateName} governorate coat of arms",
                'srnamespace' => 6, // File namespace
                'srlimit' => 1,
            ]);

            if (!$response->successful() || empty($response->json('query.search'))) {
                return $this->generatePlaceholderLogo($governorateName);
            }

            // Get the first result
            $firstResult = $response->json('query.search.0.title');
            
            // Get image URL
            $imageResponse = Http::get($searchUrl, [
                'action' => 'query',
                'format' => 'json',
                'titles' => $firstResult,
                'prop' => 'imageinfo',
                'iiprop' => 'url',
            ]);

            $pages = $imageResponse->json('query.pages');
            $page = reset($pages);
            
            if (isset($page['imageinfo'][0]['url'])) {
                $imageUrl = $page['imageinfo'][0]['url'];
                return $this->downloadAndConvertImage($imageUrl, $governorateName);
            }

            return $this->generatePlaceholderLogo($governorateName);
        } catch (\Exception $e) {
            \Log::warning("Failed to fetch logo for {$governorateName}: " . $e->getMessage());
            return $this->generatePlaceholderLogo($governorateName);
        }
    }

    /**
     * Download image and convert to WebP
     */
    private function downloadAndConvertImage(string $url, string $name): string
    {
        try {
            $imageContent = Http::get($url)->body();
            $filename = str_replace(' ', '_', strtolower($name)) . '.webp';
            $path = "logos/{$filename}";
            
            // Save original image temporarily
            $tempPath = storage_path("app/temp_{$filename}");
            file_put_contents($tempPath, $imageContent);
            
            // Convert to WebP (if Intervention Image is available)
            // For now, just save as is
            Storage::disk('public')->put($path, $imageContent);
            
            // Clean up
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            return $path;
        } catch (\Exception $e) {
            \Log::warning("Failed to download image for {$name}: " . $e->getMessage());
            return $this->generatePlaceholderLogo($name);
        }
    }

    /**
     * Generate placeholder logo
     */
    private function generatePlaceholderLogo(string $name): string
    {
        // Return a placeholder path or empty string
        // In production, you could generate an SVG with the first letter
        return '';
    }
}
