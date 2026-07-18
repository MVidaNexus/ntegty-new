<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $modelLabel = 'مقال';
    protected static ?string $pluralModelLabel = 'المدونة والأخبار';
    protected static ?string $navigationLabel = 'المدونة والأخبار';
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Group::make([
                            Forms\Components\Section::make('محتوى المقال')
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->label('عنوان المقال')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            $slug = preg_replace('/\s+/u', '-', trim($state));
                                            $slug = str_replace(['?', '/', '\\', '&', '#', '%'], '', $slug);
                                            $set('slug', $slug);
                                        }),
                                    
                                    Forms\Components\TextInput::make('slug')
                                        ->label('الرابط الفريد (Slug)')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->maxLength(255),
                                    
                                    Forms\Components\Select::make('category')
                                        ->label('التصنيف')
                                        ->options([
                                            'results' => 'نتائج الامتحانات',
                                            'alternatives' => 'بدائل الشهادة الإعدادية',
                                            'capabilities' => 'اختبارات القدرات',
                                            'grades' => 'توزيع الدرجات',
                                        ])
                                        ->required()
                                        ->default('results'),

                                    Forms\Components\Textarea::make('summary')
                                        ->label('ملخص المقال القصير')
                                        ->rows(3)
                                        ->helperText('نبذة مختصرة تظهر في قوائم المقالات (يفضل 150-200 حرف)')
                                        ->maxLength(500),

                                    \FilamentTiptapEditor\TiptapEditor::make('content')
                                        ->label('محتوى المقال الرئيسي')
                                        ->profile('default')
                                        ->required()
                                        ->columnSpanFull(),
                                ])
                        ])->columnSpan(2),

                        Forms\Components\Group::make([
                            Forms\Components\Section::make('النشر والصورة')
                                ->schema([
                                    Forms\Components\FileUpload::make('image_path')
                                        ->label('صورة المقال الرئيسية')
                                        ->directory('blog')
                                        ->image()
                                        ->imageEditor()
                                        ->columnSpanFull(),

                                    Forms\Components\Toggle::make('is_published')
                                        ->label('حالة النشر')
                                        ->default(true),

                                    Forms\Components\DateTimePicker::make('published_at')
                                        ->label('تاريخ النشر')
                                        ->default(now())
                                        ->required(),
                                ]),

                            Forms\Components\Section::make('إعدادات SEO')
                                ->collapsible()
                                ->collapsed()
                                ->schema([
                                    Forms\Components\TextInput::make('seo_title')
                                        ->label('عنوان الصفحة (SEO Title)')
                                        ->maxLength(70),
                                    
                                    Forms\Components\Textarea::make('seo_description')
                                        ->label('وصف الصفحة (Meta Description)')
                                        ->maxLength(160)
                                        ->rows(3),
                                    
                                    Forms\Components\TextInput::make('seo_keywords')
                                        ->label('الكلمات المفتاحية (Meta Keywords)')
                                        ->placeholder('كلمة 1, كلمة 2, كلمة 3')
                                        ->maxLength(255),
                                ])
                        ])->columnSpan(1)
                    ])
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('الصورة')
                    ->square(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->wrap()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('category')
                    ->label('التصنيف')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'results' => 'info',
                        'alternatives' => 'success',
                        'capabilities' => 'warning',
                        'grades' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'results' => 'نتائج الامتحانات',
                        'alternatives' => 'بدائل الشهادة الإعدادية',
                        'capabilities' => 'اختبارات القدرات',
                        'grades' => 'توزيع الدرجات',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('منشور')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('التصنيف')
                    ->options([
                        'results' => 'نتائج الامتحانات',
                        'alternatives' => 'بدائل الشهادة الإعدادية',
                        'capabilities' => 'اختبارات القدرات',
                        'grades' => 'توزيع الدرجات',
                    ]),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('المنشورة فقط')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
