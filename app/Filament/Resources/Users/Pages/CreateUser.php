<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (filled($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }

    // ✅ Afișează notificarea + așteaptă 2 secunde înainte de redirect
    protected function afterCreate(): void
    {
        Notification::make()
            ->title('User created successfully!')
            ->success()
            ->duration(2000) // ms = 2 secunde
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
