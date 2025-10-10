<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),
                 TextColumn::make('invoice_type')
                    ->badge()
                    ->colors([
                        'primary' => 'client',
                        'warning' => 'subcontractor',
                        'info' => 'payslip',
                    ]),
                 TextColumn::make('invoiceable.name')
                    ->label('To')
                    ->searchable()
                    ->sortable(),
                 TextColumn::make('invoice_date')
                    ->date()
                    ->sortable(),
                 TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) =>
                    $record->due_date < now() && $record->status !== 'paid' ? 'danger' : null
                    ),
                 TextColumn::make('total_amount')
                    ->money('GBP')
                    ->sortable(),
                 TextColumn::make('status')
                     ->badge()
                    ->colors([
                        'secondary' => 'draft',
                        'info' => 'sent',
                        'success' => 'paid',
                        'danger' => 'overdue',
                        'warning' => 'cancelled',
                    ]),
                 TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items'),
            ])
            ->filters([
                 SelectFilter::make('invoice_type')
                    ->options([
                        'client' => 'Client',
                        'subcontractor' => 'Subcontractor',
                        'payslip' => 'Payslip',
                    ]),
                 SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ]),
                 Filter::make('overdue')
                    ->query(fn ($query) => $query
                        ->where('due_date', '<', now())
                        ->where('status', '!=', 'paid')
                    ),
            ])
            ->recordActions([
                 ViewAction::make(),
                 EditAction::make(),
                 Action::make('download_pdf')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Invoice $record) => route('invoices.pdf', $record))
                    ->openUrlInNewTab(),
                 Action::make('mark_paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Invoice $record) => $record->update([
                        'status' => 'paid',
                        'paid_date' => now(),
                    ]))
                    ->visible(fn (Invoice $record) => $record->status !== 'paid'),
            ])
            ->toolbarActions([
                 BulkActionGroup::make([
                     DeleteBulkAction::make(),
                ]),
            ]);
    }
}
