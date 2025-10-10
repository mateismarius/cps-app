<?php

namespace App\Filament\Resources\Equipment\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EquipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Equipment Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('model')
                            ->maxLength(255),
                        TextInput::make('serial_number')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('category')
                            ->maxLength(255),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Purchase Information')
                    ->schema([
                        DatePicker::make('purchase_date'),
                        TextInput::make('purchase_price')
                            ->numeric()
                            ->prefix('£'),
                    ])->columns(2),

                Section::make('Maintenance Schedule')
                    ->schema([
                        DatePicker::make('next_service_date'),
                        DatePicker::make('next_calibration_date'),
                        TextInput::make('service_interval_days')
                            ->numeric()
                            ->suffix('days')
                            ->helperText('Number of days between services'),
                        Select::make('status')
                            ->options([
                                'available' => 'Available',
                                'in_use' => 'In Use',
                                'maintenance' => 'Maintenance',
                                'retired' => 'Retired',
                            ])
                            ->default('available')
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
