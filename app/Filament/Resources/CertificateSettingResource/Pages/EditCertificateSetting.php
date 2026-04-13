<?php

namespace App\Filament\Resources\CertificateSettingResource\Pages;

use App\Filament\Resources\CertificateSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCertificateSetting extends EditRecord
{
    protected static string $resource = CertificateSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('preview')
                ->label('معاينة الشهادة')
                ->icon('heroicon-o-eye')
                ->url(route('certificate.index'))
                ->openUrlInNewTab(),
        ];
    }
}
