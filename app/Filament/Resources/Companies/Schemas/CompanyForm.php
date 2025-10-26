<?php

namespace App\Filament\Resources\Companies\Schemas;

use App\Models\Company;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('parent_company_id')
                    ->options(fn () =>
                    Company::query()
                        ->where('type', 'ltd')
                        ->orderBy('id') // prima înregistrare = CPS
                        ->pluck('name', 'id')
                    )
                    ->default(fn () =>
                    Company::query()
                        ->where('type', 'ltd')
                        ->orderBy('id')
                        ->value('id') // ia ID-ul primei companii (CPS)
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('type')
                    ->options([
                        'self_employed' => 'Self-employed',
                        'ltd' => 'Limited Company',
                    ])
                    ->default('self_employed')
                    ->required(),
                TextInput::make('contact_person'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                Textarea::make('address'),
                Toggle::make('active')
                    ->inline(false)
                    ->required(),
            ]);
    }
}
