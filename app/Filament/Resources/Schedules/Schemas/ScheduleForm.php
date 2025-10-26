<?php

namespace App\Filament\Resources\Schedules\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
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
                        Select::make('project_id')
                            ->label('Project')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('engineer_id', null))
                            ->columnSpan(2),

                        Select::make('engineer_id')
                            ->label('Engineer')
                            ->options(function () {
                                return User::whereHas('engineer', function ($query) {
                                    $query->where('active', true);
                                })
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Only active engineers are shown')
                            ->columnSpan(2),

                        DatePicker::make('date')
                            ->label('Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now())
                            ->columnSpan(2),

                        TextInput::make('location')
                            ->label('Location')
                            ->maxLength(255)
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Section::make('Shift Times')
                    ->schema([

                        TextEntry::make('shift_duration')
                            ->label('Duration')
                            ->state(function ($get) {
                                $start = $get('shift_start');
                                $end = $get('shift_end');

                                if (!$start || !$end) {
                                    return 'N/A';
                                }

                                $startTime = \Carbon\Carbon::parse($start);
                                $endTime = \Carbon\Carbon::parse($end);
                                $hours = $startTime->diffInHours($endTime, true);

                                return number_format($hours, 2) . ' hours';
                            })
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
