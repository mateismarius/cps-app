<?php

namespace App\Filament\Resources\Engineers\Pages;

use App\Filament\Resources\Engineers\EngineerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEngineers extends ListRecords
{
    protected static string $resource = EngineerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
