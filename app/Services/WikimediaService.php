<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class WikimediaService
{
    /**
     * Fetch governorate logo from Wikimedia Commons
     */
    public function fetchGovernoratelogo(string $governorateName, string $country = 'Egypt'): ?string
    {
        try {
            $searchTerm = "{$governorateName} {$country} governorate coat of arms";
            
            // Search for image on Wikimedia Commons
            $searchUrl = "https://commons.wikimedia.org/w/api.php";
            $response = Http::timeout(10)->get($searchUrl, [
                'action' => 'query',
                'format' => 'json',
                'list' => 'search',
                'srsearch' => $searchTerm,
                'srnamespace' => 6, // File namespace
                'srlimit' => 1,
            ]);

            if (!$response->successful() || empty($response->json('query.search'))) {
                Log::info("No logo found for {$governorateName}");
                return null;
            }

            // Get the first result
            $firstResult = $response->json('query.search.0.title');
            
            // Get image URL
            $imageResponse = Http::timeout(10)->get($searchUrl, [
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
                return $this->downloadAndSaveImage($imageUrl, $governorateName);
            }

            return null;

        } catch (\Exception $e) {
            Log::warning("Failed to fetch logo for {$governorateName}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Download image and save to storage
     */
    private function downloadAndSaveImage(string $url, string $name): ?string
    {
        try {
            $imageContent = Http::timeout(15)->get($url)->body();
            $filename = str_replace(' ', '_', strtolower($name)) . '.png';
            $path = "logos/{$filename}";
            
            Storage::disk('public')->put($path, $imageContent);
            
            return $path;

        } catch (\Exception $e) {
            Log::warning("Failed to download image for {$name}: " . $e->getMessage());
            return null;
        }
    }
}
