<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TimesheetsRelationManager extends RelationManager
{
    protected static string $relationship = 'timesheets';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('work_date')
                    ->date()
                    ->sortable(),
                 TextColumn::make('worker.full_name')
                    ->searchable(['first_name', 'last_name']),
                 TextColumn::make('shift_type')
                    ->badge(),
                 TextColumn::make('hours_worked')
                    ->suffix(' hrs'),
                 TextColumn::make('rate_amount')
                    ->money('GBP'),
                 TextColumn::make('status')
                     ->badge()
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'submitted',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'info' => 'invoiced',
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                 ViewAction::make()
                    ->url(fn ($record) => route('filament.admin.resources.timesheets.view', ['record' => $record])),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
