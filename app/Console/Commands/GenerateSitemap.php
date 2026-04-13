<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.xml file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $urls = [];

        // Static Pages
        $urls[] = route('home');
        $urls[] = route('certificate.index');
        $urls[] = route('contact');
        // $urls[] = route('privacy'); 
        // $urls[] = route('terms');

        // Egypt Pages
        $urls[] = route('egypt.index');
        $urls[] = route('egypt.preparatory');
        $urls[] = route('egypt.secondary');
        $urls[] = route('egypt.diplomas.index');
        // Diplomas sub-pages removed as requested

        // Egypt Governorates for Preparatory
        $egypt = Country::where('code', 'EG')->with('governorates')->first();
        if ($egypt) {
            foreach ($egypt->governorates as $gov) {
                // Pass the model instance so Laravel uses getRouteKeyName() (slug)
                $urls[] = route('egypt.governorate.results', $gov); 
            }
        }

        // Other Countries
        $countries = Country::where('code', '!=', 'EG')->where('is_active', true)->with('examTypes')->get();

        foreach ($countries as $country) {
            // Country Index
            $urls[] = route('country.index', $country);

            // Exam Types
            foreach ($country->examTypes as $examType) {
                // Ensure slug exists, if not generate/guess it or skip
                if ($examType->slug) {
                    $urls[] = route('country.exam', ['country' => $country, 'slug' => $examType->slug]);
                }
            }
        }

        // XML Construction
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . $url . '</loc>';
            $xml .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        File::put(base_path('sitemap.xml'), $xml);

        $this->info('Sitemap generated successfully at public/sitemap.xml');
    }
}
