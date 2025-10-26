<?php

namespace App\Filament\Resources\CertificationTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CertificationTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('validity_months')
                    ->required()
                    ->numeric()
                    ->default(12),
                Toggle::make('active')
                    ->required(),
            ]);
    }
}
