<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-envelope'),
                TextColumn::make('user_type')
                    ->badge()
                    ->label('Type')
                    ->colors([
                        'primary' => 'employee',
                        'success' => 'self_employed',
                        'info' => 'subcontractor_ltd',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'employee' => 'Employee',
                        'self_employed' => 'Self Employed',
                        'subcontractor_ltd' => 'Subcontractor LTD',
                        default => 'Unknown',
                    }),
                TextColumn::make('employee.employee_number')
                    ->label('Employee #')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('subcontractor.name')
                    ->label('Subcontractor')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(','),
                IconColumn::make('has_worker')
                    ->label('Worker')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->employee?->worker ||
                        $record->subcontractor?->workers()->exists())
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_type')
                    ->options([
                        'employee' => 'Employee',
                        'self_employed' => 'Self Employed',
                        'subcontractor_ltd' => 'Subcontractor LTD',
                    ]),
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
                Filter::make('has_worker')
                    ->label('Has Worker Profile')
                    ->query(fn (Builder $query): Builder =>
                    $query->whereHas('employee.worker')
                        ->orWhereHas('subcontractor.workers')
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('impersonate')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->visible(fn () => auth()->user()->hasRole('super_admin'))
                    ->action(fn (User $record) =>
                    auth()->login($record)
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
