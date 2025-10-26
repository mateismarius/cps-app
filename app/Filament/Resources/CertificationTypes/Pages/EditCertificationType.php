<?php

namespace App\Filament\Resources\CertificationTypes\Pages;

use App\Filament\Resources\CertificationTypes\CertificationTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCertificationType extends EditRecord
{
    protected static string $resource = CertificationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
