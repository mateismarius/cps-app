<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Models\Company;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('main_company_id')
                    ->options(fn() =>
                    Company::query()
                        ->where('name', 'CPS Network Communications')
                        ->orderBy('id') // prima înregistrare = CPS
                        ->pluck('name', 'id')
                    )
                    ->default(fn () =>
                    Company::query()
                        ->where('type', 'ltd')
                        ->orderBy('id')
                        ->value('id') // ia ID-ul primei companii (CPS)
                    )
                    ->required(),

                TextInput::make('contact_person'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('address'),
                TextInput::make('default_rate')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('active')
                    ->required(),
            ]);
    }
}
