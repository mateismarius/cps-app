<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Subcontractor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invoice Details')
                    ->schema([
                        TextInput::make('invoice_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'INV-' . date('Y') . '-' .
                                str_pad(Invoice::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT))
                            ->maxLength(255),

                        Select::make('invoice_type')
                            ->options([
                                'client'        => 'Client Invoice',
                                'subcontractor' => 'Subcontractor Invoice',
                                'payslip'       => 'Employee Payslip',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('invoiceable_id', null)),

                        Select::make('invoiceable_id')
                            ->label(fn ($get) => match ($get('invoice_type')) {
                                'client'        => 'Client',
                                'subcontractor' => 'Subcontractor',
                                'payslip'       => 'Employee',
                                default         => 'Select Type First',
                            })
                            ->options(fn ($get) => match ($get('invoice_type')) {
                                'client'        => Client::pluck('name', 'id'),
                                'subcontractor' => Subcontractor::pluck('name', 'id'),
                                default         => null,
                            })
                            ->required()
                            ->searchable()
                            ->preload(),

                        Hidden::make('invoiceable_type')
                            ->default(fn ($get) => match ($get('invoice_type')) {
                                'client'        => Client::class,
                                'subcontractor' => Subcontractor::class,
                                default         => null,
                            }),
                    ])->columns(3),

                Section::make('Dates & Status')
                    ->schema([
                        DatePicker::make('invoice_date')
                            ->required()
                            ->default(now()),
                        DatePicker::make('due_date')
                            ->required()
                            ->default(now()->addDays(30))
                            ->afterOrEqual('invoice_date'),
                        DatePicker::make('paid_date')
                            ->visible(fn ($get) => in_array($get('status'), ['paid'])),
                        Select::make('status')
                            ->options([
                                'draft'     => 'Draft',
                                'sent'      => 'Sent',
                                'paid'      => 'Paid',
                                'overdue'   => 'Overdue',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columns(3),

                Group::make()
                    ->schema([
                        Section::make('Invoice Items')
                            ->schema([
                                Repeater::make('items')
                                    ->relationship('items')
                                    ->schema([
                                        TextInput::make('description')
                                            ->required()
                                            ->columnSpan(3),

                                        TextInput::make('quantity')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                                $amount = ($state ?? 0) * ($get('unit_price') ?? 0);
                                                $set('amount', $amount);
                                                static::recalculateTotals($get, $set);
                                            }),

                                        TextInput::make('unit')
                                            ->default('hours')
                                            ->required(),

                                        TextInput::make('unit_price')
                                            ->numeric()
                                            ->prefix('£')
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                                $amount = ($get('quantity') ?? 0) * ($state ?? 0);
                                                $set('amount', $amount);
                                                static::recalculateTotals($get, $set);
                                            }),

                                        TextInput::make('amount')
                                            ->numeric()
                                            ->prefix('£')
                                            ->required()
                                            ->disabled()
                                            ->dehydrated(),
                                    ])
                                    ->columns(7)
                                    ->defaultItems(1)
                                    ->reorderable()
                                    ->collapsible()
                                    ->afterStateUpdated(fn ($state, $get, $set) => static::recalculateTotals($get, $set))
                                    ->addActionLabel('Add Item')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Totals')
                            ->schema([
                                TextInput::make('subtotal')
                                    ->numeric()
                                    ->prefix('£')
                                    ->disabled()
                                    ->dehydrated(),

                                TextInput::make('vat_rate')
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(20)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, $get, $set) => static::recalculateTotals($get, $set)),

                                TextInput::make('vat_amount')
                                    ->numeric()
                                    ->prefix('£')
                                    ->disabled()
                                    ->dehydrated(),

                                TextInput::make('total_amount')
                                    ->numeric()
                                    ->prefix('£')
                                    ->disabled()
                                    ->dehydrated(),

                                Textarea::make('notes')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])->columns(4),
                    ])
                    ->columnSpanFull()
                    ->columns(1),
            ]);

    }
    protected static function recalculateTotals(callable $get, callable $set): void
    {
        // 1️⃣ Preluăm toate liniile din repeater
        $items = $get('items') ?? [];

        // 2️⃣ Subtotal = suma tuturor "amount"-urilor
        $subtotal = collect($items)->sum(fn ($item) => (float) ($item['amount'] ?? 0));

        // 3️⃣ Calculăm TVA și totalul
        $vatRate   = (float) ($get('../../vat_rate') ?? 0);
        $vatAmount = round($subtotal * $vatRate / 100, 2);
        $total     = round($subtotal + $vatAmount, 2);

        // 4️⃣ Actualizăm câmpurile principale
        $set('../../subtotal', $subtotal);
        $set('../../vat_amount', $vatAmount);
        $set('../../total_amount', $total);
    }

}
