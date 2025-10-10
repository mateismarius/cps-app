<?php

// app/Observers/TimesheetObserver.php

namespace App\Observers;

use App\Models\Timesheet;
use App\Models\Project;
use App\Notifications\TimesheetApprovedNotification;

class TimesheetObserver
{
    /**
     * Handle the Timesheet "created" event.
     */
    public function created(Timesheet $timesheet): void
    {
        $this->updateProjectCost($timesheet->project);
    }

    /**
     * Handle the Timesheet "updated" event.
     */
    public function updated(Timesheet $timesheet): void
    {
        // If timesheet was just approved, notify the worker
        if ($timesheet->isDirty('status') && $timesheet->status === 'approved') {
            if ($timesheet->worker->employee && $timesheet->worker->employee->user) {
                $timesheet->worker->employee->user->notify(
                    new TimesheetApprovedNotification($timesheet)
                );
            }
        }

        $this->updateProjectCost($timesheet->project);
    }

    /**
     * Handle the Timesheet "deleted" event.
     */
    public function deleted(Timesheet $timesheet): void
    {
        $this->updateProjectCost($timesheet->project);
    }

    /**
     * Update project actual cost
     */
    protected function updateProjectCost(Project $project): void
    {
        $totalCost = $project->timesheets()
            ->where('status', 'approved')
            ->get()
            ->sum(fn ($timesheet) => $timesheet->calculateAmount());

        $project->update(['actual_cost' => $totalCost]);
    }


}
