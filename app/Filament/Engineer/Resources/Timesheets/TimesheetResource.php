<?php

namespace App\Filament\Engineer\Resources\Timesheets;

use App\Filament\Engineer\Resources\Timesheets\Pages\CreateTimesheet;
use App\Filament\Engineer\Resources\Timesheets\Pages\EditTimesheet;
use App\Filament\Engineer\Resources\Timesheets\Pages\ListTimesheets;
use App\Filament\Engineer\Resources\Timesheets\Schemas\TimesheetForm;
use App\Filament\Engineer\Resources\Timesheets\Tables\TimesheetsTable;
use App\Models\Timesheet;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;
    protected static ?string $navigationLabel = 'Timesheets';

    protected static ?string $recordTitleAttribute = 'Timesheet';

    public static function form(Schema $schema): Schema
    {
        return TimesheetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TimesheetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTimesheets::route('/'),
            'create' => CreateTimesheet::route('/create'),
            'edit' => EditTimesheet::route('/{record}/edit'),
        ];
    }
}
