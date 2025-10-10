<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                 TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                 Textarea::make('description')
                    ->rows(3),
                 Select::make('assigned_to')
                    ->relationship('assignedTo', 'first_name')
                    ->searchable()
                    ->preload(),
                 DatePicker::make('due_date'),
                 Select::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->required(),
                 Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('title')
                    ->searchable(),
                 TextColumn::make('assignedTo.full_name')
                    ->label('Assigned To'),
                 TextColumn::make('due_date')
                    ->date(),
                 TextColumn::make('priority')
                     ->badge()
                    ->colors([
                        'secondary' => 'low',
                        'info' => 'medium',
                        'warning' => 'high',
                        'danger' => 'urgent',
                    ]),
                 TextColumn::make('status')
                     ->badge()
                    ->colors([
                        'secondary' => 'pending',
                        'warning' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                 CreateAction::make(),
            ])
            ->recordActions([
                 EditAction::make(),
                 DeleteAction::make(),
            ])
            ->toolbarActions([
                 BulkActionGroup::make([
                     DeleteBulkAction::make(),
                ]),
            ]);
    }
}
