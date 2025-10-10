<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                 TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                 Textarea::make('description')
                    ->rows(3),
                 Select::make('type')
                    ->options([
                        'contract' => 'Contract',
                        'permit' => 'Permit',
                        'report' => 'Report',
                        'photo' => 'Photo',
                        'drawing' => 'Drawing',
                        'other' => 'Other',
                    ])
                    ->required(),
                 FileUpload::make('file_path')
                    ->required()
                    ->directory('project-documents')
                    ->visibility('private'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('title')
                    ->searchable(),
                 TextColumn::make('type')
                    ->badge(),
                 TextColumn::make('uploadedBy.name')
                    ->label('Uploaded By'),
                 TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                 CreateAction::make(),
            ])
            ->recordActions([
                 Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => route('documents.download', $record)),
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
