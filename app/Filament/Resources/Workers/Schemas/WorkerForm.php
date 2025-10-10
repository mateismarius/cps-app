<?php

namespace App\Filament\Resources\Workers\Schemas;


use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WorkerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make('Worker Type')
                    ->schema([
                         Select::make('worker_type')
                            ->options([
                                'employee' => 'Employee',
                                'self_employed' => 'Self Employed',
                                'ltd' => 'LTD Company',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('employee_id', null);
                                $set('subcontractor_id', null);
                            }),
                         Select::make('employee_id')
                            ->label('Link to Employee')
                            ->relationship('employee', 'first_name')
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('worker_type') === 'employee'),
                         Select::make('subcontractor_id')
                            ->label('Subcontractor')
                            ->relationship('subcontractor', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => in_array($get('worker_type'), ['self_employed', 'ltd'])),
                    ])->columns(3),

                 Section::make('Personal Information')
                    ->schema([
                         TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                         TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                         TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                         TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(2),

                 Section::make('Skills & Trades')
                    ->schema([
                         CheckboxList::make('trades')
                            ->options([
                                'data_cabling' => 'Data Cabling Engineer',
                                'electrician' => 'Electrician',
                                'plumber' => 'Plumber',
                                'hvac' => 'HVAC Technician',
                                'general_construction' => 'General Construction',
                                'fire_alarm' => 'Fire Alarm Specialist',
                                'security_systems' => 'Security Systems',
                                'fibre_optic' => 'Fibre Optic Specialist',
                            ])
                            ->columns(2),
                         Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
            ]);
    }
}
