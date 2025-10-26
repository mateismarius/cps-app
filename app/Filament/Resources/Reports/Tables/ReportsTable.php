<?php

namespace App\Filament\Resources\Reports\Tables;

use App\Models\Report;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('report_date')
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

                TextColumn::make('timesheet.date')
                    ->label('Timesheet Date')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('summary')
                    ->label('Summary')
                    ->limit(50)
                    ->searchable()
                    ->tooltip(function (Report $record): ?string {
                        return $record->summary;
                    }),

                IconColumn::make('file_path')
                    ->label('Attachment')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->getStateUsing(fn (Report $record) => !empty($record->file_path)),

                TextColumn::make('timesheet.approved')
                    ->label('Timesheet Status')
                    ->badge()
                    ->getStateUsing(fn (Report $record) => $record->timesheet?->approved)
                    ->formatStateUsing(fn ($state) => $state ? 'Approved' : 'Pending')
                    ->colors([
                        'success' => true,
                        'warning' => false,
                    ])
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
                                fn (Builder $query, $date): Builder => $query->whereDate('report_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('report_date', '<=', $date),
                            );
                    }),

                Filter::make('has_attachment')
                    ->label('Has Attachment')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('file_path'))
                    ->toggle(),

                Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('report_date', now()))
                    ->toggle(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('download')
                        ->label('Download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function (Report $record) {
                            if ($record->file_path && Storage::exists($record->file_path)) {
                                return Storage::download($record->file_path);
                            }
                        })
                        ->visible(fn (Report $record) => !empty($record->file_path)),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('report_date', 'desc');
    }
}
