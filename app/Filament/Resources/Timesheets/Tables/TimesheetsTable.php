<?php

namespace App\Filament\Resources\Timesheets\Tables;

use App\Models\Timesheet;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TimesheetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('engineer.name')
                    ->label('Engineer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('schedule.shift_start')
                    ->label('Shift Start')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('schedule.shift_end')
                    ->label('Shift End')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('schedule.location')
                    ->label('Location')
                    ->searchable()
                    ->toggleable()
                    ->limit(30),

                IconColumn::make('approved')
                    ->label('Approved')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextColumn::make('reports_count')
                    ->label('Reports')
                    ->counts('reports')
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('project_id')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('engineer_id')
                    ->label('Engineer')
                    ->relationship('engineer', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Filter::make('date_range')
                    ->schema([
                        DatePicker::make('from')
                            ->label('From Date')
                            ->native(false),
                        DatePicker::make('until')
                            ->label('Until Date')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),

                TernaryFilter::make('approved')
                    ->label('Approval Status')
                    ->placeholder('All timesheets')
                    ->trueLabel('Approved')
                    ->falseLabel('Pending'),

                Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('date', now()))
                    ->toggle(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Timesheet $record) => $record->update(['approved' => true]))
                        ->visible(fn (Timesheet $record) => !$record->approved),
                    Action::make('unapprove')
                        ->label('Unapprove')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn (Timesheet $record) => $record->update(['approved' => false]))
                        ->visible(fn (Timesheet $record) => $record->approved),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['approved' => true])),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }
}
