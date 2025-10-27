<?php

namespace App\Filament\Engineer\Resources\Timesheets\Pages;

use App\Filament\Engineer\Resources\Timesheets\TimesheetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTimesheet extends EditRecord
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
