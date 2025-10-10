<?php

namespace App\Filament\Resources\Clients\Schemas;


use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make('Company Information')
                    ->schema([
                         TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                         TextInput::make('trading_name')
                            ->maxLength(255),
                         TextInput::make('registration_number')
                            ->maxLength(255),
                         TextInput::make('vat_number')
                            ->maxLength(255),
                    ])->columns(2),

                 Section::make('Contact Details')
                    ->schema([
                         TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                         TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                         Textarea::make('address')
                            ->rows(3),
                         TextInput::make('city')
                            ->maxLength(255),
                         TextInput::make('postcode')
                            ->maxLength(255),
                    ])->columns(2),

                 Section::make('Business Settings')
                    ->schema([
                         TextInput::make('payment_terms_days')
                            ->numeric()
                            ->default(30)
                            ->suffix('days')
                            ->required(),
                         Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required(),
                         KeyValue::make('contacts')
                            ->label('Additional Contacts')
                            ->keyLabel('Name/Role')
                            ->valueLabel('Email/Phone'),
                    ])->columns(2),
            ]);
    }
}
