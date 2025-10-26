<?php

namespace App\Filament\Resources\Engineers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EngineerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Name')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required(),
                Select::make('trade_id')
                    ->relationship('trade', 'name'),
                Toggle::make('direct_to_main')
                    ->required(),
                TextInput::make('rate_to_subcontractor')
                    ->numeric(),
                TextInput::make('rate_to_main')
                    ->required()
                    ->numeric(),
                Toggle::make('active')
                    ->required(),
            ]);
    }
}
