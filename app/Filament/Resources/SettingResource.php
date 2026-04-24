<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'إعدادات الموقع';
    protected static ?string $navigationGroup = 'إعدادات الموقع';
    protected static ?string $pluralModelLabel = 'إعدادات الموقع';
    protected static ?string $modelLabel = 'إعداد';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تعديل الإعداد')
                    ->description('قم بتعديل قيمة الإعداد')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\TextInput::make('display_key')
                            ->label('اسم الإعداد')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?Setting $record) => self::getSettingLabel($record?->key ?? '')),
                        
                        Forms\Components\TextInput::make('value')
                            ->label('القيمة')
                            ->hidden(fn (?Setting $record) => in_array($record?->type, ['image', 'boolean']))
                            ->required(fn (?Setting $record) => !in_array($record?->type, ['image', 'boolean']))
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('bool_value')
                            ->label('تفعيل')
                            ->hidden(fn (?Setting $record) => $record?->type !== 'boolean')
                            ->afterStateHydrated(function ($component, $state, ?Setting $record) {
                                if ($record) {
                                    $component->state($record->value === '1' || $record->value === 'true' || $record->value === true || $record->value === 1);
                                }
                            }),

                        Forms\Components\FileUpload::make('image_value')
                            ->label('الصورة')
                            ->image()
                            ->directory('settings')
                            ->hidden(fn (?Setting $record) => $record?->type !== 'image')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->afterStateHydrated(function ($component, ?Setting $record) {
                                if ($record && $record->type === 'image' && $record->value) {
                                    // FileUpload expects array, not string
                                    $component->state([$record->value]);
                                }
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Setting::query()->whereIn('key', ['site_name', 'site_description', 'logo', 'header_icon', 'favicon', 'show_academic_year_filter'])
            )
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\ImageColumn::make('preview_image')
                        ->label('')
                        ->state(fn (?Setting $record) => $record?->type === 'image' ? asset('uploads/' . $record->value) : null)
                        ->size(80)
                        ->circular()
                        ->grow(false)
                        ->extraAttributes(['class' => 'border-2 border-primary-500 shadow-lg'])
                        ->visible(fn (?Setting $record) => $record?->type === 'image'),
                    
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('key')
                            ->label('الإعداد')
                            ->formatStateUsing(fn (string $state): string => self::getSettingLabel($state))
                            ->weight(FontWeight::Bold)
                            ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                            ->icon(fn (?Setting $record) => self::getSettingIcon($record?->key ?? ''))
                            ->color('primary'),
                        
                        Tables\Columns\TextColumn::make('description')
                            ->label('الوصف')
                            ->state(fn (?Setting $record) => self::getSettingDescription($record?->key ?? ''))
                            ->color('gray')
                            ->size(Tables\Columns\TextColumn\TextColumnSize::Small),
                        
                        Tables\Columns\TextColumn::make('display_value')
                            ->label('القيمة الحالية')
                            ->state(function (?Setting $record) {
                                if (!$record) return '—';
                                if ($record->type === 'image') return '📷 صورة مرفوعة';
                                if ($record->type === 'boolean') return $record->value ? '✅ مفعل' : '❌ غير مفعل';
                                return $record->value ?: '—';
                            })
                            ->badge()
                            ->color(fn (?Setting $record) => match(true) {
                                !$record => 'gray',
                                $record->type === 'boolean' && $record->value => 'success',
                                $record->type === 'boolean' && !$record->value => 'danger',
                                $record->type === 'image' => 'info',
                                default => 'gray',
                            }),
                    ])->space(2),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning')
                    ->modalWidth('lg'),
            ])
            ->bulkActions([])
            ->paginated(false)
            ->heading('⚙️ الإعدادات الأساسية')
            ->description('إدارة الإعدادات العامة للموقع - اسم الموقع، الوصف، الشعار، والأيقونات');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
    
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
    
    private static function getSettingLabel(string $key): string
    {
        return match ($key) {
            'site_name' => 'اسم الموقع',
            'site_description' => 'وصف الموقع',
            'logo' => 'شعار الموقع (Logo)',
            'header_icon' => 'صورة الهيدر',
            'favicon' => 'أيقونة المتصفح (Favicon)',
            'show_academic_year_filter' => 'فلتر السنوات الدراسية',
            default => $key,
        };
    }
    
    private static function getSettingIcon(string $key): string
    {
        return match ($key) {
            'site_name' => 'heroicon-o-building-office',
            'site_description' => 'heroicon-o-document-text',
            'logo' => 'heroicon-o-photo',
            'header_icon' => 'heroicon-o-paint-brush',
            'favicon' => 'heroicon-o-star',
            'show_academic_year_filter' => 'heroicon-o-calendar',
            default => 'heroicon-o-cog',
        };
    }
    
    private static function getSettingDescription(string $key): string
    {
        return match ($key) {
            'site_name' => 'الاسم الرسمي الذي يظهر في عنوان الصفحة والهيدر',
            'site_description' => 'وصف الموقع للـ SEO ومحركات البحث',
            'logo' => 'الشعار الرئيسي للموقع (يفضل PNG شفاف)',
            'header_icon' => 'الصورة التي تظهر في هيدر الموقع',
            'favicon' => 'الأيقونة الصغيرة في تاب المتصفح',
            'show_academic_year_filter' => 'إظهار أو إخفاء فلتر السنوات في الصفحة الرئيسية',
            default => '',
        };
    }
}
