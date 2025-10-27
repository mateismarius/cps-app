<?php

namespace App\Filament\Engineer\Resources\Timesheets\Schemas;


use App\Models\Schedule;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class TimesheetForm
{
    public static function configure(Schema $schema): Schema
    {
        $userId = Auth::id();
        return $schema
            ->components([
                Section::make('Timesheet Entry')
                    ->schema([
                        DatePicker::make('date')
                            ->label('Date')
                            ->required()
                            ->default(now())
                            ->maxDate(now())
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) use ($userId) {
                                if (!$state) return;

                                // Check for scheduled project
                                $schedule = Schedule::where('engineer_id', $userId)
                                    ->where('date', $state)
                                    ->first();

                                if ($schedule) {
                                    $set('project_id', $schedule->project_id);
                                    $set('schedule_id', $schedule->id);
                                    $set('is_scheduled', true);
                                } else {
                                    $set('project_id', null);
                                    $set('schedule_id', null);
                                    $set('is_scheduled', false);
                                }
                            }),

                        TextEntry::make('schedule_info')
                            ->label('Schedule Information')
                            ->state(function (Get $get) use ($userId) {
                                $date = $get('date');
                                if (!$date) return 'Select a date to see schedule';

                                $schedule = Schedule::where('engineer_id', $userId)
                                    ->where('date', $date)
                                    ->with('project')
                                    ->first();

                                if ($schedule) {
                                    return '✅ Scheduled: ' . $schedule->project->name;
                                }

                                return '⚠️ No schedule found - Select project manually';
                            }),

                        Select::make('project_id')
                            ->label('Project')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn (Get $get) => $get('is_scheduled'))
                            ->helperText(fn (Get $get) =>
                            $get('is_scheduled')
                                ? 'Using scheduled project'
                                : 'Select project for exceptional entry'
                            ),

                        Hidden::make('schedule_id'),
                        Hidden::make('is_scheduled'),
                        Hidden::make('engineer_id')
                            ->default($userId),

                        Textarea::make('notes')
                            ->label('Notes (Optional)')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Add any additional notes about this work day...'),
                    ])
                    ->columns(1),
            ]);
    }
}
