<?php

namespace App\Filament\Widgets;

namespace App\Filament\Widgets;

use App\Models\Schedule;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingSchedule extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Schedule::query()
                    ->where('schedule_date', '>=', now())
                    ->orderBy('schedule_date')
                    ->limit(15)
            )
            ->columns([
                 TextColumn::make('schedule_date')
                    ->date()
                    ->sortable(),
                 TextColumn::make('worker.full_name')
                    ->label('Worker'),
                 TextColumn::make('project.name')
                    ->limit(30),
                 TextColumn::make('shift_type')
                     ->badge()
                    ->colors([
                        'primary' => 'day',
                        'warning' => 'night',
                    ]),
                 TextColumn::make('start_time')
                    ->time(),
                 TextColumn::make('role')
                ->badge(),
            ]);
    }
}
