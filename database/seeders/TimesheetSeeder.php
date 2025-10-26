<?php

namespace Database\Seeders;

use App\Models\Timesheet;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TimesheetSeeder extends Seeder
{
    public function run(): void
    {
        // Creează timesheets doar pentru schedules din trecut și azi
        $pastSchedules = Schedule::where('date', '<=', Carbon::now())
            ->get();

        if ($pastSchedules->isEmpty()) {
            $this->command->warn('No past schedules found!');
            return;
        }

        foreach ($pastSchedules as $schedule) {
            // 80% șanse să existe timesheet (unii ingineri uită să completeze)
            if (rand(1, 10) <= 8) {
                Timesheet::create([
                    'project_id' => $schedule->project_id,
                    'schedule_id' => $schedule->id,
                    'engineer_id' => $schedule->engineer_id,
                    'date' => $schedule->date,
                    'approved' => $this->shouldBeApproved($schedule->date),
                ]);
            }
        }

        $this->command->info('Timesheets created successfully!');
    }

    private function shouldBeApproved($date): bool
    {
        $daysAgo = Carbon::parse($date)->diffInDays(Carbon::now());

        // Timesheets mai vechi de 3 zile au șanse mai mari să fie aprobate
        if ($daysAgo > 3) {
            return rand(1, 10) <= 9; // 90% aprobate
        } elseif ($daysAgo > 1) {
            return rand(1, 10) <= 6; // 60% aprobate
        } else {
            return rand(1, 10) <= 2; // 20% aprobate (recent)
        }
    }
}
