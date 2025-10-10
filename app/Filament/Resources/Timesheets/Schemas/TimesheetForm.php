<?php

namespace App\Filament\Resources\Timesheets\Schemas;

use App\Models\Rate;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TimesheetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Timesheet Details')
                    ->schema([
                        Select::make('worker_id')
                            ->label('Worker')
                            ->relationship('worker', 'first_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) =>
                            self::loadWorkerRates($state, $set)
                            ),
                        Select::make('project_id')
                            ->label('Project')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('work_date')
                            ->required()
                            ->default(now())
                            ->maxDate(now()),
                    ])->columns(3),

                Section::make('Time & Shift')
                    ->schema([
                        TimePicker::make('clock_in')
                            ->seconds(false)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $get, callable $set) =>
                            self::calculateHours($get, $set)
                            ),
                        TimePicker::make('clock_out')
                            ->seconds(false)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $get, callable $set) =>
                            self::calculateHours($get, $set)
                            ),
                        TextInput::make('hours_worked')
                            ->numeric()
                            ->step(0.25)
                            ->suffix('hours')
                            ->required(),
                        Select::make('shift_type')
                            ->options([
                                'day' => 'Day Shift',
                                'night' => 'Night Shift',
                                'custom' => 'Custom',
                            ])
                            ->default('day')
                            ->required()
                            ->reactive(),
                    ])->columns(4),

                Section::make('Rate Information')
                    ->schema([
                        Select::make('rate_id')
                            ->label('Rate')
                            ->options(fn ($get) =>
                            Rate::where('worker_id', $get('worker_id'))
                                ->where('rate_type', $get('shift_type'))
                                ->active()
                                ->pluck('name', 'id')
                            )
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $rate = Rate::find($state);
                                    $set('rate_amount', $rate->rate_amount);
                                    $set('rate_type', $rate->rate_type);
                                }
                            }),
                        TextInput::make('rate_amount')
                            ->numeric()
                            ->prefix('£')
                            ->required()
                            ->step(0.01),
                        Select::make('rate_type')
                            ->options([
                                'hourly' => 'Hourly',
                                'daily' => 'Daily',
                                'nightly' => 'Nightly',
                                'shift' => 'Shift',
                            ])
                            ->default('hourly')
                            ->required(),
                    ])->columns(3),

                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'submitted' => 'Submitted',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'invoiced' => 'Invoiced',
                            ])
                            ->default('draft')
                            ->required()
                            ->disabled(fn ($record) => $record && $record->status === 'invoiced'),
                    ]),
            ]);
    }

    protected static function calculateHours($get, $set): void
    {
        $clockIn = $get('clock_in');
        $clockOut = $get('clock_out');

        if ($clockIn && $clockOut) {
            $start = \Carbon\Carbon::parse($clockIn);
            $end = \Carbon\Carbon::parse($clockOut);
            $hours = $end->diffInMinutes($start) / 60;
            $set('hours_worked', round($hours, 2));
        }
    }

    protected static function loadWorkerRates($workerId, $set): void
    {
        if (!$workerId) return;

        $defaultRate = Rate::where('worker_id', $workerId)
            ->where('is_active', true)
            ->first();

        if ($defaultRate) {
            $set('rate_id', $defaultRate->id);
            $set('rate_amount', $defaultRate->rate_amount);
            $set('rate_type', $defaultRate->rate_type);
        }
    }
}
