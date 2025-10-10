<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('trading_name'),
                TextInput::make('registration_number'),
                TextInput::make('vat_number'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('city'),
                TextInput::make('postcode'),
                TextInput::make('country')
                    ->required()
                    ->default('UK'),
                TextInput::make('bank_details'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
            ]);
    }
}
