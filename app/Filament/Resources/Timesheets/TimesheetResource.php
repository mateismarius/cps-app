<?php

namespace App\Filament\Resources\Timesheets;

use App\Filament\Resources\Timesheets\Pages\CreateTimesheet;
use App\Filament\Resources\Timesheets\Pages\EditTimesheet;
use App\Filament\Resources\Timesheets\Pages\ListTimesheets;
use App\Filament\Resources\Timesheets\Schemas\TimesheetForm;
use App\Filament\Resources\Timesheets\Tables\TimesheetsTable;
use App\Models\Rate;
use App\Models\Timesheet;
use App\Models\Worker;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;
    protected static string|null|\UnitEnum $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'Timesheet';
    protected static function calculateHours($get, $set)
    {
        $clockIn = $get('clock_in');

        $clockOut = $get('clock_out');

        if ($clockIn && $clockOut) {
            $start = \Carbon\Carbon::parse($clockIn);
            $end = \Carbon\Carbon::parse($clockOut);
            $hours = $end->diffInMinutes($start) / 60;
            $set('hours_worked', round($hours, 2));
        }
    }

    protected static function loadWorkerRates($workerId, $set)
    {
        if (!$workerId) return;

        $defaultRate = Rate::where('worker_id', $workerId)
            ->where('is_active', true)
            ->first();

        if ($defaultRate) {
            $set('rate_id', $defaultRate->id);
            $set('rate_amount', $defaultRate->rate_amount);
            $set('rate_type', $defaultRate->rate_type);
        }
    }

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

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // If user is a worker, show only their timesheets
        if (auth()->user()->can('view_own_timesheets') &&
            !auth()->user()->can('view_all_timesheets')) {
            $worker = Worker::where('employee_id', auth()->user()->employee?->id)->first();
            if ($worker) {
                $query->where('worker_id', $worker->id);
            }
        }

        return $query;
    }
}
