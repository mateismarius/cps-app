<?php

namespace App\Filament\Resources\Timesheets\Schemas;

use App\Models\Schedule;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TimesheetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Timesheet Information')
                    ->schema([
                        Select::make('project_id')
                            ->label('Project')
                            ->relationship('project', 'name', fn (Builder $query) => $query->where('status', 'active'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('engineer_id', null);
                                $set('schedule_id', null);
                            })
                            ->columnSpan(2),

                        DatePicker::make('date')
                            ->label('Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now())
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('engineer_id', null);
                                $set('schedule_id', null);
                            })
                            ->columnSpan(2),

                        Select::make('engineer_id')
                            ->label('Engineer (from Schedule)')
                            ->options(function (callable $get) {
                                $projectId = $get('project_id');
                                $date = $get('date');

                                if (!$projectId || !$date) {
                                    return [];
                                }

                                // Get engineers scheduled for this project and date
                                return Schedule::query()
                                    ->where('project_id', $projectId)
                                    ->whereDate('date', $date)
                                    ->with(['engineer'])
                                    ->get()
                                    ->pluck('engineer.name', 'engineer_id')
                                    ->unique()
                                    ->filter()
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                // Auto-set schedule_id when engineer is selected
                                if ($state) {
                                    $schedule = Schedule::query()
                                        ->where('project_id', $get('project_id'))
                                        ->whereDate('date', $get('date'))
                                        ->where('engineer_id', $state)
                                        ->first();

                                    $set('schedule_id', $schedule?->id);
                                }
                            })
                            ->helperText('Only engineers scheduled for this project and date')
                            ->columnSpan(2),

                        Select::make('schedule_id')
                            ->label('Schedule')
                            ->relationship('schedule', 'id')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn ($record) =>
                            "Schedule #{$record->id} - {$record->shift_start} to {$record->shift_end}"
                            )
                            ->helperText('Automatically set based on selected engineer')
                            ->columnSpan(2),

                        Toggle::make('approved')
                            ->label('Approved')
                            ->default(false)
                            ->inline(false)
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Section::make('Schedule Information')
                    ->schema([
                        TextEntry::make('schedule_details')
                            ->label('Schedule Details')
                            ->state(function (callable $get) {
                                $scheduleId = $get('schedule_id');

                                if (!$scheduleId) {
                                    return 'No schedule selected';
                                }

                                $schedule = Schedule::find($scheduleId);

                                if (!$schedule) {
                                    return 'Schedule not found';
                                }

                                return "Shift: {$schedule->shift_start} - {$schedule->shift_end}" .
                                    ($schedule->location ? " | Location: {$schedule->location}" : '');
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (callable $get) => $get('schedule_id')),
            ]);
    }
}
