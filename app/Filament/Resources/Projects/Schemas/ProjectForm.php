<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Company;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project Information')
                    ->schema([
                        Select::make('main_company_id')
                            ->label('Main Company')
                            ->relationship('mainCompany', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(50),
                            ])
                            ->columnSpan(1),

                        TextInput::make('name')
                            ->label('Project Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Project Timeline')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),

                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('start_date')
                            ->columnSpan(1),

                        Select::make('status')
                            ->label('Status')
                            ->options(Project::getStatusOptions())
                            ->default(Project::STATUS_PENDING)
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        Select::make('billing_type')
                            ->label('Billing Type')
                            ->options(Project::getBillingTypeOptions())
                            ->default(Project::BILLING_SHIFTS)
                            ->required()
                            ->native(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }
}
