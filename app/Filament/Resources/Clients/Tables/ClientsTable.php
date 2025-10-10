<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                 TextColumn::make('trading_name')
                    ->searchable()
                    ->toggleable(),
                 TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-m-envelope'),
                 TextColumn::make('phone')
                    ->searchable()
                    ->icon('heroicon-m-phone'),
                 TextColumn::make('city')
                    ->searchable()
                    ->toggleable(),
                 TextColumn::make('payment_terms_days')
                    ->label('Payment Terms')
                    ->suffix(' days')
                    ->sortable(),
                 TextColumn::make('status')
                     ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'suspended',
                    ]),
                 TextColumn::make('projects_count')
                    ->counts('projects')
                    ->label('Projects'),
                 TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
