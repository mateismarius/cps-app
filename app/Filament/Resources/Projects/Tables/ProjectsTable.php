<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('project_number')
                    ->searchable()
                    ->sortable(),
                 TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                 TextColumn::make('client.name')
                    ->searchable()
                    ->sortable(),
                 TextColumn::make('projectManager.name')
                    ->label('PM')
                    ->toggleable(),
                 TextColumn::make('status')
                     ->badge()
                    ->colors([
                        'secondary' => 'pending',
                        'success' => 'active',
                        'warning' => 'on_hold',
                        'info' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                 TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                 TextColumn::make('deadline')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->deadline && $record->deadline < now() && $record->status !== 'completed' ? 'danger' : null),
                 TextColumn::make('budget')
                    ->money('GBP')
                    ->sortable()
                    ->toggleable(),
                 TextColumn::make('workers_count')
                    ->counts('workers')
                    ->label('Team'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'on_hold' => 'On Hold',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('client')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('overdue')
                    ->query(fn ($query) => $query->where('deadline', '<', now())
                        ->whereNotIn('status', ['completed', 'cancelled'])),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
