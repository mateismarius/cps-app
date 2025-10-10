<?php

namespace App\Filament\Widgets;

use App\Models\Timesheet;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTimesheets extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Timesheet::query()
                    ->where('status', 'submitted')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                 TextColumn::make('work_date')
                    ->date(),
                 TextColumn::make('worker.full_name')
                    ->label('Worker'),
                 TextColumn::make('project.name')
                    ->limit(30),
                 TextColumn::make('hours_worked')
                    ->suffix(' hrs'),
                 TextColumn::make('total')
                    ->money('GBP')
                    ->getStateUsing(fn ($record) => $record->calculateAmount()),
                 TextColumn::make('status')
                ->badge(),
            ])
            ->recordActions([
                 Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Timesheet $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    })
                    ->visible(fn () => auth()->user()->can('approve_timesheets')),
            ]);
    }
}
