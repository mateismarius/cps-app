<?php

namespace App\Filament\Resources\LeaveRequests\Tables;


use App\Models\LeaveRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                 TextColumn::make('leave_type')
                     ->badge()
                    ->label('Type')
                    ->colors([
                        'success' => 'annual',
                        'warning' => 'sick',
                        'secondary' => 'unpaid',
                        'info' => 'parental',
                        'primary' => 'other',
                    ]),
                 TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                 TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                 TextColumn::make('days_requested')
                    ->suffix(' days')
                    ->sortable(),
                 TextColumn::make('status')
                     ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'secondary' => 'cancelled',
                    ]),
                 TextColumn::make('approvedBy.name')
                    ->label('Approved By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                 SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ]),
                 SelectFilter::make('leave_type')
                    ->options([
                        'annual' => 'Annual',
                        'sick' => 'Sick',
                        'unpaid' => 'Unpaid',
                        'parental' => 'Parental',
                        'other' => 'Other',
                    ]),
            ])
            ->recordActions([
                 ViewAction::make(),
                 EditAction::make(),
                 Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    })
                    ->visible(fn (LeaveRequest $record) =>
                        $record->status === 'pending' &&
                        auth()->user()->can('approve_leaves')
                    ),
                 Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (LeaveRequest $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    })
                    ->visible(fn (LeaveRequest $record) =>
                        $record->status === 'pending' &&
                        auth()->user()->can('approve_leaves')
                    ),
            ])
            ->toolbarActions([
                 BulkActionGroup::make([
                     DeleteBulkAction::make(),
                ]),
            ]);
    }
}
