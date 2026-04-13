<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class HomepageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'إعدادات الصفحة الرئيسية';
    protected static ?string $navigationGroup = 'إعدادات الموقع';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.homepage-settings';
    protected static ?string $title = 'إعدادات الصفحة الرئيسية';
    protected static ?string $slug = 'homepage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // SEO Meta Tags
            'seo_title' => SiteSetting::get('seo_title', 'نتائج الطلاب في الوطن العربي | نتيجتي'),
            'seo_description' => SiteSetting::get('seo_description', 'منصة نتيجتي لعرض نتائج الشهادات الإعدادية والثانوية والدبلومات في الوطن العربي'),
            'seo_keywords' => SiteSetting::get('seo_keywords', 'نتائج, امتحانات, شهادة إعدادية, شهادة ثانوية, دبلومات, مصر, الوطن العربي'),
            
            // Hero Section
            'hero_badge' => SiteSetting::get('hero_badge', 'بوابة نتائج الامتحانات الرسمية'),
            'hero_title' => SiteSetting::get('hero_title', 'نتيجتي'),
            'hero_subtitle' => SiteSetting::get('hero_subtitle', 'بوابتك الرسمية لنتائج الامتحانات في الوطن العربي'),
            
            // About Section
            'about_section_active' => SiteSetting::get('about_section_active', '1') === '1',
            'about_section_title' => SiteSetting::get('about_section_title', 'عن نتيجتي'),
            'about_section_content' => SiteSetting::get('about_section_content', $this->getDefaultAboutContent()),
            
            // Header Announcement Bar
            'header_announcement_active' => SiteSetting::get('header_announcement_active', '1') === '1',
            'header_announcement_text' => SiteSetting::get('header_announcement_text', 'حصرياً: نتائج الشهادات العامة فور اعتمادها! تابعونا لحظة بلحظة.'),
            
            // Footer
            'footer_about_title' => SiteSetting::get('footer_about_title', 'نتيجتي'),
            'footer_about_text' => SiteSetting::get('footer_about_text', 'منصة نتائج الطلاب الأولى في الوطن العربي. نوفر لك الوصول السريع والمجاني لنتائج الامتحانات في مصر والدول العربية.'),
            'footer_copyright' => SiteSetting::get('footer_copyright', 'نتيجتي - جميع الحقوق محفوظة'),
            'footer_slogan' => SiteSetting::get('footer_slogan', 'صنع بحب في الوطن العربي'),
            
            // Social Links
            'footer_facebook_url' => SiteSetting::get('footer_facebook_url', ''),
            'footer_telegram_url' => SiteSetting::get('footer_telegram_url', ''),
            'footer_whatsapp_url' => SiteSetting::get('footer_whatsapp_url', ''),
        ]);
    }

    protected function getDefaultAboutContent(): string
    {
        return 'موقع نتيجتي هو المنصة العربية الأكبر والأحدث المخصصة لعرض نتائج الشهادات العامة والأزهرية والدبلومات الفنية فور اعتمادها رسمياً. ننفرد بتغطية شاملة وحصرية لنتائج الامتحانات في مصر، العراق، ليبيا، فلسطين وغيرها من الدول. لا نكتفي بعرض النتيجة فحسب، بل نقدم أدوات ذكية تتيح لك البحث بالاسم أو رقم الجلوس، تصميم شهادات تقدير احترافية، وطباعة كشف الدرجات بضغطة زر. هدفنا هو توفير تجربة مستخدم سهلة، سريعة، وموثوقة لجميع الطلاب وأولياء الأمور.';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Section::make('إعدادات محركات البحث (SEO)')
                                    ->description('هذه الإعدادات تظهر في نتائج بحث جوجل ومحركات البحث الأخرى')
                                    ->icon('heroicon-o-globe-alt')
                                    ->schema([
                                        TextInput::make('seo_title')
                                            ->label('عنوان الصفحة (Title)')
                                            ->placeholder('نتائج الطلاب في الوطن العربي | نتيجتي')
                                            ->maxLength(70)
                                            ->helperText('يظهر في تاب المتصفح ونتائج البحث (يفضل 60-70 حرف)')
                                            ->columnSpanFull(),
                                            
                                        Textarea::make('seo_description')
                                            ->label('وصف الصفحة (Meta Description)')
                                            ->placeholder('منصة نتيجتي لعرض نتائج الشهادات الإعدادية والثانوية...')
                                            ->maxLength(160)
                                            ->rows(2)
                                            ->helperText('يظهر أسفل العنوان في نتائج البحث (يفضل 150-160 حرف)')
                                            ->columnSpanFull(),
                                            
                                        TextInput::make('seo_keywords')
                                            ->label('الكلمات المفتاحية (Keywords)')
                                            ->placeholder('نتائج, امتحانات, شهادة إعدادية, شهادة ثانوية...')
                                            ->maxLength(255)
                                            ->helperText('افصل بين الكلمات بفاصلة')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('العنوان الرئيسي')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                Section::make('عنوان الصفحة الرئيسية')
                                    ->description('العنوان الذي يظهر في أعلى الصفحة الرئيسية')
                                    ->icon('heroicon-o-home')
                                    ->schema([
                                        TextInput::make('hero_badge')
                                            ->label('نص الشريط الأسود العلوي')
                                            ->placeholder('بوابة نتائج الامتحانات الرسمية')
                                            ->maxLength(100)
                                            ->helperText('النص الذي يظهر في الشريط الأسود أعلى الصفحة بجانب التاريخ')
                                            ->columnSpanFull(),
                                        
                                        TextInput::make('hero_title')
                                            ->label('العنوان الرئيسي')
                                            ->placeholder('نتيجتي')
                                            ->maxLength(50)
                                            ->helperText('الاسم الرئيسي للموقع'),
                                            
                                        TextInput::make('hero_subtitle')
                                            ->label('الشعار / الوصف')
                                            ->placeholder('بوابتك الرسمية لنتائج الامتحانات في الوطن العربي')
                                            ->maxLength(200)
                                            ->columnSpanFull()
                                            ->helperText('يظهر بجانب العنوان الرئيسي'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('الهيدر')
                            ->icon('heroicon-o-bars-3')
                            ->schema([
                                Section::make('شريط الإعلانات العلوي')
                                    ->description('الشريط الملون الذي يظهر أعلى الصفحة')
                                    ->icon('heroicon-o-speaker-wave')
                                    ->schema([
                                        Toggle::make('header_announcement_active')
                                            ->label('تفعيل شريط الإعلانات')
                                            ->helperText('إظهار/إخفاء الشريط العلوي'),
                                            
                                        TextInput::make('header_announcement_text')
                                            ->label('نص الإعلان')
                                            ->placeholder('حصرياً: نتائج الشهادات العامة فور اعتمادها!')
                                            ->maxLength(200)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('عن الموقع')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('قسم "عن الموقع"')
                                    ->description('يظهر في أسفل الصفحة الرئيسية قبل الفوتر')
                                    ->schema([
                                        Toggle::make('about_section_active')
                                            ->label('تفعيل القسم')
                                            ->helperText('إظهار/إخفاء قسم "عن الموقع"'),
                                            
                                        TextInput::make('about_section_title')
                                            ->label('عنوان القسم')
                                            ->placeholder('عن نتيجتي')
                                            ->maxLength(100),
                                            
                                        Textarea::make('about_section_content')
                                            ->label('محتوى القسم')
                                            ->placeholder('اكتب نبذة عن الموقع...')
                                            ->rows(6)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('الفوتر')
                            ->icon('heroicon-o-bars-3-bottom-right')
                            ->schema([
                                Section::make('معلومات الفوتر')
                                    ->description('المحتوى الذي يظهر في أسفل كل صفحة')
                                    ->schema([
                                        TextInput::make('footer_about_title')
                                            ->label('عنوان قسم "عن الموقع"')
                                            ->placeholder('نتيجتي')
                                            ->maxLength(50),
                                            
                                        Textarea::make('footer_about_text')
                                            ->label('نص "عن الموقع" في الفوتر')
                                            ->placeholder('منصة نتائج الطلاب الأولى...')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                            
                                        TextInput::make('footer_copyright')
                                            ->label('نص حقوق النشر')
                                            ->placeholder('نتيجتي - جميع الحقوق محفوظة')
                                            ->maxLength(100),
                                            
                                        TextInput::make('footer_slogan')
                                            ->label('الشعار السفلي')
                                            ->placeholder('صنع بحب في الوطن العربي')
                                            ->maxLength(100),
                                    ])->columns(2),

                                Section::make('روابط التواصل الاجتماعي (الفوتر)')
                                    ->description('روابط حسابات التواصل الاجتماعي في الفوتر')
                                    ->schema([
                                        TextInput::make('footer_facebook_url')
                                            ->label('رابط صفحة فيسبوك')
                                            ->placeholder('https://facebook.com/yourpage')
                                            ->url(),
                                            
                                        TextInput::make('footer_telegram_url')
                                            ->label('رابط قناة تيليجرام')
                                            ->placeholder('https://t.me/yourchannel')
                                            ->url(),
                                            
                                        TextInput::make('footer_whatsapp_url')
                                            ->label('رابط واتساب')
                                            ->placeholder('https://wa.me/201234567890')
                                            ->url(),
                                    ])->columns(3),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // SEO Meta Tags
        SiteSetting::set('seo_title', $data['seo_title'] ?? 'نتائج الطلاب في الوطن العربي | نتيجتي', 'seo', 'text', 'عنوان الصفحة');
        SiteSetting::set('seo_description', $data['seo_description'] ?? '', 'seo', 'text', 'وصف الصفحة');
        SiteSetting::set('seo_keywords', $data['seo_keywords'] ?? '', 'seo', 'text', 'الكلمات المفتاحية');

        // Hero Section
        SiteSetting::set('hero_badge', $data['hero_badge'] ?? 'بوابة نتائج الامتحانات الرسمية', 'homepage', 'text', 'النص العلوي');
        SiteSetting::set('hero_title', $data['hero_title'] ?? 'نتيجتي', 'homepage', 'text', 'العنوان الرئيسي');
        SiteSetting::set('hero_subtitle', $data['hero_subtitle'] ?? '', 'homepage', 'text', 'الشعار');
        
        // About Section
        SiteSetting::set('about_section_active', ($data['about_section_active'] ?? false) ? '1' : '0', 'homepage', 'boolean', 'تفعيل قسم عن الموقع');
        SiteSetting::set('about_section_title', $data['about_section_title'] ?? 'عن نتيجتي', 'homepage', 'text', 'عنوان قسم عن الموقع');
        SiteSetting::set('about_section_content', $data['about_section_content'] ?? '', 'homepage', 'textarea', 'محتوى قسم عن الموقع');
        
        // Header Announcement
        SiteSetting::set('header_announcement_active', ($data['header_announcement_active'] ?? false) ? '1' : '0', 'header', 'boolean', 'تفعيل شريط الإعلانات');
        SiteSetting::set('header_announcement_text', $data['header_announcement_text'] ?? '', 'header', 'text', 'نص شريط الإعلانات');
        
        // Footer
        SiteSetting::set('footer_about_title', $data['footer_about_title'] ?? 'نتيجتي', 'footer', 'text', 'عنوان الفوتر');
        SiteSetting::set('footer_about_text', $data['footer_about_text'] ?? '', 'footer', 'textarea', 'نص الفوتر');
        SiteSetting::set('footer_copyright', $data['footer_copyright'] ?? '', 'footer', 'text', 'حقوق النشر');
        SiteSetting::set('footer_slogan', $data['footer_slogan'] ?? '', 'footer', 'text', 'الشعار السفلي');
        
        // Social Links
        SiteSetting::set('footer_facebook_url', $data['footer_facebook_url'] ?? '', 'footer', 'url', 'رابط فيسبوك');
        SiteSetting::set('footer_telegram_url', $data['footer_telegram_url'] ?? '', 'footer', 'url', 'رابط تيليجرام');
        SiteSetting::set('footer_whatsapp_url', $data['footer_whatsapp_url'] ?? '', 'footer', 'url', 'رابط واتساب');

        SiteSetting::clearCache();

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }
}
