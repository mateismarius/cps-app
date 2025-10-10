<?php

namespace App\Filament\Resources\Subcontractors\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubcontractorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make('Subcontractor Information')
                    ->schema([
                         TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                         Select::make('relationship_type')
                            ->options([
                                'direct' => 'Direct',
                                'indirect' => 'Indirect',
                            ])
                            ->required()
                            ->reactive(),
                         Select::make('parent_subcontractor_id')
                            ->label('Parent Subcontractor')
                            ->relationship('parentSubcontractor', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('relationship_type') === 'indirect'),
                         Select::make('business_type')
                            ->options([
                                'self_employed' => 'Self Employed',
                                'ltd' => 'Limited Company',
                            ])
                            ->required(),
                    ])->columns(2),

                 Section::make('Company Details')
                    ->schema([
                         TextInput::make('registration_number')
                            ->maxLength(255),
                         TextInput::make('vat_number')
                            ->maxLength(255),
                         TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                         TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(2),

                 Section::make('Address')
                    ->schema([
                         Textarea::make('address')
                            ->rows(3),
                         TextInput::make('city')
                            ->maxLength(255),
                         TextInput::make('postcode')
                            ->maxLength(255),
                    ])->columns(2),

                 Section::make('Banking & Status')
                    ->schema([
                         KeyValue::make('bank_details')
                            ->keyLabel('Field')
                            ->valueLabel('Value'),
                         Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
