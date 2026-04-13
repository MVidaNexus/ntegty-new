<?php

namespace App\Filament\Resources\ResultScheduleResource\Pages;

use App\Filament\Resources\ResultScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResultSchedule extends EditRecord
{
    protected static string $resource = ResultScheduleResource::class;

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
}
