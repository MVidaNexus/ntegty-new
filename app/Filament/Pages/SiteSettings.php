<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Components\FileUpload;
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

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?string $navigationLabel = 'أكواد التتبع والإعلانات';
    protected static ?string $navigationGroup = 'إعدادات الموقع';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.site-settings';
    protected static ?string $title = 'أكواد التتبع والإعلانات';
    protected static ?string $slug = 'tracking-codes';

    public ?array $data = [];

    public function mount(): void
    {
        // Read ads.txt content from file
        $adsContent = '';
        $adsPath = public_path('ads.txt');
        if (file_exists($adsPath)) {
            $adsContent = file_get_contents($adsPath);
        }
        
        // Default robots.txt content
        $defaultRobots = "User-agent: *\nDisallow: /admin\nDisallow: /nova\nDisallow: /dashboard\nAllow: /\n\nSitemap: " . url('/sitemap.xml');
        
        $this->form->fill([
            'header_scripts' => SiteSetting::get('header_scripts', ''),
            'footer_scripts' => SiteSetting::get('footer_scripts', ''),
            'google_analytics_id' => SiteSetting::get('google_analytics_id', ''),
            'meta_verification' => SiteSetting::get('meta_verification', ''),
            'adsense_code' => SiteSetting::get('adsense_code', ''),
            'custom_css' => SiteSetting::get('custom_css', ''),
            'maintenance_mode' => SiteSetting::get('maintenance_mode', '0') === '1',
            'maintenance_message' => SiteSetting::get('maintenance_message', 'الموقع تحت الصيانة، يرجى العودة لاحقاً'),
            'ads_txt_content' => $adsContent,
            'robots_txt_content' => SiteSetting::get('robots_txt', $defaultRobots),
            'og_image' => SiteSetting::get('og_image', ''),
            'fb_app_id' => SiteSetting::get('fb_app_id', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('أكواد التتبع')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Section::make('Google Analytics')
                                    ->description('أضف معرف Google Analytics لتتبع زوار الموقع')
                                    ->schema([
                                        TextInput::make('google_analytics_id')
                                            ->label('Google Analytics ID')
                                            ->placeholder('G-XXXXXXXXXX أو UA-XXXXXXXXX-X')
                                            ->helperText('مثال: G-ABC123XYZ'),
                                    ]),
                                    
                                Section::make('أكواد التحقق')
                                    ->description('أكواد التحقق من ملكية الموقع')
                                    ->schema([
                                        Textarea::make('meta_verification')
                                            ->label('Meta Tags للتحقق')
                                            ->placeholder('<meta name="google-site-verification" content="..." />')
                                            ->helperText('أضف meta tags للتحقق من Google Search Console, Bing, إلخ')
                                            ->rows(3),
                                    ]),
                            ]),

                        Tabs\Tab::make('أكواد مخصصة')
                            ->icon('heroicon-o-code-bracket')
                            ->schema([
                                Section::make('كود الهيدر (Head)')
                                    ->description('أكواد تضاف داخل <head> - مثل: تتبع، خطوط، أكواد إعلانات')
                                    ->schema([
                                        Textarea::make('header_scripts')
                                            ->label('أكواد Header')
                                            ->placeholder('<!-- أضف أكواد JavaScript, CSS, Meta tags -->')
                                            ->rows(8)
                                            ->columnSpanFull(),
                                    ]),
                                    
                                Section::make('كود الفوتر (Body End)')
                                    ->description('أكواد تضاف قبل إغلاق </body> - مثل: سكربتات، chat widgets')
                                    ->schema([
                                        Textarea::make('footer_scripts')
                                            ->label('أكواد Footer')
                                            ->placeholder('<!-- أضف أكواد JavaScript -->')
                                            ->rows(8)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('الإعلانات')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                Section::make('Google AdSense')
                                    ->description('كود AdSense الرئيسي')
                                    ->schema([
                                        Textarea::make('adsense_code')
                                            ->label('كود AdSense')
                                            ->placeholder('<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXXXXXX"></script>')
                                            ->rows(5)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('ملف ads.txt')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('إعدادات ads.txt')
                                    ->description('ملف ads.txt يُستخدم للتحقق من البائعين المعتمدين للإعلانات على موقعك')
                                    ->schema([
                                        Textarea::make('ads_txt_content')
                                            ->label('محتوى ملف ads.txt')
                                            ->placeholder('google.com, pub-0000000000000000, DIRECT, f08c47fec0942fa0')
                                            ->helperText('أضف كل سطر من ملف ads.txt الخاص بك. سيتم حفظه في /ads.txt')
                                            ->rows(12)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('ملف robots.txt')
                            ->icon('heroicon-o-cpu-chip')
                            ->schema([
                                Section::make('إعدادات robots.txt')
                                    ->description('ملف robots.txt يتحكم في كيفية زحف محركات البحث لموقعك')
                                    ->schema([
                                        Textarea::make('robots_txt_content')
                                            ->label('محتوى ملف robots.txt')
                                            ->placeholder("User-agent: *\nDisallow: /admin\nAllow: /\n\nSitemap: https://ntegty.com/sitemap.xml")
                                            ->helperText('تحكم في الصفحات التي يمكن لمحركات البحث الوصول إليها')
                                            ->rows(15)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('السوشيال ميديا')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Section::make('صورة المشاركة الافتراضية (OG Image)')
                                    ->description('الصورة التي تظهر عند مشاركة الموقع على فيسبوك وتويتر وغيرها')
                                    ->schema([
                                        FileUpload::make('og_image')
                                            ->label('صورة OG الافتراضية')
                                            ->image()
                                            ->directory('images')
                                            ->disk('public_root')
                                            ->visibility('public')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('1200:630')
                                            ->imageResizeTargetWidth('1200')
                                            ->imageResizeTargetHeight('630')
                                            ->helperText('الأبعاد المثالية: 1200×630 بكسل. ستظهر هذه الصورة عند مشاركة روابط الموقع.')
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Facebook App ID')
                                    ->description('معرف تطبيق فيسبوك للتكامل مع منصة فيسبوك')
                                    ->schema([
                                        TextInput::make('fb_app_id')
                                            ->label('Facebook App ID')
                                            ->placeholder('123456789012345')
                                            ->helperText('يمكنك الحصول عليه من developers.facebook.com')
                                            ->maxLength(20),
                                    ]),
                            ]),

                        Tabs\Tab::make('التخصيص')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make('CSS مخصص')
                                    ->description('أضف CSS مخصص للموقع')
                                    ->schema([
                                        Textarea::make('custom_css')
                                            ->label('Custom CSS')
                                            ->placeholder('/* أضف CSS مخصص */')
                                            ->rows(10)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('الصيانة')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->schema([
                                Section::make('وضع الصيانة')
                                    ->description('تفعيل وضع الصيانة يمنع الزوار من تصفح الموقع')
                                    ->schema([
                                        Toggle::make('maintenance_mode')
                                            ->label('تفعيل وضع الصيانة')
                                            ->helperText('عند التفعيل، سيظهر للزوار رسالة الصيانة'),
                                        Textarea::make('maintenance_message')
                                            ->label('رسالة الصيانة')
                                            ->rows(3),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::set('header_scripts', $data['header_scripts'] ?? '', 'scripts', 'code', 'أكواد Header');
        SiteSetting::set('footer_scripts', $data['footer_scripts'] ?? '', 'scripts', 'code', 'أكواد Footer');
        SiteSetting::set('google_analytics_id', $data['google_analytics_id'] ?? '', 'analytics', 'text', 'Google Analytics ID');
        SiteSetting::set('meta_verification', $data['meta_verification'] ?? '', 'seo', 'code', 'Meta Verification');
        SiteSetting::set('adsense_code', $data['adsense_code'] ?? '', 'ads', 'code', 'AdSense Code');
        SiteSetting::set('custom_css', $data['custom_css'] ?? '', 'design', 'code', 'Custom CSS');
        SiteSetting::set('maintenance_mode', ($data['maintenance_mode'] ?? false) ? '1' : '0', 'system', 'boolean', 'Maintenance Mode');
        SiteSetting::set('maintenance_message', $data['maintenance_message'] ?? '', 'system', 'textarea', 'Maintenance Message');

        // Save ads.txt file
        $adsContent = $data['ads_txt_content'] ?? '';
        $adsPath = public_path('ads.txt');
        if (!empty(trim($adsContent))) {
            file_put_contents($adsPath, $adsContent);
        } elseif (file_exists($adsPath)) {
            // If content is empty and file exists, delete it
            unlink($adsPath);
        }

        // Save robots.txt content to database
        SiteSetting::set('robots_txt', $data['robots_txt_content'] ?? '', 'seo', 'code', 'Robots.txt Content');

        // Save OG image
        $ogImage = $data['og_image'] ?? '';
        SiteSetting::set('og_image', $ogImage, 'social', 'image', 'OG Image');

        // Save Facebook App ID
        SiteSetting::set('fb_app_id', $data['fb_app_id'] ?? '', 'social', 'text', 'Facebook App ID');

        SiteSetting::clearCache();

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }
}
