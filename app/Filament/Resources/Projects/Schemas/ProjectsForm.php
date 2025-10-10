<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make('Project Details')
                    ->schema([
                         TextInput::make('project_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->default(fn () => 'PRJ-' . str_pad(Project::max('id') + 1, 5, '0', STR_PAD_LEFT)),
                         TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                         Select::make('client_id')
                            ->relationship('client', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                 TextInput::make('name')
                                    ->required(),
                                 TextInput::make('email')
                                    ->email(),
                            ]),
                         Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'active' => 'Active',
                                'on_hold' => 'On Hold',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required(),
                    ])->columns(2),

                 Section::make('Project Management')
                    ->schema([
                         Select::make('project_manager_id')
                            ->label('Project Manager')
                            ->relationship('projectManager', 'name')
                            ->searchable()
                            ->preload(),
                         Select::make('supervisor_id')
                            ->label('Supervisor')
                            ->relationship('supervisor', 'name')
                            ->searchable()
                            ->preload(),
                         Textarea::make('location')
                            ->rows(2),
                         Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                 Section::make('Timeline & Budget')
                    ->schema([
                         DatePicker::make('start_date'),
                         DatePicker::make('end_date'),
                         DatePicker::make('deadline'),
                         Select::make('billing_type')
                            ->options([
                                'time_and_materials' => 'Time & Materials',
                                'fixed_price' => 'Fixed Price',
                                'shifts' => 'Shifts',
                            ])
                            ->default('time_and_materials')
                            ->reactive(),
                         TextInput::make('allocated_shifts')
                            ->numeric()
                            ->visible(fn ($get) => $get('billing_type') === 'shifts'),
                         TextInput::make('budget')
                            ->numeric()
                            ->prefix('£')
                            ->maxValue(999999999.99),
                         TextInput::make('actual_cost')
                            ->numeric()
                            ->prefix('£')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(3),

                 Section::make('Additional Information')
                    ->schema([
                         KeyValue::make('required_permits')
                            ->label('Required Permits')
                            ->keyLabel('Permit Type')
                            ->valueLabel('Details'),
                         Repeater::make('risks')
                            ->schema([
                                 TextInput::make('risk')
                                    ->required(),
                                 Select::make('severity')
                                    ->options([
                                        'low' => 'Low',
                                        'medium' => 'Medium',
                                        'high' => 'High',
                                    ]),
                                 Textarea::make('mitigation')
                                    ->rows(2),
                            ])
                            ->columns(3),
                         Repeater::make('meetings')
                            ->schema([
                                 DateTimePicker::make('date'),
                                 TextInput::make('title'),
                                 Textarea::make('notes')
                                    ->rows(2),
                            ])
                            ->columns(3),
                    ])->collapsed(),
            ]);
    }
}
