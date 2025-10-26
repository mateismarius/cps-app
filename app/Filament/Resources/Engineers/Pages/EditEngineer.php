<?php

namespace App\Filament\Resources\Engineers\Pages;

use App\Filament\Resources\Engineers\EngineerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEngineer extends EditRecord
{
    protected static string $resource = EngineerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
