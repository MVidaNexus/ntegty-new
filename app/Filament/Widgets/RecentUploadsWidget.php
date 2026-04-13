<?php

namespace App\Filament\Widgets;

use App\Models\UploadLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentUploadsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 3;
    
    protected static ?string $heading = 'آخر الملفات المرفوعة';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                UploadLog::query()
                    ->with(['governorate', 'examType'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('batch_name')
                    ->label('الملف')
                    ->icon('heroicon-m-document')
                    ->weight('bold')
                    ->limit(30),
                Tables\Columns\TextColumn::make('governorate.name_ar')
                    ->label('المحافظة')
                    ->badge()
                    ->color('info')
                    ->placeholder('موحد'),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'انتظار',
                        'processing' => 'معالجة',
                        'completed' => 'مكتمل',
                        'failed' => 'فشل',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('processed_rows')
                    ->label('النتائج')
                    ->numeric()
                    ->suffix(' نتيجة'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->since()
                    ->color('gray'),
            ])
            ->paginated(false)
            ->striped();
    }
}
