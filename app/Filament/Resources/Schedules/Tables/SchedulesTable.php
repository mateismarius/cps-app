<?php

namespace App\Filament\Resources\Schedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('schedule_date')
                    ->label('Date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->schedule_date < now()->startOfDay() ? 'secondary' : 'primary'),
                 TextColumn::make('worker.full_name')
                    ->label('Worker')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                 TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->limit(30),
                 TextColumn::make('shift_type')
                     ->badge()
                    ->label('Shift')
                    ->colors([
                        'primary' => 'day',
                        'warning' => 'night',
                    ]),
                 TextColumn::make('start_time')
                    ->time()
                    ->toggleable(),
                 TextColumn::make('end_time')
                    ->time()
                    ->toggleable(),
                 TextColumn::make('role')
                     ->badge()
                    ->colors([
                        'secondary' => 'worker',
                        'info' => 'team_leader',
                        'success' => 'supervisor',
                    ]),
                 TextColumn::make('createdBy.name')
                    ->label('Scheduled By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                 SelectFilter::make('worker')
                    ->relationship('worker', 'first_name')
                    ->searchable()
                    ->preload(),
                 SelectFilter::make('project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),
                 SelectFilter::make('shift_type')
                    ->options([
                        'day' => 'Day',
                        'night' => 'Night',
                    ]),
                 Filter::make('schedule_date')
                    ->schema([
                        DatePicker::make('from')
                            ->default(now()->startOfWeek()),
                        DatePicker::make('until')
                            ->default(now()->endOfWeek()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('schedule_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('schedule_date', '<=', $date),
                            );
                    }),
                 Filter::make('upcoming')
                    ->query(fn (Builder $query): Builder => $query->where('schedule_date', '>=', now()))
                    ->default(),
            ])
            ->recordActions([
                 ViewAction::make(),
                 EditAction::make(),
                 DeleteAction::make(),
            ])
            ->toolbarActions([
                 BulkActionGroup::make([
                     DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('schedule_date', 'desc');
    }
}
