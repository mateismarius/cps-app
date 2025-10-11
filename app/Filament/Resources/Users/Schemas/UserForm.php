<?php

namespace App\Filament\Resources\Users\Schemas;



use App\Models\Employee;
use App\Models\Worker;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('User Details')
                    ->tabs([
                        // Tab 1: Account Information
                        Tab::make('Account')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Account Details')
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                        TextInput::make('password')
                                            ->password()
                                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                            ->dehydrated(fn ($state) => filled($state))
                                            ->required(fn (string $context): bool => $context === 'create')
                                            ->maxLength(255),
                                        Select::make('user_type')
                                            ->label('User Type')
                                            ->options([
                                                'employee' => 'Employee',
                                                'subcontractor_ltd' => 'Subcontractor (LTD)',
                                                'self_employed' => 'Self Employed',
                                            ])
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                // Reset associated IDs when type changes
                                                $set('subcontractor_id', null);
                                            }),
                                    ])->columns(2),

                                Section::make('Roles & Permissions')
                                    ->schema([
                                        Select::make('roles')
                                            ->label('Roles')
                                            ->multiple()
                                            ->relationship('roles', 'name')
                                            ->preload()
                                            ->searchable(),
                                        CheckboxList::make('permissions')
                                            ->relationship('permissions', 'name')
                                            ->columns(3)
                                            ->searchable()
                                            ->bulkToggleable(),
                                    ]),
                            ]),

                        // Tab 2: Employee Details (only if user_type = employee)
                        Tabs\Tab::make('Employee')
                            ->icon('heroicon-o-identification')
                            ->visible(fn ($get) => $get('user_type') === 'employee')
                            ->schema([
                                Section::make('Employee Information')
                                    ->description('Complete employee details for this user')
                                    ->schema([
                                        TextInput::make('employee.employee_number')
                                            ->label('Employee Number')
                                            ->default(fn () => 'EMP-' . str_pad(Employee::count() + 1, 4, '0', STR_PAD_LEFT))
                                            ->required()
                                            ->unique(Employee::class, 'employee_number', ignoreRecord: true)
                                            ->maxLength(255),
                                        TextInput::make('employee.first_name')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('employee.last_name')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('employee.email')
                                            ->email()
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('employee.phone')
                                            ->tel()
                                            ->maxLength(255),
                                        DatePicker::make('employee.date_of_birth'),
                                    ])->columns(2),

                                Section::make('Employment Details')
                                    ->schema([
                                        DatePicker::make('employee.employment_start_date')
                                            ->required()
                                            ->default(now()),
                                        DatePicker::make('employee.employment_end_date'),
                                        TextInput::make('employee.job_title')
                                            ->maxLength(255),
                                        TextInput::make('employee.department')
                                            ->maxLength(255),
                                        TextInput::make('employee.national_insurance_number')
                                            ->label('NI Number')
                                            ->maxLength(255),
                                        Select::make('employee.status')
                                            ->options([
                                                'active' => 'Active',
                                                'inactive' => 'Inactive',
                                                'on_leave' => 'On Leave',
                                                'terminated' => 'Terminated',
                                            ])
                                            ->default('active')
                                            ->required(),
                                    ])->columns(3),

                                Section::make('Salary Information')
                                    ->schema([
                                        TextInput::make('employee.salary_amount')
                                            ->numeric()
                                            ->prefix('£')
                                            ->step(0.01),
                                        Select::make('employee.salary_period')
                                            ->options([
                                                'hourly' => 'Hourly',
                                                'daily' => 'Daily',
                                                'weekly' => 'Weekly',
                                                'monthly' => 'Monthly',
                                                'annual' => 'Annual',
                                            ])
                                            ->default('monthly'),
                                        TextInput::make('employee.holiday_allowance_days')
                                            ->numeric()
                                            ->default(28)
                                            ->suffix('days'),
                                    ])->columns(3),

                                Section::make('Address')
                                    ->schema([
                                        Textarea::make('employee.address')
                                            ->rows(2),
                                        TextInput::make('employee.city')
                                            ->maxLength(255),
                                        TextInput::make('employee.postcode')
                                            ->maxLength(255),
                                    ])->columns(3),
                            ]),

                        // Tab 3: Subcontractor Details (only if user_type is subcontractor)
                        Tabs\Tab::make('Subcontractor')
                            ->icon('heroicon-o-building-office-2')
                            ->visible(fn ($get) => in_array($get('user_type'), ['subcontractor_ltd', 'self_employed']))
                            ->schema([
                                Section::make('Link to Existing Subcontractor')
                                    ->description('Select an existing subcontractor or create new one below')
                                    ->schema([
                                        Select::make('subcontractor_id')
                                            ->label('Existing Subcontractor')
                                            ->relationship('subcontractor', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255),
                                                Select::make('business_type')
                                                    ->options([
                                                        'self_employed' => 'Self Employed',
                                                        'ltd' => 'Limited Company',
                                                    ])
                                                    ->required(),
                                                TextInput::make('email')
                                                    ->email(),
                                                TextInput::make('phone')
                                                    ->tel(),
                                            ])
                                            ->helperText('Link this user to an existing subcontractor record'),
                                    ]),

                                Section::make('OR Create New Subcontractor Details')
                                    ->description('Complete these fields only if creating a new subcontractor')
                                    ->collapsed()
                                    ->schema([
                                        TextEntry::make('new_subcontractor_note')
                                            ->state('new_subcontractor_note')
                                            ->columnSpanFull(),
                                        TextInput::make('new_subcontractor.name')
                                            ->label('Company Name')
                                            ->maxLength(255),
                                        Select::make('new_subcontractor.relationship_type')
                                            ->label('Relationship Type')
                                            ->options([
                                                'direct' => 'Direct',
                                                'indirect' => 'Indirect',
                                            ])
                                            ->default('direct'),
                                        Select::make('new_subcontractor.business_type')
                                            ->label('Business Type')
                                            ->options([
                                                'self_employed' => 'Self Employed',
                                                'ltd' => 'Limited Company',
                                            ]),
                                        TextInput::make('new_subcontractor.registration_number')
                                            ->label('Company Registration Number')
                                            ->maxLength(255),
                                        TextInput::make('new_subcontractor.vat_number')
                                            ->label('VAT Number')
                                            ->maxLength(255),
                                        TextInput::make('new_subcontractor.email')
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('new_subcontractor.phone')
                                            ->tel()
                                            ->maxLength(255),
                                        Textarea::make('new_subcontractor.address')
                                            ->rows(2),
                                        TextInput::make('new_subcontractor.city')
                                            ->maxLength(255),
                                        TextInput::make('new_subcontractor.postcode')
                                            ->maxLength(255),
                                    ])->columns(2),
                            ]),

                        // Tab 4: Worker Details & Specialties
                        Tab::make('Worker Profile')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->schema([
                                Section::make('Worker Details')
                                    ->description('Define this user as a worker with trades/specialties')
                                    ->schema([
                                        Toggle::make('create_worker')
                                            ->label('Create Worker Profile')
                                            ->helperText('Enable to create a worker profile for this user')
                                            ->reactive()
                                            ->default(false)
                                            ->columnSpanFull(),

                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('worker.first_name')
                                                    ->label('First Name')
                                                    ->required(fn ($get) => $get('create_worker'))
                                                    ->maxLength(255),
                                                TextInput::make('worker.last_name')
                                                    ->label('Last Name')
                                                    ->required(fn ($get) => $get('create_worker'))
                                                    ->maxLength(255),
                                                TextInput::make('worker.email')
                                                    ->email()
                                                    ->maxLength(255),
                                                TextInput::make('worker.phone')
                                                    ->tel()
                                                    ->maxLength(255),
                                                Select::make('worker.worker_type')
                                                    ->label('Worker Type')
                                                    ->options([
                                                        'employee' => 'Employee',
                                                        'self_employed' => 'Self Employed',
                                                        'ltd' => 'LTD Company',
                                                    ])
                                                    ->default(fn ($get) => match($get('user_type')) {
                                                        'employee' => 'employee',
                                                        'self_employed' => 'self_employed',
                                                        'subcontractor_ltd' => 'ltd',
                                                        default => 'employee',
                                                    }),
                                                Select::make('worker.status')
                                                    ->options([
                                                        'active' => 'Active',
                                                        'inactive' => 'Inactive',
                                                        'suspended' => 'Suspended',
                                                    ])
                                                    ->default('active'),
                                            ])
                                            ->visible(fn ($get) => $get('create_worker')),
                                    ]),

                                Section::make('Trades & Specialties')
                                    ->description('Select all trades/skills this worker can perform')
                                    ->schema([
                                        CheckboxList::make('worker.trades')
                                            ->label('Trades & Specialties')
                                            ->options([
                                                'data_cabling' => 'Data Cabling Engineer',
                                                'fibre_optic' => 'Fibre Optic Specialist',
                                                'structured_cabling' => 'Structured Cabling',
                                                'network_installation' => 'Network Installation',
                                                'electrician' => 'Electrician',
                                                'electrical_installation' => 'Electrical Installation',
                                                'fire_alarm' => 'Fire Alarm Specialist',
                                                'fire_alarm_installation' => 'Fire Alarm Installation',
                                                'security_systems' => 'Security Systems',
                                                'cctv' => 'CCTV Installation',
                                                'access_control' => 'Access Control Systems',
                                                'av_systems' => 'Audio Visual Systems',
                                                'sound_systems' => 'Sound Systems',
                                                'building_management' => 'Building Management Systems',
                                                'hvac' => 'HVAC Technician',
                                                'plumber' => 'Plumber',
                                                'general_construction' => 'General Construction',
                                                'ceiling_installation' => 'Ceiling Installation',
                                                'containment_installation' => 'Containment Installation',
                                                'testing_commissioning' => 'Testing & Commissioning',
                                                'project_management' => 'Project Management',
                                                'site_supervisor' => 'Site Supervisor',
                                            ])
                                            ->columns(3)
                                            ->searchable()
                                            ->bulkToggleable(),
                                    ])
                                    ->visible(fn ($get) => $get('create_worker')),
                            ]),

                        // Tab 5: Rates
                        Tabs\Tab::make('Rates')
                            ->icon('heroicon-o-currency-pound')
                            ->schema([
                                Section::make('Worker Rates')
                                    ->description('Define multiple rate types for this worker')
                                    ->schema([
                                        TextEntry::make('rates_info')
                                            ->state('rates_info')
                                            ->columnSpanFull(),

                                        Repeater::make('worker_rates')
                                            ->label('Rate Configurations')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Rate Name')
                                                    ->required()
                                                    ->placeholder('e.g., Standard Hourly, Night Shift, Weekend')
                                                    ->columnSpan(2),

                                                Select::make('rate_type')
                                                    ->options([
                                                        'hourly' => 'Hourly',
                                                        'daily' => 'Daily',
                                                        'nightly' => 'Night Shift',
                                                        'shift' => 'Per Shift',
                                                        'custom' => 'Custom',
                                                    ])
                                                    ->required()
                                                    ->reactive(),

                                                TextInput::make('rate_amount')
                                                    ->label('Rate Amount')
                                                    ->numeric()
                                                    ->prefix('£')
                                                    ->required()
                                                    ->step(0.01)
                                                    ->minValue(0),

                                                Select::make('currency')
                                                    ->options([
                                                        'GBP' => 'GBP (£)',
                                                        'EUR' => 'EUR (€)',
                                                        'USD' => 'USD ($)',
                                                    ])
                                                    ->default('GBP'),

                                                DatePicker::make('valid_from')
                                                    ->label('Valid From')
                                                    ->default(now()),

                                                DatePicker::make('valid_until')
                                                    ->label('Valid Until')
                                                    ->after('valid_from'),

                                                Toggle::make('is_active')
                                                    ->label('Active')
                                                    ->default(true)
                                                    ->inline(false),

                                                Textarea::make('description')
                                                    ->label('Notes')
                                                    ->rows(2)
                                                    ->placeholder('Additional details about this rate...')
                                                    ->columnSpanFull(),

                                                Hidden::make('rateable_type')
                                                    ->default(Worker::class),
                                            ])
                                            ->columns(4)
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Rate')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                $state['name'] ?? 'New Rate'
                                            )
                                            ->reorderable()
                                            ->cloneable()
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Quick Rate Templates')
                                    ->description('Pre-fill common rate configurations')
                                    ->collapsed()
                                    ->schema([
                                        Actions::make([
                                            Action::make('add_standard_rates')
                                                ->label('Add Standard Rate Set')
                                                ->icon('heroicon-o-plus-circle')
                                                ->action(function (Set $set, Get $get) {
                                                    $existingRates = $get('worker_rates') ?? [];
                                                    $newRates = [
                                                        [
                                                            'name' => 'Standard Day Rate',
                                                            'rate_type' => 'hourly',
                                                            'rate_amount' => 15.00,
                                                            'currency' => 'GBP',
                                                            'valid_from' => now()->format('Y-m-d'),
                                                            'is_active' => true,
                                                            'rateable_type' => Worker::class,
                                                        ],
                                                        [
                                                            'name' => 'Night Shift Premium',
                                                            'rate_type' => 'nightly',
                                                            'rate_amount' => 20.00,
                                                            'currency' => 'GBP',
                                                            'valid_from' => now()->format('Y-m-d'),
                                                            'is_active' => true,
                                                            'rateable_type' => Worker::class,
                                                        ],
                                                        [
                                                            'name' => 'Weekend Rate',
                                                            'rate_type' => 'hourly',
                                                            'rate_amount' => 22.50,
                                                            'currency' => 'GBP',
                                                            'valid_from' => now()->format('Y-m-d'),
                                                            'is_active' => true,
                                                            'rateable_type' => Worker::class,
                                                        ],
                                                    ];
                                                    $set('worker_rates', array_merge($existingRates, $newRates));
                                                })
                                                ->color('success'),

                                            Action::make('add_supervisor_rates')
                                                ->label('Add Supervisor Rate Set')
                                                ->icon('heroicon-o-user-plus')
                                                ->action(function (Set $set, Get $get) {
                                                    $existingRates = $get('worker_rates') ?? [];
                                                    $newRates = [
                                                        [
                                                            'name' => 'Supervisor Day Rate',
                                                            'rate_type' => 'hourly',
                                                            'rate_amount' => 25.00,
                                                            'currency' => 'GBP',
                                                            'valid_from' => now()->format('Y-m-d'),
                                                            'is_active' => true,
                                                            'rateable_type' => Worker::class,
                                                        ],
                                                        [
                                                            'name' => 'Supervisor Night Rate',
                                                            'rate_type' => 'nightly',
                                                            'rate_amount' => 30.00,
                                                            'currency' => 'GBP',
                                                            'valid_from' => now()->format('Y-m-d'),
                                                            'is_active' => true,
                                                            'rateable_type' => Worker::class,
                                                        ],
                                                    ];
                                                    $set('worker_rates', array_merge($existingRates, $newRates));
                                                })
                                                ->color('info'),
                                        ])->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }
    protected static function rateFields(): array
    {
        return [
            Select::make('shift_type')
                ->options([
                    'day'     => 'Day',
                    'night'   => 'Night',
                    'weekend' => 'Weekend',
                ])->required(),
            TextInput::make('amount')->numeric()->prefix('£')->required(),
            Select::make('currency')->options(['GBP' => 'GBP', 'EUR' => 'EUR'])->default('GBP'),
        ];
    }
}
