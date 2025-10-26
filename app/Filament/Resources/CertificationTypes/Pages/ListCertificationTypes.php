<?php

namespace App\Filament\Resources\CertificationTypes\Pages;

use App\Filament\Resources\CertificationTypes\CertificationTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCertificationTypes extends ListRecords
{
    protected static string $resource = CertificationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
