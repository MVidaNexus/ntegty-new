<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\CacheService;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class CacheManagement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';
    protected static ?string $navigationLabel = 'إدارة الكاش';
    protected static ?string $title = 'إدارة الكاش (Redis)';
    protected static ?string $navigationGroup = 'إعدادات الموقع';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'cache-management';

    protected static string $view = 'filament.pages.cache-management';

    public array $stats = [];
    public array $categories = [];
    public array $keys = [];
    public ?string $selectedCategory = null;

    // Cache settings form data
    public ?array $cacheSettings = [];

    /**
     * Default cache durations (in seconds)
     */
    public static array $defaultDurations = [
        'cache_enabled' => true,
        'cache_home' => 600,           // 10 minutes
        'cache_index' => 600,          // 10 minutes
        'cache_preparatory' => 600,    // 10 minutes
        'cache_secondary' => 600,      // 10 minutes
        'cache_diplomas' => 600,       // 10 minutes
        'cache_governorate' => 300,    // 5 minutes
        'cache_all_results' => 300,    // 5 minutes
        'cache_result' => 1800,        // 30 minutes
        'cache_default' => 300,        // 5 minutes
    ];

    /**
     * Duration options for select
     */
    public static array $durationOptions = [
        0 => 'بدون كاش',
        60 => 'دقيقة واحدة',
        120 => 'دقيقتان',
        180 => '3 دقائق',
        300 => '5 دقائق',
        600 => '10 دقائق',
        900 => '15 دقيقة',
        1200 => '20 دقيقة',
        1800 => '30 دقيقة',
        3600 => 'ساعة واحدة',
        7200 => 'ساعتان',
        10800 => '3 ساعات',
        21600 => '6 ساعات',
        43200 => '12 ساعة',
        86400 => 'يوم واحد',
        172800 => 'يومان',
        259200 => '3 أيام',
        604800 => 'أسبوع',
        'custom' => '📅 مدة مخصصة بالأيام...',
    ];

    /**
     * Helper to create duration field with custom option
     */
    protected function createDurationField(string $name, string $label, int $default): array
    {
        return [
            Select::make($name)
                ->label($label)
                ->options(self::$durationOptions)
                ->default($default)
                ->live()
                ->afterStateUpdated(fn ($state, $set) => $state !== 'custom' ? $set("{$name}_custom", null) : null)
                ->visible(fn ($get) => $get('cache_enabled')),
                
            TextInput::make("{$name}_custom")
                ->label('عدد الأيام')
                ->numeric()
                ->minValue(1)
                ->maxValue(30)
                ->suffix('يوم')
                ->placeholder('مثال: 7')
                ->visible(fn ($get) => $get('cache_enabled') && $get($name) === 'custom')
                ->helperText('💡 أدخل رقم الأيام: 1 = يوم واحد، 7 = أسبوع، 30 = شهر'),
        ];
    }

    public function mount(): void
    {
        $this->loadStats();
        $this->loadCacheSettings();
    }

    public function loadStats(): void
    {
        $this->stats = CacheService::getCacheStats();
        $this->categories = $this->stats['categories'] ?? [];
    }

    public function loadCacheSettings(): void
    {
        $this->cacheSettings = [];
        foreach (self::$defaultDurations as $key => $default) {
            $value = Setting::get($key, $default);
            
            // Convert to appropriate type
            if ($key === 'cache_enabled') {
                $this->cacheSettings[$key] = (bool) $value;
            } else {
                $intValue = (int) $value;
                // Check if value matches predefined options or is custom
                if (!array_key_exists($intValue, self::$durationOptions) && $intValue > 0) {
                    // It's a custom value (in seconds), convert to days for display
                    $this->cacheSettings[$key] = 'custom';
                    $this->cacheSettings[$key . '_custom'] = (int) ($intValue / 86400);
                } else {
                    $this->cacheSettings[$key] = $intValue;
                }
            }
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('إعدادات كاش الصفحات')
                    ->description('حدد مدة الكاش لكل نوع من الصفحات. اختر "مدة مخصصة" لإدخال عدد الأيام يدوياً.')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Toggle::make('cache_enabled')
                            ->label('تفعيل كاش الصفحات')
                            ->helperText('إيقاف هذا الخيار سيوقف كاش جميع الصفحات')
                            ->default(true)
                            ->live()
                            ->columnSpanFull(),

                        // Home
                        Select::make('cache_home')
                            ->label('🏠 الصفحة الرئيسية')
                            ->options(self::$durationOptions)
                            ->default(600)
                            ->live()
                            ->visible(fn ($get) => $get('cache_enabled')),
                        TextInput::make('cache_home_custom')
                            ->label('عدد الأيام')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->suffix('يوم')
                            ->helperText('1 = يوم، 7 = أسبوع، 30 = شهر')
                            ->visible(fn ($get) => $get('cache_enabled') && $get('cache_home') === 'custom'),

                        // Index
                        Select::make('cache_index')
                            ->label('🌍 صفحات الدول')
                            ->options(self::$durationOptions)
                            ->default(600)
                            ->live()
                            ->visible(fn ($get) => $get('cache_enabled')),
                        TextInput::make('cache_index_custom')
                            ->label('عدد الأيام')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->suffix('يوم')
                            ->helperText('1 = يوم، 7 = أسبوع، 30 = شهر')
                            ->visible(fn ($get) => $get('cache_enabled') && $get('cache_index') === 'custom'),

                        // Preparatory
                        Select::make('cache_preparatory')
                            ->label('📚 قائمة محافظات الإعدادية')
                            ->options(self::$durationOptions)
                            ->default(600)
                            ->live()
                            ->visible(fn ($get) => $get('cache_enabled')),
                        TextInput::make('cache_preparatory_custom')
                            ->label('عدد الأيام')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->suffix('يوم')
                            ->helperText('1 = يوم، 7 = أسبوع، 30 = شهر')
                            ->visible(fn ($get) => $get('cache_enabled') && $get('cache_preparatory') === 'custom'),

                        // Secondary
                        Select::make('cache_secondary')
                            ->label('🎓 صفحة الثانوية العامة')
                            ->options(self::$durationOptions)
                            ->default(600)
                            ->live()
                            ->visible(fn ($get) => $get('cache_enabled')),
                        TextInput::make('cache_secondary_custom')
                            ->label('عدد الأيام')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->suffix('يوم')
                            ->helperText('1 = يوم، 7 = أسبوع، 30 = شهر')
                            ->visible(fn ($get) => $get('cache_enabled') && $get('cache_secondary') === 'custom'),

                        // Diplomas
                        Select::make('cache_diplomas')
                            ->label('🔧 صفحة الدبلومات الفنية')
                            ->options(self::$durationOptions)
                            ->default(600)
                            ->live()
                            ->visible(fn ($get) => $get('cache_enabled')),
                        TextInput::make('cache_diplomas_custom')
                            ->label('عدد الأيام')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->suffix('يوم')
                            ->helperText('1 = يوم، 7 = أسبوع، 30 = شهر')
                            ->visible(fn ($get) => $get('cache_enabled') && $get('cache_diplomas') === 'custom'),

                        // Governorate
                        Select::make('cache_governorate')
                            ->label('🏛️ صفحة بحث المحافظة')
                            ->options(self::$durationOptions)
                            ->default(300)
                            ->live()
                            ->visible(fn ($get) => $get('cache_enabled')),
                        TextInput::make('cache_governorate_custom')
                            ->label('عدد الأيام')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->suffix('يوم')
                            ->helperText('1 = يوم، 7 = أسبوع، 30 = شهر')
                            ->visible(fn ($get) => $get('cache_enabled') && $get('cache_governorate') === 'custom'),

                        // All Results
                        Select::make('cache_all_results')
                            ->label('📋 صفحات جميع النتائج')
                            ->options(self::$durationOptions)
                            ->default(300)
                            ->live()
                            ->visible(fn ($get) => $get('cache_enabled')),
                        TextInput::make('cache_all_results_custom')
                            ->label('عدد الأيام')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->suffix('يوم')
                            ->helperText('1 = يوم، 7 = أسبوع، 30 = شهر')
                            ->visible(fn ($get) => $get('cache_enabled') && $get('cache_all_results') === 'custom'),

                        // Result
                        Select::make('cache_result')
                            ->label('👤 صفحة نتيجة طالب فردي')
                            ->options(self::$durationOptions)
                            ->default(1800)
                            ->live()
                            ->visible(fn ($get) => $get('cache_enabled')),
                        TextInput::make('cache_result_custom')
                            ->label('عدد الأيام')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->suffix('يوم')
                            ->helperText('1 = يوم، 7 = أسبوع، 30 = شهر')
                            ->visible(fn ($get) => $get('cache_enabled') && $get('cache_result') === 'custom'),

                        // Default
                        Select::make('cache_default')
                            ->label('⚙️ الصفحات الأخرى (افتراضي)')
                            ->options(self::$durationOptions)
                            ->default(300)
                            ->live()
                            ->visible(fn ($get) => $get('cache_enabled')),
                        TextInput::make('cache_default_custom')
                            ->label('عدد الأيام')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->suffix('يوم')
                            ->helperText('1 = يوم، 7 = أسبوع، 30 = شهر')
                            ->visible(fn ($get) => $get('cache_enabled') && $get('cache_default') === 'custom'),
                    ])
                    ->columns(2),
            ])
            ->statePath('cacheSettings');
    }

    public function saveCacheSettings(): void
    {
        $cacheFields = [
            'cache_home', 'cache_index', 'cache_preparatory', 'cache_secondary',
            'cache_diplomas', 'cache_governorate', 'cache_all_results', 
            'cache_result', 'cache_default'
        ];

        foreach ($this->cacheSettings as $key => $value) {
            // Skip custom fields - they are handled separately
            if (str_ends_with($key, '_custom')) {
                continue;
            }

            // Check if this is a cache duration field with custom value
            if (in_array($key, $cacheFields) && $value === 'custom') {
                $customDays = $this->cacheSettings[$key . '_custom'] ?? 1;
                // Convert days to seconds (1 day = 86400 seconds)
                $value = (int) $customDays * 86400;
            }

            Setting::set($key, $value, 'number', 'cache');
        }

        // Clear settings cache
        CacheService::invalidateSettings();
        
        // Clear page cache so new settings take effect
        CacheService::invalidatePageCache();

        Notification::make()
            ->title('تم حفظ إعدادات الكاش')
            ->body('سيتم تطبيق الإعدادات الجديدة على الطلبات القادمة')
            ->success()
            ->send();
    }

    public function toggleCache(): void
    {
        $currentState = (bool) Setting::get('cache_enabled', true);
        $newState = !$currentState;
        
        Setting::set('cache_enabled', $newState, 'boolean', 'cache');
        $this->cacheSettings['cache_enabled'] = $newState;
        
        // Clear settings cache
        CacheService::invalidateSettings();
        
        // If disabling, also clear page cache
        if (!$newState) {
            CacheService::invalidatePageCache();
        }

        Notification::make()
            ->title($newState ? 'تم تفعيل الكاش' : 'تم إيقاف الكاش')
            ->body($newState ? 'الموقع يعمل الآن بأقصى سرعة' : 'كل الطلبات ستذهب للداتابيز مباشرة')
            ->icon($newState ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
            ->iconColor($newState ? 'success' : 'danger')
            ->send();
    }

    public function resetCacheSettings(): void
    {
        foreach (self::$defaultDurations as $key => $value) {
            Setting::set($key, $value, 'number', 'cache');
        }

        $this->loadCacheSettings();
        CacheService::invalidateSettings();

        Notification::make()
            ->title('تم إعادة الإعدادات الافتراضية')
            ->success()
            ->send();
    }

    public function loadKeys(?string $category = null): void
    {
        $this->selectedCategory = $category;
        $this->keys = CacheService::getCachedKeys($category, 50);
    }

    public function clearAll(): void
    {
        $result = CacheService::clearAll();
        
        Notification::make()
            ->title($result['message'])
            ->success()
            ->send();
        
        $this->loadStats();
        $this->keys = [];
    }

    public function clearCategory(string $category): void
    {
        $methods = [
            'results' => fn() => CacheService::invalidateResults(0),
            'exam_types' => fn() => CacheService::invalidateExamTypes(),
            'countries' => fn() => CacheService::invalidateCountries(),
            'governorates' => fn() => CacheService::invalidateGovernorates(),
            'branches' => fn() => CacheService::invalidateBranches(),
            'settings' => fn() => CacheService::invalidateSettings(),
            'stats' => fn() => CacheService::invalidateStats(),
            'pages' => fn() => CacheService::invalidatePages(),
            'page_cache' => fn() => CacheService::invalidatePageCache(),
        ];

        if (isset($methods[$category])) {
            $prefix = match($category) {
                'results' => CacheService::PREFIX_RESULTS,
                'exam_types' => CacheService::PREFIX_EXAM_TYPES,
                'countries' => CacheService::PREFIX_COUNTRIES,
                'governorates' => CacheService::PREFIX_GOVERNORATES,
                'branches' => CacheService::PREFIX_BRANCHES,
                'settings' => CacheService::PREFIX_SETTINGS,
                'stats' => CacheService::PREFIX_STATS,
                'pages' => CacheService::PREFIX_PAGES,
                'page_cache' => CacheService::PREFIX_PAGE_CACHE,
                default => '',
            };
            
            $deleted = CacheService::deleteByPattern($prefix . '*');
            
            Notification::make()
                ->title("تم مسح كاش {$this->getCategoryLabel($category)}")
                ->body("تم حذف {$deleted} مفتاح")
                ->success()
                ->send();
        }
        
        $this->loadStats();
        $this->loadKeys($this->selectedCategory);
    }

    public function deleteKey(string $key): void
    {
        Cache::forget($key);
        
        Notification::make()
            ->title('تم حذف المفتاح')
            ->success()
            ->send();
        
        $this->loadStats();
        $this->loadKeys($this->selectedCategory);
    }

    public function warmUp(): void
    {
        $warmed = CacheService::warmUp();
        
        Notification::make()
            ->title('تم تحميل الصفحات للكاش')
            ->body('تم تخزين ' . count($warmed) . ' صفحة مؤقتاً')
            ->success()
            ->send();
        
        $this->loadStats();
    }

    public function clearLaravelCache(): void
    {
        \Artisan::call('optimize:clear');
        
        Notification::make()
            ->title('تم مسح كاش Laravel')
            ->body('تم مسح: config, route, view, event caches')
            ->success()
            ->send();
    }

    protected function getCategoryLabel(string $category): string
    {
        return match($category) {
            'results' => 'النتائج',
            'exam_types' => 'أنواع الشهادات',
            'countries' => 'الدول',
            'governorates' => 'المحافظات',
            'branches' => 'الشُعب',
            'settings' => 'الإعدادات',
            'stats' => 'الإحصائيات',
            'pages' => 'الصفحات',
            'page_cache' => 'كاش الصفحات (HTTP)',
            'other' => 'أخرى',
            default => $category,
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('warmUp')
                ->label('تحميل صفحات للكاش')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning')
                ->action('warmUp'),
            
            Action::make('clearLaravel')
                ->label('مسح كاش Laravel')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action('clearLaravelCache'),
            
            Action::make('clearAll')
                ->label('مسح كل الكاش')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('مسح كل الكاش')
                ->modalDescription('هل أنت متأكد؟ سيتم مسح جميع البيانات المخزنة مؤقتاً.')
                ->action('clearAll'),
        ];
    }

    public static function canAccess(): bool
    {
        return true;
    }
}
