<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class HomepageSocialSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'سوشيال الصفحة الرئيسية';
    protected static ?string $title = 'أزرار السوشيال في الصفحة الرئيسية';
    protected static ?string $navigationGroup = 'إعدادات السوشيال';
    protected static ?int $navigationSort = 52;
    
    protected static string $view = 'filament.pages.homepage-social-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'whatsapp_url' => Setting::where('key', 'homepage_whatsapp_url')->value('value') ?? '',
            'whatsapp_label' => Setting::where('key', 'homepage_whatsapp_label')->value('value') ?? 'جروب واتساب',
            'whatsapp_active' => Setting::where('key', 'homepage_whatsapp_active')->value('value') == '1',
            
            'telegram_url' => Setting::where('key', 'homepage_telegram_url')->value('value') ?? '',
            'telegram_label' => Setting::where('key', 'homepage_telegram_label')->value('value') ?? 'قناة تليجرام',
            'telegram_active' => Setting::where('key', 'homepage_telegram_active')->value('value') == '1',
            
            'facebook_url' => Setting::where('key', 'homepage_facebook_url')->value('value') ?? '',
            'facebook_label' => Setting::where('key', 'homepage_facebook_label')->value('value') ?? 'صفحة فيسبوك',
            'facebook_active' => Setting::where('key', 'homepage_facebook_active')->value('value') == '1',
            
            'facebook_group_url' => Setting::where('key', 'homepage_facebook_group_url')->value('value') ?? '',
            'facebook_group_label' => Setting::where('key', 'homepage_facebook_group_label')->value('value') ?? 'جروب فيسبوك',
            'facebook_group_active' => Setting::where('key', 'homepage_facebook_group_active')->value('value') == '1',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('واتساب')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('whatsapp_url')
                                ->label('رابط الواتساب')
                                ->url()
                                ->placeholder('https://chat.whatsapp.com/...')
                                ->columnSpan(2),
                            Forms\Components\Toggle::make('whatsapp_active')
                                ->label('مفعّل')
                                ->inline(false),
                        ]),
                        Forms\Components\TextInput::make('whatsapp_label')
                            ->label('نص الزر')
                            ->default('جروب واتساب'),
                    ])
                    ->collapsible(),
                    
                Forms\Components\Section::make('تيليجرام')
                    ->icon('heroicon-o-paper-airplane')
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('telegram_url')
                                ->label('رابط التيليجرام')
                                ->url()
                                ->placeholder('https://t.me/...')
                                ->columnSpan(2),
                            Forms\Components\Toggle::make('telegram_active')
                                ->label('مفعّل')
                                ->inline(false),
                        ]),
                        Forms\Components\TextInput::make('telegram_label')
                            ->label('نص الزر')
                            ->default('قناة تليجرام'),
                    ])
                    ->collapsible(),
                    
                Forms\Components\Section::make('صفحة فيسبوك')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('facebook_url')
                                ->label('رابط صفحة فيسبوك')
                                ->url()
                                ->placeholder('https://facebook.com/...')
                                ->columnSpan(2),
                            Forms\Components\Toggle::make('facebook_active')
                                ->label('مفعّل')
                                ->inline(false),
                        ]),
                        Forms\Components\TextInput::make('facebook_label')
                            ->label('نص الزر')
                            ->default('صفحة فيسبوك'),
                    ])
                    ->collapsible(),
                    
                Forms\Components\Section::make('جروب فيسبوك')
                    ->icon('heroicon-o-users')
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('facebook_group_url')
                                ->label('رابط جروب فيسبوك')
                                ->url()
                                ->placeholder('https://facebook.com/groups/...')
                                ->columnSpan(2),
                            Forms\Components\Toggle::make('facebook_group_active')
                                ->label('مفعّل')
                                ->inline(false),
                        ]),
                        Forms\Components\TextInput::make('facebook_group_label')
                            ->label('نص الزر')
                            ->default('جروب فيسبوك'),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $mappings = [
            'homepage_whatsapp_url' => $data['whatsapp_url'] ?? '',
            'homepage_whatsapp_label' => $data['whatsapp_label'] ?? 'جروب واتساب',
            'homepage_whatsapp_active' => ($data['whatsapp_active'] ?? false) ? '1' : '0',
            
            'homepage_telegram_url' => $data['telegram_url'] ?? '',
            'homepage_telegram_label' => $data['telegram_label'] ?? 'قناة تليجرام',
            'homepage_telegram_active' => ($data['telegram_active'] ?? false) ? '1' : '0',
            
            'homepage_facebook_url' => $data['facebook_url'] ?? '',
            'homepage_facebook_label' => $data['facebook_label'] ?? 'صفحة فيسبوك',
            'homepage_facebook_active' => ($data['facebook_active'] ?? false) ? '1' : '0',
            
            'homepage_facebook_group_url' => $data['facebook_group_url'] ?? '',
            'homepage_facebook_group_label' => $data['facebook_group_label'] ?? 'جروب فيسبوك',
            'homepage_facebook_group_active' => ($data['facebook_group_active'] ?? false) ? '1' : '0',
        ];
        
        foreach ($mappings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('حفظ الإعدادات')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('save'),
        ];
    }
}
