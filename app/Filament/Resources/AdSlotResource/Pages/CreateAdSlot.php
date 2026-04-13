<?php

namespace App\Filament\Resources\AdSlotResource\Pages;

use App\Filament\Resources\AdSlotResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdSlot extends CreateRecord
{
    protected static string $resource = AdSlotResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إنشاء مكان الإعلان بنجاح';
    }
}
