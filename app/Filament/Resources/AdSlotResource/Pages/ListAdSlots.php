<?php

namespace App\Filament\Resources\AdSlotResource\Pages;

use App\Filament\Resources\AdSlotResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListAdSlots extends ListRecords
{
    protected static string $resource = AdSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إضافة مكان إعلان جديد')
                ->icon('heroicon-o-plus'),
        ];
    }
}
