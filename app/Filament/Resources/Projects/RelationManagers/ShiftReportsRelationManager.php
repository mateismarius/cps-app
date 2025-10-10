<?php

namespace App\Filament\Resources\Projects\RelationManagers;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShiftReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'shiftReports';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                 DatePicker::make('report_date')
                    ->required()
                    ->default(now()),
                 Select::make('shift_type')
                    ->options([
                        'day' => 'Day',
                        'night' => 'Night',
                    ])
                    ->required(),
                 Textarea::make('work_completed')
                    ->rows(3),
                 Textarea::make('issues')
                    ->rows(3),
                 Textarea::make('notes')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('report_date')
                    ->date()
                    ->sortable(),
                 TextColumn::make('shift_type')
                    ->badge(),
                 TextColumn::make('submittedBy.name')
                    ->label('Submitted By'),
                 TextColumn::make('work_completed')
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                 CreateAction::make(),
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
            ]);
    }
}
