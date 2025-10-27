<?php

namespace App\Filament\Engineer\Resources\Timesheets\Pages;

use App\Filament\Engineer\Resources\Timesheets\TimesheetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTimesheet extends CreateRecord
{
    protected static string $resource = TimesheetResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Timesheet submitted successfully';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Verifică dacă există deja timesheet pentru această dată
        $exists = \App\Models\Timesheet::where('engineer_id', auth()->id())
            ->where('date', $data['date'])
            ->exists();

        if ($exists) {
            $this->halt();
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Timesheet already exists')
                ->body('You have already submitted a timesheet for this date.')
                ->send();
        }

        $data['engineer_id'] = auth()->id();
        $data['approved'] = false;

        return $data;
    }
}
