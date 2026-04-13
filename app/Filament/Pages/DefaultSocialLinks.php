<?php

namespace App\Filament\Pages;

use App\Models\SocialLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class DefaultSocialLinks extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationLabel = 'السوشيال الافتراضي';
    protected static ?string $title = 'روابط السوشيال الافتراضية';
    protected static ?string $navigationGroup = 'إعدادات السوشيال';
    protected static ?int $navigationSort = 10;
    
    protected static string $view = 'filament.pages.default-social-links';

    public array $links = [];

    public function mount(): void
    {
        $this->loadLinks();
    }

    protected function loadLinks(): void
    {
        $existingLinks = SocialLink::where('scope_type', 'default')
            ->orderBy('sort_order')
            ->get()
            ->map(fn($link) => [
                'id' => $link->id,
                'platform' => $link->platform,
                'url' => $link->url,
                'label' => $link->label,
                'is_active' => $link->is_active,
                'sort_order' => $link->sort_order,
            ])
            ->toArray();

        $this->links = $existingLinks;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Repeater::make('links')
                    ->label('روابط السوشيال ميديا')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Select::make('platform')
                                    ->label('المنصة')
                                    ->options(SocialLink::getPlatformOptions())
                                    ->required()
                                    ->native(false)
                                    ->searchable()
                                    ->columnSpan(1),
                                    
                                Forms\Components\TextInput::make('url')
                                    ->label('الرابط')
                                    ->url()
                                    ->required()
                                    ->placeholder('https://...')
                                    ->columnSpan(2),
                                    
                                Forms\Components\Toggle::make('is_active')
                                    ->label('مفعّل')
                                    ->default(true)
                                    ->inline(false)
                                    ->columnSpan(1),
                            ]),
                            
                        Forms\Components\TextInput::make('label')
                            ->label('تسمية مخصصة (اختياري)')
                            ->placeholder('اتركه فارغاً لاستخدام اسم المنصة')
                            ->maxLength(50),
                    ])
                    ->itemLabel(fn (array $state): ?string => 
                        isset($state['platform']) 
                            ? (SocialLink::getPlatformOptions()[$state['platform']] ?? $state['platform']) . ' - ' . ($state['url'] ?? '')
                            : null
                    )
                    ->addActionLabel('إضافة رابط جديد')
                    ->reorderable()
                    ->reorderableWithDragAndDrop()
                    ->collapsible()
                    ->cloneable()
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ])
            ->statePath('');
    }

    public function save(): void
    {
        // Delete existing default links
        SocialLink::where('scope_type', 'default')->delete();

        // Create new links with sort order
        $index = 0;
        foreach ($this->links as $linkData) {
            if (empty($linkData['platform']) || empty($linkData['url'])) {
                continue;
            }

            SocialLink::create([
                'platform' => $linkData['platform'],
                'url' => $linkData['url'],
                'label' => $linkData['label'] ?? null,
                'scope_type' => 'default',
                'scope_id' => null,
                'sort_order' => (int) $index,
                'is_active' => (bool) ($linkData['is_active'] ?? true),
            ]);
            
            $index++;
        }

        Notification::make()
            ->title('تم الحفظ بنجاح')
            ->success()
            ->send();

        $this->loadLinks();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('حفظ التغييرات')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('save'),
        ];
    }
}
