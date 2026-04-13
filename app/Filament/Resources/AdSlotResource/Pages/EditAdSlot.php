<?php

namespace App\Filament\Resources\AdSlotResource\Pages;

use App\Filament\Resources\AdSlotResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditAdSlot extends EditRecord
{
    protected static string $resource = AdSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'تم تحديث مكان الإعلان بنجاح';
    }
}
