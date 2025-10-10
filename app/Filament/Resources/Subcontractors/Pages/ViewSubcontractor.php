<?php

namespace App\Filament\Resources\Subcontractors\Pages;

use App\Filament\Resources\Subcontractors\SubcontractorResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSubcontractor extends ViewRecord
{
    protected static string $resource = SubcontractorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
