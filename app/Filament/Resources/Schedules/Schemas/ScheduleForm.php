<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make('Schedule Details')
                    ->schema([
                         Select::make('worker_id')
                            ->label('Worker')
                            ->relationship('worker', 'first_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name),
                         Select::make('project_id')
                            ->label('Project')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                         DatePicker::make('schedule_date')
                            ->required()
                            ->default(now())
                            ->minDate(now()),
                         Select::make('shift_type')
                            ->options([
                                'day' => 'Day Shift',
                                'night' => 'Night Shift',
                            ])
                            ->default('day')
                            ->required(),
                    ])->columns(2),

                 Section::make('Shift Details')
                    ->schema([
                         TimePicker::make('start_time')
                            ->seconds(false)
                            ->default('08:00'),
                         TimePicker::make('end_time')
                            ->seconds(false)
                            ->default('17:00'),
                         Select::make('role')
                            ->options([
                                'worker' => 'Worker',
                                'team_leader' => 'Team Leader',
                                'supervisor' => 'Supervisor',
                            ])
                            ->default('worker')
                            ->required(),
                         Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

}
