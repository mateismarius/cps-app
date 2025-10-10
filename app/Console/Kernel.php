<?php

// app/Console/Kernel.php - Add scheduled tasks

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Update certification statuses daily
        $schedule->command('certifications:update-status')
            ->daily()
            ->at('00:00');

        // Update invoice statuses daily
        $schedule->command('invoices:update-status')
            ->daily()
            ->at('01:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
