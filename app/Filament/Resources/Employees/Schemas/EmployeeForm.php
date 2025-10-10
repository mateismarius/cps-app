<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('employee_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'EMP-' . str_pad(Employee::count() + 1, 4, '0', STR_PAD_LEFT)),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required(),
                                TextInput::make('email')
                                    ->email()
                                    ->required(),
                                TextInput::make('password')
                                    ->password()
                                    ->required(),
                            ]),
                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        DatePicker::make('date_of_birth'),
                        TextInput::make('national_insurance_number')
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Address')
                    ->schema([
                        Textarea::make('address')
                            ->rows(3),
                        TextInput::make('city')
                            ->maxLength(255),
                        TextInput::make('postcode')
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Employment Details')
                    ->schema([
                        DatePicker::make('employment_start_date')
                            ->required(),
                        DatePicker::make('employment_end_date'),
                        TextInput::make('job_title')
                            ->maxLength(255),
                        TextInput::make('department')
                            ->maxLength(255),
                        TextInput::make('salary_amount')
                            ->numeric()
                            ->prefix('£'),
                        Select::make('salary_period')
                            ->options([
                                'hourly' => 'Hourly',
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                                'monthly' => 'Monthly',
                                'annual' => 'Annual',
                            ]),
                        TextInput::make('holiday_allowance_days')
                            ->numeric()
                            ->default(28)
                            ->suffix('days'),
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'on_leave' => 'On Leave',
                                'terminated' => 'Terminated',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Section::make('Additional Information')
                    ->schema([
                        KeyValue::make('emergency_contact')
                            ->keyLabel('Field')
                            ->valueLabel('Value'),
                        KeyValue::make('bank_details')
                            ->keyLabel('Field')
                            ->valueLabel('Value'),
                    ])->columns(2),
            ]);
    }
}
