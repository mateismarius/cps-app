<?php

// app/Filament/Pages/Dashboard.php - Custom Dashboard

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\ProjectsChart::class,
            \App\Filament\Widgets\RecentTimesheets::class,
            \App\Filament\Widgets\UpcomingSchedule::class,
            \App\Filament\Widgets\ExpiringCertifications::class,
        ];
    }
}


