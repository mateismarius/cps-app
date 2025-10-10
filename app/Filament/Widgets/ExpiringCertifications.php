<?php

namespace App\Filament\Widgets;

use App\Models\Certification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ExpiringCertifications extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Certification::query()
                    ->where('expiry_date', '<=', now()->addDays(90))
                    ->where('expiry_date', '>=', now())
                    ->orderBy('expiry_date')
            )
            ->columns([
                 TextColumn::make('name'),
                 TextColumn::make('certifiable_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => class_basename($state)),
                 TextColumn::make('certifiable.name')
                    ->label('Holder'),
                 TextColumn::make('expiry_date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) =>
                    $record->expiry_date <= now()->addDays(30) ? 'danger' : 'warning'
                    ),
                 TextColumn::make('status')
                     ->badge()
                    ->colors([
                        'success' => 'valid',
                        'warning' => 'expiring_soon',
                        'danger' => 'expired',
                    ]),
            ]);
    }
}
