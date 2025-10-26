<?php

namespace App\Filament\Resources\Certificates\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('engineer_id')
                    ->relationship('engineer', 'id')
                    ->required(),
                TextInput::make('certification_type_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('issue_date'),
                DatePicker::make('expiry_date'),
                TextInput::make('file_path'),
                TextInput::make('mime_type'),
                Toggle::make('verified')
                    ->required(),
            ]);
    }
}
