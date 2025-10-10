<?php

namespace App\Filament\Resources\Subcontractors\Pages;

use App\Filament\Resources\Subcontractors\SubcontractorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubcontractors extends ListRecords
{
    protected static string $resource = SubcontractorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
