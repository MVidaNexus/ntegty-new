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
    public static function webPage(string $title, string $description, ?string $url = null, ?string $datePublished = null, ?string $dateModified = null): array
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
            'datePublished' => $datePublished ?? now()->toIso8601String(),
            'dateModified' => $dateModified ?? now()->toIso8601String(),
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
    public static function collectionPage(string $title, string $description, array $items = [], ?string $datePublished = null, ?string $dateModified = null): array
    {
        $schema = [
            '@type' => 'CollectionPage',
            '@id' => request()->url() . '/#collectionpage',
            'url' => request()->url(),
            'name' => $title,
            'description' => $description,
            'inLanguage' => 'ar',
            'datePublished' => $datePublished ?? now()->toIso8601String(),
            'dateModified' => $dateModified ?? now()->toIso8601String(),
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
        
        $faqs = [
            [
                'question' => 'كيف يمكنني الاستعلام عن نتيجة الامتحانات برقم الجلوس أو الاسم؟',
                'answer' => 'يمكنك الاستعلام عن نتيجتك بكل سهولة عبر منصة نتيجتي بالخطوات التالية: 1. اختر علم الدولة الخاصة بك من الصفحة الرئيسية (مثل مصر، العراق، ليبيا، إلخ). 2. اختر نوع الشهادة التعليمية (مثل الشهادة الإعدادية أو الثانوية). 3. أدخل رقم جلوسك أو اسمك الرباعي في خانة البحث، ثم اضغط على زر "عرض النتيجة" لتظهر لك درجاتك كاملة مع المجموع الكلي والنسبة المئوية فوراً وبشكل مجاني تماماً.'
            ],
            [
                'question' => 'ما هي الدول والشهادات التعليمية التي تغطيها بوابة نتيجتي؟',
                'answer' => 'تغطي بوابة نتيجتي نتائج الامتحانات الرسمية للعديد من الدول العربية والشهادات العامة والأزهرية، بما في ذلك: نتائج الامتحانات في مصر (الشهادة الإعدادية، الثانوية العامة، الدبلومات الفنية، الشهادة الثانوية الأزهرية)، نتائج الامتحانات في العراق (الصف الثالث المتوسط، السادس الاعدادي)، ونتائج الشهادات العامة في ليبيا، السودان، فلسطين، اليمن، الأردن، وسوريا.'
            ],
            [
                'question' => 'هل النتائج المعروضة على المنصة رسمية ومطابقة للوزارة؟',
                'answer' => 'نعم، جميع النتائج المعروضة على منصة نتيجتي هي نتائج رسمية ومطابقة 100% للنتائج المعتمدة من وزارة التربية والتعليم والتعليم الفني في مصر، ووزارة التربية العراقية، وكافة الهيئات التعليمية الرسمية في الدول العربية. يتم تحديث ورفع قواعد البيانات ولينكات الاستعلام مباشرة بالتنسيق مع الجهات المعنية فور اعتمادها رسمياً.'
            ],
            [
                'question' => 'كيف يمكنني استخدام خدمة "صمم شهادتك" المجانية؟',
                'answer' => 'نوفر خدمة حصرية ومجانية تتيح للطلاب وأولياء الأمور تصميم شهادة تقدير للمتفوقين بشكل فوري. بعد الحصول على نتيجتك، يمكنك الانتقال إلى قسم "شهادة تقدير"، وإدخال اسم الطالب، المدرسة، المجموع الكلي، وحفظ الشهادة أو طباعتها بتصميم فائق الجودة والجمال لمشاركتها مع الأهل والأصدقاء احتفالاً بالنجاح.'
            ]
        ];

        $faqSchema = [
            '@type' => 'FAQPage',
            'mainEntity' => array_map(function($faq) {
                return [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq['answer']
                    ]
                ];
            }, $faqs)
        ];

        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                self::website(),
                self::webPage($siteName, $siteDescription, $siteUrl),
                self::breadcrumb([
                    ['name' => 'الرئيسية', 'url' => $siteUrl],
                ]),
                $faqSchema
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
                self::collectionPage($title, $description, $items, $country->created_at?->toIso8601String(), $country->updated_at?->toIso8601String()),
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
                    $items,
                    $examType->created_at?->toIso8601String(),
                    $examType->updated_at?->toIso8601String()
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
     * Full optimized schema for secondary exam page (Thanaweya Amma) with FAQPage and WebPage
     */
    public static function secondaryExamPage(ExamType $examType): string
    {
        $siteUrl = config('app.url');
        $country = $examType->country;
        
        $seoTitle = $examType->seo_title ?: "نتيجة الثانوية العامة 2026 برقم الجلوس والاسم - نتيجتي";
        $seoDesc = $examType->seo_description ?: "رابط نتيجة الثانوية العامة 2026 في مصر فور صدورها. استعلم عن نتيجتك برقم الجلوس والاسم مجاناً، وتعرف على درجات المواد، النسبة المئوية، وترتيب الأوائل.";
        
        $faqs = [
            [
                'question' => 'متى تظهر نتيجة الثانوية العامة 2026 في مصر؟',
                'answer' => 'تظهر نتيجة الثانوية العامة 2026 بعد انتهاء أعمال التصحيح ورصد درجات الطلاب إلكترونياً واعتمادها رسمياً من وزير التربية والتعليم والتعليم الفني في المؤتمر الصحفي، ومن المتوقع إعلان النتيجة في أواخر شهر يوليو أو مطلع أغسطس 2026.'
            ],
            [
                'question' => 'كيف يمكنني الحصول على نتيجة الثانوية العامة 2026 برقم الجلوس والاسم؟',
                'answer' => 'يمكنك الاستعلام الفوري عن النتيجة عبر منصة نتيجتي بالخطوات التالية: 1. الدخول على صفحة نتيجة الثانوية العامة في مصر. 2. كتابة رقم الجلوس الخاص بك في خانة البحث. 3. الضغط على زر "بحث عن النتيجة" لتظهر لك الدرجات بالتفصيل مع النسبة المئوية والمجموع الكلي.'
            ],
            [
                'question' => 'ما هو المجموع الكلي لدرجات الثانوية العامة 2026؟',
                'answer' => 'المجموع الكلي لدرجات الثانوية العامة المصرية هو 410 درجات موزعة على المواد الأساسية للشعبتين العلمية والأدبية، وحد النجاح (درجة المرور) هو 50% من درجة كل مادة بشرط حضور الطالب للامتحان.'
            ],
            [
                'question' => 'كيف يتم حساب النسبة المئوية للثانوية العامة؟',
                'answer' => 'يتم حساب النسبة المئوية عن طريق قسمة المجموع الذي حصل عليه الطالب على المجموع الكلي (410) ثم ضرب الناتج في 100. على سبيل المثال، إذا حصل الطالب على مجموع 369 درجة، تكون النسبة المئوية: (369 / 410) * 100 = 90%.'
            ],
            [
                'question' => 'ما هي خطوات تقديم تظلم على نتيجة الثانوية العامة؟',
                'answer' => 'بعد إعلان النتيجة، تفتح وزارة التربية والتعليم باب التظلمات إلكترونياً لمدة 15 يوماً. يقوم الطالب بدفع الرسوم المقررة لكل مادة عبر منافذ الدفع المعتمدة، ثم يحدد موعداً للاطلاع على صورة من ورقة الإجابة (البابل شيت) وكتابة ملاحظاته في مقر الكنترول الرئيسي.'
            ]
        ];

        $faqSchema = [
            '@type' => 'FAQPage',
            'mainEntity' => array_map(function($faq) {
                return [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq['answer']
                    ]
                ];
            }, $faqs)
        ];

        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                self::educationalCredential($examType),
                self::webPage(
                    $seoTitle,
                    $seoDesc,
                    request()->url(),
                    $examType->created_at?->toIso8601String(),
                    $examType->updated_at?->toIso8601String()
                ),
                self::breadcrumb([
                    ['name' => 'الرئيسية', 'url' => $siteUrl],
                    ['name' => $country->name_ar, 'url' => url('/egypt')],
                    ['name' => $examType->name_ar],
                ]),
                $faqSchema
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
                self::webPage($title, $description, null, $governorate->created_at?->toIso8601String(), $governorate->updated_at?->toIso8601String()),
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
                self::webPage($title, "نتائج طلاب {$examTypeName} في محافظة {$governorate->name_ar}", null, $governorate->created_at?->toIso8601String(), $governorate->updated_at?->toIso8601String()),
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
                self::webPage($title, "نتيجة الطالب {$result->student_name} في {$examType->name_ar} - رقم الجلوس: {$result->seat_number}", null, $result->created_at?->toIso8601String(), $result->updated_at?->toIso8601String()),
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
                self::webPage(
                    'تصميم شهادة تقدير للمتفوقين مجاناً 2026',
                    'أداة مجانية لتصميم شهادة تقدير احترافية للطلاب المتفوقين في مصر والعراق وليبيا وجميع الدول العربية 2026'
                ),
                self::breadcrumb([
                    ['name' => 'الرئيسية', 'url' => $siteUrl],
                    ['name' => 'شهادة تقدير', 'url' => $siteUrl . '/certificate'],
                ]),
                [
                    '@type'            => 'SoftwareApplication',
                    '@id'              => $siteUrl . '/certificate#app',
                    'name'             => 'مصمم شهادة التقدير - نتيجتي',
                    'applicationCategory' => 'EducationalApplication',
                    'operatingSystem'  => 'Web',
                    'url'              => $siteUrl . '/certificate',
                    'offers'           => [
                        '@type'        => 'Offer',
                        'price'        => '0',
                        'priceCurrency' => 'EGP',
                    ],
                    'description'      => 'أداة مجانية لتصميم وطباعة شهادة تقدير احترافية للطلاب المتفوقين في جميع الدول العربية',
                    'inLanguage'       => 'ar',
                    'publisher'        => self::organization(),
                ],
                [
                    '@type'  => 'HowTo',
                    '@id'    => $siteUrl . '/certificate#howto',
                    'name'   => 'كيفية تصميم شهادة تقدير مجانية للطالب المتفوق',
                    'description' => 'اتبع هذه الخطوات البسيطة لتصميم شهادة تقدير احترافية في ثوانٍ معدودة',
                    'totalTime'  => 'PT2M',
                    'step' => [
                        [
                            '@type'  => 'HowToStep',
                            'name'   => 'أدخل اسم الطالب',
                            'text'   => 'اكتب اسم الطالب كاملاً في الخانة المخصصة',
                            'position' => 1,
                        ],
                        [
                            '@type'  => 'HowToStep',
                            'name'   => 'أضف اسم المدرسة ونوع الامتحان',
                            'text'   => 'اكتب اسم المدرسة ونوع الشهادة مثل الشهادة الإعدادية 2026',
                            'position' => 2,
                        ],
                        [
                            '@type'  => 'HowToStep',
                            'name'   => 'أدخل المجموع والنسبة',
                            'text'   => 'أضف المجموع الكلي والنسبة المئوية للطالب',
                            'position' => 3,
                        ],
                        [
                            '@type'  => 'HowToStep',
                            'name'   => 'حمّل أو اطبع الشهادة',
                            'text'   => 'اضغط تحميل الشهادة لحفظها أو اطبعها مباشرة',
                            'position' => 4,
                        ],
                    ],
                ],
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
                    'datePublished' => now()->toIso8601String(),
                    'dateModified' => now()->toIso8601String(),
                ],
                self::breadcrumb([
                    ['name' => 'الرئيسية', 'url' => $siteUrl],
                    ['name' => 'اتصل بنا', 'url' => $siteUrl . '/contact'],
                ]),
            ],
        ];
        
        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Generate NewsArticle/BlogPosting schema for a blog article
     */
    public static function blogPostPage(\App\Models\Post $post): string
    {
        $siteUrl = config('app.url');
        $siteName = \App\Models\SiteSetting::get('site_name', 'نتيجتي');
        $imageUrl = $post->image_path ? asset($post->image_path) : url('/images/og-default.png');

        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'NewsArticle',
                    '@id' => url()->current() . '#article',
                    'isPartOf' => [
                        '@type' => 'WebPage',
                        '@id' => url()->current(),
                        'url' => url()->current(),
                        'name' => $post->seo_title ?? $post->title,
                    ],
                    'headline' => $post->title,
                    'description' => $post->seo_description ?? $post->summary,
                    'image' => [
                        '@type' => 'ImageObject',
                        'url' => $imageUrl,
                    ],
                    'datePublished' => $post->published_at?->toIso8601String() ?? $post->created_at->toIso8601String(),
                    'dateModified' => $post->updated_at->toIso8601String(),
                    'author' => [
                        '@type' => 'Organization',
                        'name' => $siteName,
                        'url' => $siteUrl,
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => $siteName,
                        'url' => $siteUrl,
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => url(\App\Models\SiteSetting::get('logo', 'uploads/settings/01KP46W4HWEWPQCRSK6ZE68C6G.png')),
                        ]
                    ],
                    'mainEntityOfPage' => url()->current(),
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => 'الرئيسية',
                            'item' => $siteUrl,
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => 'المدونة',
                            'item' => route('blog.index'),
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 3,
                            'name' => $post->title,
                            'item' => url()->current(),
                        ],
                    ],
                ]
            ],
        ];

        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
