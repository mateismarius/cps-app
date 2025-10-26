<?php

namespace App\Filament\Resources\Equipment\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EquipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('serial_number'),
                TextInput::make('assigned_to')
                    ->numeric(),
                Select::make('project_id')
                    ->relationship('project', 'name'),
                DatePicker::make('assigned_date'),
                DatePicker::make('return_date'),
                TextInput::make('condition')
                    ->required()
                    ->default('good'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
