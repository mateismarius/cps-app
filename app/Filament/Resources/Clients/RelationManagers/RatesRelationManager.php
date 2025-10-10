<?php

// app/Filament/Resources/ClientResource/RelationManagers/RatesRelationManager.php

namespace App\Filament\Resources\Clients\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RatesRelationManager extends RelationManager
{
    protected static string $relationship = 'projectRates';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Select::make('project_id')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Leave empty for default client rate'),
                 Select::make('worker_id')
                    ->relationship('worker', 'first_name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Leave empty for all workers'),
                 Select::make('rate_type')
                    ->options([
                        'hourly' => 'Hourly',
                        'daily' => 'Daily',
                        'nightly' => 'Nightly',
                        'shift' => 'Shift',
                        'fixed_price' => 'Fixed Price',
                    ])
                    ->required(),
                 TextInput::make('rate_amount')
                    ->numeric()
                    ->prefix('£')
                    ->required(),
                 DatePicker::make('valid_from')
                    ->default(now()),
                 DatePicker::make('valid_until'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('project.name')
                    ->label('Project')
                    ->default('All Projects'),
                 TextColumn::make('worker.full_name')
                    ->label('Worker')
                    ->default('All Workers'),
                 TextColumn::make('rate_type')
                    ->badge(),
                 TextColumn::make('rate_amount')
                    ->money('GBP'),
                 TextColumn::make('valid_from')
                    ->date(),
                 TextColumn::make('valid_until')
                    ->date(),
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
