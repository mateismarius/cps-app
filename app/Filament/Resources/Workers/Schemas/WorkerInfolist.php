<?php

namespace App\Filament\Resources\Workers\Schemas;

use App\Models\Worker;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WorkerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('employee_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('subcontractor_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('worker_type'),
                TextEntry::make('first_name'),
                TextEntry::make('last_name'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Worker $record): bool => $record->trashed()),
            ]);
    }
}
