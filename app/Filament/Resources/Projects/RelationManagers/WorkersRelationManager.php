<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkersRelationManager extends RelationManager
{
    protected static string $relationship = 'workers';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Select::make('role')
                    ->options([
                        'worker' => 'Worker',
                        'team_leader' => 'Team Leader',
                        'supervisor' => 'Supervisor',
                        'foreman' => 'Foreman',
                    ])
                    ->required(),
                 DatePicker::make('assigned_date')
                    ->required()
                    ->default(now()),
                 DatePicker::make('removed_date'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('full_name')
                    ->searchable(['first_name', 'last_name']),
                 TextColumn::make('worker_type')
                    ->badge(),
                 TextColumn::make('pivot.role')
                     ->badge()
                    ->label('Role')
                    ->colors([
                        'secondary' => 'worker',
                        'info' => 'team_leader',
                        'success' => 'supervisor',
                        'warning' => 'foreman',
                    ]),
                 TextColumn::make('pivot.assigned_date')
                    ->label('Assigned')
                    ->date(),
                 TextColumn::make('pivot.removed_date')
                    ->label('Removed')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                 AttachAction::make()
                    ->preloadRecordSelect()
                    ->schema(fn ( AttachAction $action): array => [
                        $action->getRecordSelect(),
                         Select::make('role')
                            ->options([
                                'worker' => 'Worker',
                                'team_leader' => 'Team Leader',
                                'supervisor' => 'Supervisor',
                                'foreman' => 'Foreman',
                            ])
                            ->required(),
                         DatePicker::make('assigned_date')
                            ->required()
                            ->default(now()),
                    ]),
            ])
            ->recordActions([
                 EditAction::make(),
                 DetachAction::make(),
            ])
            ->toolbarActions([
                 BulkActionGroup::make([
                     DetachBulkAction::make(),
                ]),
            ]);
    }
}
