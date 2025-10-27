<?php

namespace App\Filament\Engineer\Resources\Timesheets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TimesheetsTable
{
    public static function configure(Table $table): Table
    {
        return $table->modifyQueryUsing(fn (Builder $query) =>
        $query->where('engineer_id', auth()->id())
            ->orderBy('date', 'desc')
        )
            ->columns([
                TextColumn::make('date')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('schedule_id')
                    ->label('Scheduled')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn ($record) => $record->schedule_id ? 'Scheduled work' : 'Exceptional entry'),

                TextColumn::make('notes')
                    ->limit(50)
                    ->toggleable()
                    ->searchable(),

                IconColumn::make('approved')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn ($record) => $record->approved ? 'Approved' : 'Pending approval'),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('approved')
                    ->label('Status')
                    ->options([
                        0 => 'Pending',
                        1 => 'Approved',
                    ]),

                Filter::make('date')
                    ->schema([
                        DatePicker::make('from')
                            ->label('From Date'),
                        DatePicker::make('until')
                            ->label('Until Date'),
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
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn ($record) => !$record->approved), // Doar dacă nu e aprobat
            ])
            ->toolbarActions([
                // Fără bulk delete - nu vrem să șteargă accidental
            ])
            ->emptyStateHeading('No timesheets yet')
            ->emptyStateDescription('Start by submitting your first timesheet entry.')
            ->emptyStateIcon('heroicon-o-clock');
    }
}
