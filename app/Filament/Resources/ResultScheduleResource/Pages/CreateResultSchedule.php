<?php

namespace App\Filament\Resources\ResultScheduleResource\Pages;

use App\Filament\Resources\ResultScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateResultSchedule extends CreateRecord
{
    protected static string $resource = ResultScheduleResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
