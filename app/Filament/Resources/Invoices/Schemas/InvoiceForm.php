<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->relationship('project', 'name')
                    ->required(),
                TextInput::make('issuer_company_id')
                    ->required()
                    ->numeric(),
                TextInput::make('receiver_company_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('period_start'),
                DatePicker::make('period_end'),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                DatePicker::make('issued_at'),
                DatePicker::make('due_at'),
                TextInput::make('file_path'),
            ]);
    }
}
