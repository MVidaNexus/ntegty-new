<?php

namespace App\Services;

use App\Models\ExamType;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\Result;
use App\Models\SiteSetting;

class SchemaService
{
    /**
     * Get base organization schema
     */
    public static function organization(): array
    {
        $siteName = SiteSetting::get('site_name', 'نتيجتي');
        $siteUrl = config('app.url');
        
        return [
            '@type' => 'Organization',
            '@id' => $siteUrl . '/#organization',
            'name' => $siteName,
            'url' => $siteUrl,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $siteUrl . '/images/og-default.png',
                'width' => 1200,
                'height' => 630,
            ],
            'sameAs' => array_filter([
                SiteSetting::get('facebook_url'),
                SiteSetting::get('twitter_url'),
                SiteSetting::get('youtube_url'),
            ]),
        ];
    }

    /**
     * Website schema with search action
     */
    public static function website(): array
    {
        $siteName = SiteSetting::get('site_name', 'نتيجتي');
        $siteUrl = config('app.url');
        
        return [
            '@type' => 'WebSite',
            '@id' => $siteUrl . '/#website',
            'url' => $siteUrl,
            'name' => $siteName,
            'description' => SiteSetting::get('seo_description', 'بوابة النتائج التعليمية - منصة عرض نتائج الطلاب وشهاداتهم لجميع الدول والأنظمة التعليمية'),
            'publisher' => [
                '@id' => $siteUrl . '/#organization',
            ],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $siteUrl . '/search?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
            'inLanguage' => 'ar',
        ];
    }

    /**
     * WebPage schema
     */
    public static function webPage(string $title, string $description, ?string $url = null): array
    {
        $siteUrl = config('app.url');
        
        return [
            '@type' => 'WebPage',
            '@id' => ($url ?? request()->url()) . '/#webpage',
            'url' => $url ?? request()->url(),
            'name' => $title,
            'description' => $description,
            'isPartOf' => [
                '@id' => $siteUrl . '/#website',
            ],
            'inLanguage' => 'ar',
            'datePublished' => now()->toIso8601String(),
            'dateModified' => now()->toIso8601String(),
        ];
    }

    /**
     * BreadcrumbList schema
     */
    public static function breadcrumb(array $items): array
    {
        $listItems = [];
        $position = 1;
        
        foreach ($items as $item) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }
        
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];
    }

    /**
     * EducationalOccupationalCredential schema for exam types
     */
    public static function educationalCredential(ExamType $examType): array
    {
        $siteUrl = config('app.url');
        $country = $examType->country;
        
        // Determine credential category
        $credentialCategory = match(true) {
            str_contains($examType->slug, 'primary') => 'PrimaryEducation',
            str_contains($examType->slug, 'prep') => 'LowerSecondaryEducation',
            str_contains($examType->slug, 'secondary') => 'UpperSecondaryEducation',
            str_contains($examType->slug, 'diploma') => 'VocationalEducation',
            default => 'SecondaryEducation',
        };
        
        // Determine education level
        $educationLevel = match(true) {
            str_contains($examType->slug, 'primary') => 'Primary School',
            str_contains($examType->slug, 'prep') => 'Middle School',
            str_contains($examType->slug, 'secondary') => 'High School',
            str_contains($examType->slug, 'diploma') => 'Vocational Training',
            default => 'Secondary Education',
        };
        
        $currentUrl = url()->current();
        
        return [
            '@type' => 'EducationalOccupationalCredential',
            '@id' => $currentUrl . '/#credential',
            'name' => $examType->name_ar,
            'description' => $examType->seo_description ?: "شهادة {$examType->name_ar} في {$country->name_ar}",
            'credentialCategory' => $credentialCategory,
            'educationalLevel' => $educationLevel,
            'recognizedBy' => [
                '@type' => 'Organization',
                'name' => "وزارة التربية والتعليم - {$country->name_ar}",
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressCountry' => strtoupper($country->code),
                ],
            ],
            'validIn' => [
                '@type' => 'Country',
                'name' => $country->name_ar,
            ],
            'url' => $currentUrl,
            'inLanguage' => 'ar',
        ];
    }

    /**
     * CollectionPage schema for listing pages
     */
    public static function collectionPage(string $title, string $description, array $items = []): array
    {
        $schema = [
            '@type' => 'CollectionPage',
            '@id' => request()->url() . '/#collectionpage',
            'url' => request()->url(),
            'name' => $title,
            'description' => $description,
            'inLanguage' => 'ar',
        ];
        
        if (!empty($items)) {
            $schema['mainEntity'] = [
                '@type' => 'ItemList',
                'itemListElement' => array_map(function ($item, $index) {
                    $listItem = [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => $item['name'] ?? '',
                    ];
                    if (isset($item['url'])) {
                        $listItem['url'] = $item['url'];
                    }
                    return $listItem;
                }, $items, array_keys($items)),
            ];
        }
        
        return $schema;
    }

    /**
     * Full schema for homepage
     */
    public static function homePage(): string
    {
        $siteUrl = config('app.url');
        $siteName = SiteSetting::get('site_name', 'نتيجتي');
        $siteDescription = SiteSetting::get('seo_description', 'بوابة النتائج التعليمية');
        
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                self::website(),
                self::webPage($siteName, $siteDescription, $siteUrl),
                self::breadcrumb([
                    ['name' => 'الرئيسية', 'url' => $siteUrl],
                ]),
            ],
        ];
        
        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Full schema for country page
     */
    public static function countryPage(Country $country, array $examTypes = []): string
    {
        $siteUrl = config('app.url');
        $title = "نتائج الامتحانات في {$country->name_ar}";
        $description = "عرض نتائج جميع الشهادات والامتحانات في {$country->name_ar}";
        
        $items = [];
        foreach ($examTypes as $examType) {
            $items[] = [
                'name' => $examType->name_ar,
            ];
        }
        
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                self::collectionPage($title, $description, $items),
                self::breadcrumb([
                    ['name' => 'الرئيسية', 'url' => $siteUrl],
                    ['name' => $country->name_ar],
                ]),
            ],
        ];
        
        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Full schema for exam type page
     */
    public static function examTypePage(ExamType $examType, array $governorates = []): string
    {
        $siteUrl = config('app.url');
        $country = $examType->country;
        
        $items = [];
        foreach ($governorates as $gov) {
            // Build URL without using route() to avoid missing route errors
            $items[] = [
                'name' => $gov->name_ar,
            ];
        }
        
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                self::educationalCredential($examType),
                self::collectionPage(
                    "نتائج {$examType->name_ar} - {$country->name_ar}",
                    $examType->seo_description ?: "نتائج {$examType->name_ar} في جميع محافظات {$country->name_ar}",
                    $items
                ),
                self::breadcrumb([
                    ['name' => 'الرئيسية', 'url' => $siteUrl],
                    ['name' => $country->name_ar],
                    ['name' => $examType->name_ar],
                ]),
            ],
        ];
        
        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Simplified schema for governorate list page (Iraq/Libya index)
     */
    public static function simpleExamTypePage(string $examTypeName, string $countryName, array $governorates = []): string
    {
        $siteUrl = config('app.url');
        
        $items = [];
        foreach ($governorates as $gov) {
            $items[] = [
                'name' => $gov->name_ar ?? $gov['name_ar'] ?? '',
            ];
        }
        
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                self::collectionPage(
                    "نتائج {$examTypeName} - {$countryName}",
                    "نتائج {$examTypeName} في جميع محافظات {$countryName}",
                    $items
                ),
                self::breadcrumb([
                    ['name' => 'الرئيسية', 'url' => $siteUrl],
                    ['name' => $countryName],
                ]),
            ],
        ];
        
        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Full schema for governorate results page
     * Uses governorate SEO fields if available
     */
    public static function governoratePage(ExamType $examType, Governorate $governorate): string
    {
        $siteUrl = config('app.url');
        $country = $examType->country;
        
        // Use governorate SEO fields if available, otherwise use defaults
        $defaultTitle = "نتائج {$examType->name_ar} - محافظة {$governorate->name_ar}";
        $defaultDesc = "نتائج طلاب {$examType->name_ar} في محافظة {$governorate->name_ar}";
        
        $title = $governorate->seo_title ?: $defaultTitle;
        $description = $governorate->seo_description ?: $defaultDesc;
        
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                self::webPage($title, $description),
                self::breadcrumb([
                    ['name' => 'الرئيسية', 'url' => $siteUrl],
                    ['name' => $country->name_ar],
                    ['name' => $examType->name_ar],
                    ['name' => $governorate->name_ar],
                ]),
            ],
        ];
        
        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Simplified schema for governorate page without exam type object (for Iraq/Libya)
     */
    public static function simpleGovernoratePage(Governorate $governorate, string $examTypeName): string
    {
        $siteUrl = config('app.url');
        $country = $governorate->country;
        $title = "نتائج {$examTypeName} - {$governorate->name_ar}";
        
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                self::webPage($title, "نتائج طلاب {$examTypeName} في محافظة {$governorate->name_ar}"),
                self::breadcrumb([
                    ['name' => 'الرئيسية', 'url' => $siteUrl],
                    ['name' => $country->name_ar, 'url' => url("/{$country->code}")],
                    ['name' => $governorate->name_ar],
                ]),
            ],
        ];
        
        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Full schema for individual result page
     */
    public static function resultPage(Result $result): string
    {
        $siteUrl = config('app.url');
        $examType = $result->examType;
        $country = $examType->country;
        $governorate = $result->governorate;
        
        $title = "نتيجة {$result->student_name} - {$examType->name_ar}";
        
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                self::webPage($title, "نتيجة الطالب {$result->student_name} في {$examType->name_ar} - رقم الجلوس: {$result->seat_number}"),
                self::breadcrumb([
                    ['name' => 'الرئيسية', 'url' => $siteUrl],
                    ['name' => $country->name_ar],
                    ['name' => $examType->name_ar],
                    ['name' => $governorate->name_ar],
                    ['name' => $result->student_name],
                ]),
            ],
        ];
        
        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Schema for certificate page
     */
    public static function certificatePage(): string
    {
        $siteUrl = config('app.url');
        
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                self::webPage('شهادة تقدير', 'إنشاء شهادة تقدير مخصصة للطلاب المتفوقين'),
                self::breadcrumb([
                    ['name' => 'الرئيسية', 'url' => $siteUrl],
                    ['name' => 'شهادة تقدير', 'url' => $siteUrl . '/certificate'],
                ]),
            ],
        ];
        
        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Schema for contact page
     */
    public static function contactPage(): string
    {
        $siteUrl = config('app.url');
        
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                [
                    '@type' => 'ContactPage',
                    '@id' => $siteUrl . '/contact#contactpage',
                    'url' => $siteUrl . '/contact',
                    'name' => 'اتصل بنا',
                    'description' => 'تواصل معنا لأي استفسارات أو ملاحظات',
                    'mainEntity' => self::organization(),
                ],
                self::breadcrumb([
                    ['name' => 'الرئيسية', 'url' => $siteUrl],
                    ['name' => 'اتصل بنا', 'url' => $siteUrl . '/contact'],
                ]),
            ],
        ];
        
        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
