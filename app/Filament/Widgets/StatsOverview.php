<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Worker;
use App\Models\Timesheet;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $thisMonth = now()->startOfMonth();

        return [
            Stat::make('Active Projects', Project::where('status', 'active')->count())
                ->description('Currently running')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('success'),

            Stat::make('Active Workers', Worker::where('status', 'active')->count())
                ->description('Available workforce')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Pending Timesheets', Timesheet::where('status', 'submitted')->count())
                ->description('Awaiting approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Unpaid Invoices', Invoice::where('status', 'sent')->count())
                ->description('£' . number_format(
                        Invoice::where('status', 'sent')->sum('total_amount'),
                        2
                    ))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),
        ];
    }
}
