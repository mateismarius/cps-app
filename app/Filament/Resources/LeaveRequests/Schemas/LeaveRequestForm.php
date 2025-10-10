<?php

namespace App\Filament\Resources\LeaveRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make('Leave Details')
                    ->schema([
                         Select::make('employee_id')
                            ->label('Employee')
                            ->relationship('employee', 'first_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name),
                         Select::make('leave_type')
                            ->options([
                                'annual' => 'Annual Leave',
                                'sick' => 'Sick Leave',
                                'unpaid' => 'Unpaid Leave',
                                'parental' => 'Parental Leave',
                                'other' => 'Other',
                            ])
                            ->required(),
                         DatePicker::make('start_date')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $get, callable $set) =>
                            self::calculateDays($get, $set)
                            ),
                         DatePicker::make('end_date')
                            ->required()
                            ->reactive()
                            ->afterOrEqual('start_date')
                            ->afterStateUpdated(fn ($state, callable $get, callable $set) =>
                            self::calculateDays($get, $set)
                            ),
                         TextInput::make('days_requested')
                            ->numeric()
                            ->required()
                            ->step(0.5)
                            ->suffix('days')
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(2),

                 Section::make('Additional Information')
                    ->schema([
                         Textarea::make('reason')
                            ->rows(3)
                            ->columnSpanFull(),
                         Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required()
                            ->disabled(fn ($record) => !auth()->user()->can('approve_leaves')),
                         Textarea::make('rejection_reason')
                            ->rows(2)
                            ->visible(fn ($get) => $get('status') === 'rejected'),
                    ])->columns(2),
            ]);

    }
    protected static function calculateDays($get, $set): void
    {
        $startDate = $get('start_date');
        $endDate = $get('end_date');

        if ($startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);

            // Calculate working days (excluding weekends)
            $days = 0;
            while ($start->lte($end)) {
                if ($start->isWeekday()) {
                    $days++;
                }
                $start->addDay();
            }

            $set('days_requested', $days);
        }
    }
}
