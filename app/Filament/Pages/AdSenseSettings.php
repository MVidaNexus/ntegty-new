<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class AdSenseSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'إعدادات AdSense';
    protected static ?string $navigationGroup = 'إعدادات الإعلانات';
    protected static ?int $navigationSort = 100;
    protected static string $view = 'filament.pages.adsense-settings';
    protected static ?string $title = 'إعدادات حساب AdSense';
    protected static ?string $slug = 'adsense-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'adsense_publisher_id' => SiteSetting::get('adsense_publisher_id', ''),
            'adsense_enabled' => SiteSetting::get('adsense_enabled', '0') === '1',
            'default_channel' => SiteSetting::get('adsense_default_channel', ''),
            'load_adsense_script' => SiteSetting::get('load_adsense_script', '1') === '1',
            'adsense_auto_ads' => SiteSetting::get('adsense_auto_ads', '0') === '1',
            'disable_on_admin' => SiteSetting::get('disable_ads_on_admin', '1') === '1',
            'ads_delay_ms' => SiteSetting::get('ads_delay_ms', '0'),
            'adsense_custom_script' => SiteSetting::get('adsense_custom_script', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('معلومات حساب AdSense')
                    ->description('أدخل بيانات حساب Google AdSense الخاص بك')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Toggle::make('adsense_enabled')
                            ->label('تفعيل إعلانات AdSense')
                            ->helperText('عند التعطيل، لن تظهر أي إعلانات على الموقع')
                            ->live(),
                        
                        TextInput::make('adsense_publisher_id')
                            ->label('معرف الناشر (Publisher ID)')
                            ->placeholder('ca-pub-XXXXXXXXXXXXXXXX')
                            ->helperText('يبدأ بـ ca-pub- متبوعاً بـ 16 رقم')
                            ->prefixIcon('heroicon-o-key')
                            ->required()
                            ->regex('/^ca-pub-\d{16}$/')
                            ->validationMessages([
                                'regex' => 'معرف الناشر يجب أن يكون بالتنسيق: ca-pub-XXXXXXXXXXXXXXXX (16 رقم)',
                            ]),
                        
                        TextInput::make('default_channel')
                            ->label('القناة المخصصة الافتراضية')
                            ->placeholder('مثال: homepage_ads')
                            ->helperText('اختياري - لتتبع أداء الإعلانات في AdSense')
                            ->prefixIcon('heroicon-o-signal'),
                    ])
                    ->columns(1),

                Section::make('إعدادات متقدمة')
                    ->description('تخصيص سلوك تحميل الإعلانات')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible()
                    ->schema([
                        Toggle::make('load_adsense_script')
                            ->label('تحميل سكريبت AdSense')
                            ->helperText('تحميل سكريبت adsbygoogle.js تلقائياً'),
                        
                        Toggle::make('adsense_auto_ads')
                            ->label('تفعيل الإعلانات التلقائية (Auto Ads)')
                            ->helperText('السماح لـ Google بوضع إعلانات تلقائياً في أماكن مناسبة'),
                        
                        Toggle::make('disable_on_admin')
                            ->label('إخفاء الإعلانات للمشرفين')
                            ->helperText('عدم عرض الإعلانات للمستخدمين المسجلين'),
                        
                        TextInput::make('ads_delay_ms')
                            ->label('تأخير تحميل الإعلانات (بالملي ثانية)')
                            ->numeric()
                            ->placeholder('0')
                            ->helperText('تأخير تحميل الإعلانات لتحسين سرعة الصفحة (0 = بدون تأخير)')
                            ->suffix('ms'),
                    ])
                    ->columns(2),

                Section::make('كود مخصص')
                    ->description('إضافة كود إعلاني مخصص (اختياري)')
                    ->icon('heroicon-o-code-bracket')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Textarea::make('adsense_custom_script')
                            ->label('كود سكريبت مخصص')
                            ->placeholder('<!-- أضف أي كود إضافي هنا -->')
                            ->helperText('سيتم إضافته في الهيدر بعد سكريبت AdSense')
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),

                Section::make('معاينة الإعدادات')
                    ->description('معلومات الحساب الحالية')
                    ->icon('heroicon-o-eye')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('preview')
                            ->label('')
                            ->content(function () {
                                $publisherId = SiteSetting::get('adsense_publisher_id', '');
                                $enabled = SiteSetting::get('adsense_enabled', '0') === '1';
                                
                                if (empty($publisherId)) {
                                    return new \Illuminate\Support\HtmlString('
                                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                            <div class="flex items-center gap-2 text-yellow-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                <span class="font-bold">لم يتم إعداد AdSense بعد</span>
                                            </div>
                                            <p class="mt-2 text-sm text-yellow-600">أدخل معرف الناشر الخاص بك لبدء عرض الإعلانات</p>
                                        </div>
                                    ');
                                }
                                
                                $statusColor = $enabled ? 'green' : 'red';
                                $statusText = $enabled ? 'مفعّل' : 'معطّل';
                                $statusIcon = $enabled ? '✅' : '❌';
                                
                                return new \Illuminate\Support\HtmlString("
                                    <div class=\"p-4 bg-slate-50 border border-slate-200 rounded-lg\">
                                        <div class=\"grid grid-cols-2 gap-4\">
                                            <div>
                                                <p class=\"text-sm text-slate-500\">معرف الناشر</p>
                                                <p class=\"font-mono font-bold text-slate-800\">{$publisherId}</p>
                                            </div>
                                            <div>
                                                <p class=\"text-sm text-slate-500\">الحالة</p>
                                                <p class=\"font-bold text-{$statusColor}-600\">{$statusIcon} {$statusText}</p>
                                            </div>
                                        </div>
                                    </div>
                                ");
                            }),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::set('adsense_publisher_id', $data['adsense_publisher_id'] ?? '', 'ads', 'text', 'AdSense Publisher ID');
        SiteSetting::set('adsense_enabled', ($data['adsense_enabled'] ?? false) ? '1' : '0', 'ads', 'boolean', 'AdSense Enabled');
        SiteSetting::set('adsense_default_channel', $data['default_channel'] ?? '', 'ads', 'text', 'Default Channel');
        SiteSetting::set('load_adsense_script', ($data['load_adsense_script'] ?? true) ? '1' : '0', 'ads', 'boolean', 'Load AdSense Script');
        SiteSetting::set('adsense_auto_ads', ($data['adsense_auto_ads'] ?? false) ? '1' : '0', 'ads', 'boolean', 'Auto Ads');
        SiteSetting::set('disable_ads_on_admin', ($data['disable_on_admin'] ?? true) ? '1' : '0', 'ads', 'boolean', 'Disable Ads on Admin');
        SiteSetting::set('ads_delay_ms', $data['ads_delay_ms'] ?? '0', 'ads', 'number', 'Ads Delay MS');
        SiteSetting::set('adsense_custom_script', $data['adsense_custom_script'] ?? '', 'ads', 'code', 'Custom AdSense Script');

        SiteSetting::clearCache();

        Notification::make()
            ->title('تم حفظ إعدادات AdSense بنجاح')
            ->success()
            ->send();
    }
}
