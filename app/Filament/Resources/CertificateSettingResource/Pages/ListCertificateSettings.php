<?php

namespace App\Filament\Resources\CertificateSettingResource\Pages;

use App\Filament\Resources\CertificateSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCertificateSettings extends ListRecords
{
    protected static string $resource = CertificateSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
