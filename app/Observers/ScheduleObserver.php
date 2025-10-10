<?php

namespace App\Observers;

use App\Models\Schedule;
use App\Notifications\ScheduleCreatedNotification;

class ScheduleObserver
{
    /**
     * Handle the Schedule "created" event.
     */
    public function created(Schedule $schedule): void
    {
        // Send notification to worker if they have a user account
        if ($schedule->worker->employee && $schedule->worker->employee->user) {
            $schedule->worker->employee->user->notify(
                new ScheduleCreatedNotification($schedule)
            );
        }
    }
}
