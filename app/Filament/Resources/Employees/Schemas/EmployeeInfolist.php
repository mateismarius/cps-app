<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Models\Employee;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EmployeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('employee_number'),
                TextEntry::make('first_name'),
                TextEntry::make('last_name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('date_of_birth')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('national_insurance_number')
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('city')
                    ->placeholder('-'),
                TextEntry::make('postcode')
                    ->placeholder('-'),
                TextEntry::make('employment_start_date')
                    ->date(),
                TextEntry::make('employment_end_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('job_title')
                    ->placeholder('-'),
                TextEntry::make('department')
                    ->placeholder('-'),
                TextEntry::make('salary_amount')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('salary_period'),
                TextEntry::make('holiday_allowance_days')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Employee $record): bool => $record->trashed()),
            ]);
    }
}
