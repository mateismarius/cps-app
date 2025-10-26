<?php

namespace App\Filament\Resources\Engineers\Pages;

use App\Filament\Resources\Engineers\EngineerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEngineer extends ViewRecord
{
    protected static string $resource = EngineerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
