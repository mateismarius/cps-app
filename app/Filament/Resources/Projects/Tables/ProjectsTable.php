<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Project Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('mainCompany.name')
                    ->label('Main Company')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'secondary' => Project::STATUS_PENDING,
                        'success' => Project::STATUS_ACTIVE,
                        'primary' => Project::STATUS_COMPLETED,
                        'danger' => Project::STATUS_CANCELLED,
                    ])
                    ->formatStateUsing(fn (string $state): string => Project::getStatusOptions()[$state] ?? $state),

                TextColumn::make('billing_type')
                    ->label('Billing')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Project::getBillingTypeOptions()[$state] ?? $state)
                    ->toggleable(),

                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('end_date')
                    ->label('End Date')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('schedules_count')
                    ->label('Schedules')
                    ->counts('schedules')
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('timesheets_count')
                    ->label('Timesheets')
                    ->counts('timesheets')
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Project::getStatusOptions())
                    ->multiple(),

                SelectFilter::make('billing_type')
                    ->options(Project::getBillingTypeOptions())
                    ->multiple(),

                SelectFilter::make('main_company_id')
                    ->label('Main Company')
                    ->relationship('mainCompany', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('active_projects')
                    ->label('Active Projects')
                    ->query(fn (Builder $query): Builder => $query->where('status', Project::STATUS_ACTIVE))
                    ->toggle(),
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
            ->defaultSort('created_at', 'desc');
    }
}
