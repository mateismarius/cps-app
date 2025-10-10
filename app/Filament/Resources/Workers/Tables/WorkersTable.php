<?php

namespace App\Filament\Resources\Workers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WorkersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name']),
                 TextColumn::make('worker_type')
                     ->badge()
                    ->colors([
                        'primary' => 'employee',
                        'success' => 'self_employed',
                        'info' => 'ltd',
                    ]),
                 TextColumn::make('subcontractor.name')
                    ->label('Subcontractor')
                    ->searchable()
                    ->toggleable(),
                 TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->toggleable(),
                 TextColumn::make('phone')
                    ->searchable()
                    ->icon('heroicon-m-phone')
                    ->toggleable(),
                 TextColumn::make('trades')
                    ->separator(',')
                    ->limit(2),
                 TextColumn::make('status')
                     ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'suspended',
                    ]),
                 TextColumn::make('projects_count')
                    ->counts('projects')
                    ->label('Active Projects'),
            ])
            ->filters([
                 SelectFilter::make('worker_type')
                    ->options([
                        'employee' => 'Employee',
                        'self_employed' => 'Self Employed',
                        'ltd' => 'LTD',
                    ]),
                 SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
                 SelectFilter::make('subcontractor')
                    ->relationship('subcontractor', 'name')
                    ->searchable()
                    ->preload(),
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
