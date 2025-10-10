<?php

namespace App\Filament\Resources\Timesheets\Tables;

use App\Models\Timesheet;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TimesheetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('work_date')
                    ->date()
                    ->sortable(),
                 TextColumn::make('worker.full_name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                 TextColumn::make('project.name')
                    ->searchable()
                    ->limit(30),
                 TextColumn::make('shift_type')
                    ->badge()
                    ->colors([
                        'primary' => 'day',
                        'warning' => 'night',
                        'info' => 'custom',
                    ]),
                 TextColumn::make('clock_in')
                    ->time()
                    ->toggleable(),
                 TextColumn::make('clock_out')
                    ->time()
                    ->toggleable(),
                 TextColumn::make('hours_worked')
                    ->suffix(' hrs')
                    ->sortable(),
                 TextColumn::make('rate_amount')
                    ->money('GBP')
                    ->label('Rate'),
                 TextColumn::make('total')
                    ->money('GBP')
                    ->getStateUsing(fn ($record) => $record->calculateAmount())
                    ->sortable(),
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
                SelectFilter::make('worker')
                    ->relationship('worker', 'first_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'invoiced' => 'Invoiced',
                    ]),
                Filter::make('work_date')
                    ->schema([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('work_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('work_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                 ViewAction::make(),
                 EditAction::make(),
                 Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Timesheet $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    })
                    ->visible(fn (Timesheet $record) =>
                        $record->status === 'submitted' &&
                        auth()->user()->can('approve_timesheets')
                    ),
                 Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Timesheet $record) => $record->update(['status' => 'rejected']))
                    ->visible(fn (Timesheet $record) =>
                        $record->status === 'submitted' &&
                        auth()->user()->can('approve_timesheets')
                    ),
            ])
            ->toolbarActions([
                 BulkActionGroup::make([
                     DeleteBulkAction::make(),
                     BulkAction::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update([
                                'status' => 'approved',
                                'approved_by' => auth()->id(),
                                'approved_at' => now(),
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
