<?php

namespace App\Filament\Resources\Subcontractors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubcontractorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('business_type')
                    ->badge()
                    ->colors([
                        'success' => 'self_employed',
                        'info' => 'ltd',
                    ]),
                TextColumn::make('relationship_type')
                    ->badge()
                    ->colors([
                        'primary' => 'direct',
                        'warning' => 'indirect',
                    ]),
                TextColumn::make('parentSubcontractor.name')
                    ->label('Parent')
                    ->toggleable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('workers_count')
                    ->counts('workers')
                    ->label('Workers'),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'suspended',
                    ]),
            ])
            ->filters([
                SelectFilter::make('business_type')
                    ->options([
                        'self_employed' => 'Self Employed',
                        'ltd' => 'LTD',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
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
