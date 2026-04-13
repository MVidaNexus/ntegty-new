<?php

namespace App\Filament\Pages;

use App\Models\SitemapSetting;
use App\Models\SitemapLog;
use App\Services\SitemapService;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;

class SitemapManager extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'خرائط الموقع';
    protected static ?string $navigationGroup = 'إعدادات الموقع';
    protected static ?int $navigationSort = 10;
    protected static ?string $title = 'إدارة خرائط الموقع XML';
    protected static ?string $slug = 'sitemap-manager';
    protected static string $view = 'filament.pages.sitemap-manager';

    public ?array $data = [];
    public array $statistics = [];
    public array $sitemaps = [];

    public function mount(): void
    {
        $settings = SitemapSetting::getSettings();
        
        $this->form->fill($settings?->toArray() ?? [
            'is_enabled' => true,
            'auto_generate' => true,
            'urls_per_sitemap' => 5000,
            'cache_hours' => 6,
            'include_pages' => true,
            'include_countries' => true,
            'include_exam_types' => true,
            'include_governorates' => true,
            'include_branches' => true,
            'include_students' => true,
            'include_schools' => true,
            'include_administrations' => true,
            'include_top_students' => true,
        ]);
        
        $this->loadStatistics();
    }

    protected function loadStatistics(): void
    {
        try {
            $service = new SitemapService();
            $this->statistics = $service->getStatistics();
            $this->sitemaps = $service->getSitemapsBreakdown();
        } catch (\Exception $e) {
            $this->statistics = [];
            $this->sitemaps = [];
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('إعدادات خرائط الموقع')
                    ->tabs([
                        // تبويب الإعدادات الأساسية
                        Forms\Components\Tabs\Tab::make('الإعدادات الأساسية')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Section::make('التفعيل والتوليد')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_enabled')
                                            ->label('تفعيل خرائط الموقع')
                                            ->helperText('تفعيل/تعطيل خرائط الموقع بالكامل')
                                            ->default(true),
                                        Forms\Components\Toggle::make('auto_generate')
                                            ->label('التوليد التلقائي')
                                            ->helperText('توليد الخرائط تلقائياً عند إضافة نتائج جديدة')
                                            ->default(true),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('إعدادات الأداء')
                                    ->schema([
                                        Forms\Components\TextInput::make('urls_per_sitemap')
                                            ->label('عدد الروابط لكل خريطة')
                                            ->numeric()
                                            ->minValue(100)
                                            ->maxValue(50000)
                                            ->default(5000)
                                            ->helperText('الحد الأقصى من Google هو 50,000 - يُنصح بـ 5000 للأداء الأفضل')
                                            ->suffix('رابط'),
                                        Forms\Components\TextInput::make('cache_hours')
                                            ->label('مدة التخزين المؤقت')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(168)
                                            ->default(6)
                                            ->helperText('كم ساعة يتم حفظ الخريطة في الكاش قبل إعادة توليدها')
                                            ->suffix('ساعة'),
                                    ])
                                    ->columns(2),
                            ]),

                        // تبويب الأقسام المضمنة
                        Forms\Components\Tabs\Tab::make('الأقسام المضمنة')
                            ->icon('heroicon-o-squares-plus')
                            ->schema([
                                Forms\Components\Section::make('اختر الأقسام التي تريد تضمينها في خرائط الموقع')
                                    ->schema([
                                        Forms\Components\Toggle::make('include_pages')
                                            ->label('الصفحات الثابتة')
                                            ->helperText('الرئيسية، الشهادة، اتصل بنا، الخصوصية، الشروط')
                                            ->default(true),
                                        Forms\Components\Toggle::make('include_countries')
                                            ->label('الدول')
                                            ->helperText('جميع الدول المفعلة')
                                            ->default(true),
                                        Forms\Components\Toggle::make('include_exam_types')
                                            ->label('أنواع الشهادات')
                                            ->helperText('الإعدادية، الثانوية، الدبلومات، إلخ')
                                            ->default(true),
                                        Forms\Components\Toggle::make('include_governorates')
                                            ->label('المحافظات')
                                            ->helperText('جميع المحافظات لكل دولة')
                                            ->default(true),
                                        Forms\Components\Toggle::make('include_branches')
                                            ->label('الشعب والفروع')
                                            ->helperText('علمي، أدبي، إلخ')
                                            ->default(true),
                                        Forms\Components\Toggle::make('include_students')
                                            ->label('نتائج الطلاب')
                                            ->helperText('صفحات جميع نتائج الطلاب')
                                            ->default(true),
                                        Forms\Components\Toggle::make('include_schools')
                                            ->label('المدارس')
                                            ->helperText('صفحات المدارس')
                                            ->default(true),
                                        Forms\Components\Toggle::make('include_administrations')
                                            ->label('الإدارات التعليمية')
                                            ->helperText('صفحات الإدارات')
                                            ->default(true),
                                        Forms\Components\Toggle::make('include_top_students')
                                            ->label('أوائل الطلبة')
                                            ->helperText('صفحات أوائل الطلبة')
                                            ->default(true),
                                    ])
                                    ->columns(3),
                            ]),

                        // تبويب الأولويات
                        Forms\Components\Tabs\Tab::make('الأولويات')
                            ->icon('heroicon-o-arrow-trending-up')
                            ->schema([
                                Forms\Components\Section::make('أولوية كل قسم (من 0.0 إلى 1.0)')
                                    ->description('الأولوية تخبر محركات البحث بأهمية كل صفحة نسبياً')
                                    ->schema([
                                        Forms\Components\TextInput::make('priority_home')
                                            ->label('الصفحة الرئيسية')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(1)
                                            ->step(0.1)
                                            ->default(1.0),
                                        Forms\Components\TextInput::make('priority_countries')
                                            ->label('الدول')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(1)
                                            ->step(0.1)
                                            ->default(0.9),
                                        Forms\Components\TextInput::make('priority_exam_types')
                                            ->label('أنواع الشهادات')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(1)
                                            ->step(0.1)
                                            ->default(0.85),
                                        Forms\Components\TextInput::make('priority_governorates')
                                            ->label('المحافظات')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(1)
                                            ->step(0.1)
                                            ->default(0.8),
                                        Forms\Components\TextInput::make('priority_students')
                                            ->label('نتائج الطلاب')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(1)
                                            ->step(0.1)
                                            ->default(0.7),
                                        Forms\Components\TextInput::make('priority_schools')
                                            ->label('المدارس')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(1)
                                            ->step(0.1)
                                            ->default(0.6),
                                    ])
                                    ->columns(3),

                                Forms\Components\Section::make('تردد التحديث')
                                    ->schema([
                                        Forms\Components\Select::make('changefreq_home')
                                            ->label('الصفحة الرئيسية')
                                            ->options(SitemapSetting::getChangefreqOptions())
                                            ->default('daily'),
                                        Forms\Components\Select::make('changefreq_countries')
                                            ->label('الدول والشهادات')
                                            ->options(SitemapSetting::getChangefreqOptions())
                                            ->default('daily'),
                                        Forms\Components\Select::make('changefreq_students')
                                            ->label('نتائج الطلاب')
                                            ->options(SitemapSetting::getChangefreqOptions())
                                            ->default('weekly'),
                                    ])
                                    ->columns(3),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $settings = SitemapSetting::first();
        
        if ($settings) {
            $settings->update($data);
        } else {
            SitemapSetting::create($data);
        }

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
        
        $this->loadStatistics();
    }

    public function regenerateSitemaps(): void
    {
        $service = new SitemapService();
        $result = $service->regenerateAll();
        
        if ($result['success']) {
            Notification::make()
                ->title('تم إعادة توليد الخرائط')
                ->body($result['message'] . ' - ' . $result['time'])
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('فشل في توليد الخرائط')
                ->body($result['message'])
                ->danger()
                ->send();
        }
        
        $this->loadStatistics();
    }

    public function clearCache(): void
    {
        SitemapSetting::clearSitemapCache();
        Cache::forget('sitemap:statistics');
        
        Notification::make()
            ->title('تم مسح الكاش')
            ->body('سيتم إعادة توليد الخرائط عند الطلب التالي')
            ->success()
            ->send();
        
        $this->loadStatistics();
    }

    public function pingSearchEngines(): void
    {
        $sitemapUrl = url('/sitemap.xml');
        $results = [];
        
        // Google
        $googleUrl = "https://www.google.com/ping?sitemap=" . urlencode($sitemapUrl);
        $results['google'] = @file_get_contents($googleUrl) !== false;
        
        // Bing
        $bingUrl = "https://www.bing.com/ping?sitemap=" . urlencode($sitemapUrl);
        $results['bing'] = @file_get_contents($bingUrl) !== false;
        
        $successCount = count(array_filter($results));
        
        if ($successCount > 0) {
            $settings = SitemapSetting::first();
            $settings?->update(['last_submitted_at' => now()]);
            
            Notification::make()
                ->title('تم إرسال الخريطة لمحركات البحث')
                ->body("Google: " . ($results['google'] ? '✓' : '✗') . " | Bing: " . ($results['bing'] ? '✓' : '✗'))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('فشل في إرسال الخريطة')
                ->body('تعذر الاتصال بمحركات البحث')
                ->warning()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('regenerate')
                ->label('إعادة توليد الخرائط')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('إعادة توليد خرائط الموقع')
                ->modalDescription('سيتم مسح الكاش وإعادة توليد جميع خرائط الموقع. قد يستغرق هذا بعض الوقت.')
                ->action(fn () => $this->regenerateSitemaps()),
            
            Action::make('clearCache')
                ->label('مسح الكاش')
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->action(fn () => $this->clearCache()),
            
            Action::make('pingEngines')
                ->label('إرسال لمحركات البحث')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->action(fn () => $this->pingSearchEngines()),
            
            Action::make('viewSitemap')
                ->label('عرض الخريطة')
                ->icon('heroicon-o-eye')
                ->url(url('/sitemap.xml'))
                ->openUrlInNewTab(),
        ];
    }
}
