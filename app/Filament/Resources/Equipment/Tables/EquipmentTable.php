<?php

namespace App\Filament\Resources\Equipment\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EquipmentTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('model')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('serial_number')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('next_service_date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) =>
                    $record->next_service_date && $record->next_service_date <= now()->addDays(30)
                        ? 'danger'
                        : null
                    ),
                TextColumn::make('next_calibration_date')
                    ->date()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'available',
                        'info' => 'in_use',
                        'warning' => 'maintenance',
                        'danger' => 'retired',
                    ]),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'in_use' => 'In Use',
                        'maintenance' => 'Maintenance',
                        'retired' => 'Retired',
                    ]),
                Filter::make('needs_service')
                    ->query(fn ($query) => $query->needsService()),
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
