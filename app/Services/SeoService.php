<?php

namespace App\Services;

use App\Models\SiteSetting;

class SeoService
{
    /**
     * Default OG image path
     */
    private const DEFAULT_OG_IMAGE = '/images/og-default.png';

    /**
     * Get the default OG image URL
     */
    private function getDefaultOgImage(): string
    {
        $ogImage = SiteSetting::get('og_image', '');
        
        if (!empty($ogImage)) {
            return url('/' . $ogImage);
        }
        
        return url(self::DEFAULT_OG_IMAGE);
    }

    /**
     * Generate meta tags for a page
     */
    public function generateMetaTags(string $title, string $description, ?string $image = null, ?string $canonical = null): array
    {
        $siteName = SiteSetting::get('site_name', 'نتيجتي');
        // Check if title already contains site name to avoid duplication
        if (strpos($title, $siteName) === false) {
            $fullTitle = "{$title} | {$siteName}";
        } else {
            $fullTitle = $title;
        }
        
        $ogImage = $image ?? $this->getDefaultOgImage();
        
        return [
            'title' => $fullTitle,
            'description' => $description,
            'canonical' => $canonical ?? url()->current(),
            'robots' => 'index, follow',
            'og_title' => $fullTitle,
            'og_description' => $description,
            'og_image' => $ogImage,
            'og_image_width' => 1200,
            'og_image_height' => 630,
            'og_type' => 'website',
            'og_url' => $canonical ?? url()->current(),
            'twitter_card' => 'summary_large_image',
            'twitter_site' => '@ntegty',
            'twitter_title' => $fullTitle,
            'twitter_description' => $description,
            'twitter_image' => $ogImage,
        ];
    }

    /**
     * Generate Organization Schema for Home Page with WebSite and SearchAction
     */
    public function generateOrganizationSchema(): string
    {
        return SchemaService::homePage();
    }
    
    /**
     * Generate Breadcrumb List Schema
     */
    public function generateBreadcrumbSchema(array $breadcrumbs): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(function($crumb, $index) {
                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $crumb['name'],
                    'item' => $crumb['url'] ?? null,
                ];
            }, $breadcrumbs, array_keys($breadcrumbs)),
        ];
        
        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
