<?php

namespace App\Filament\Resources\Materials\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Material Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Inventory')
                    ->schema([
                        TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->step(0.01),
                        TextInput::make('minimum_quantity')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->step(0.01)
                            ->helperText('Alert when stock falls below this level'),
                        TextInput::make('unit')
                            ->required()
                            ->default('piece')
                            ->maxLength(255),
                        TextInput::make('unit_cost')
                            ->numeric()
                            ->prefix('£')
                            ->step(0.01),
                        TextInput::make('supplier')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }
}
