<?php

// app/Helpers/RateCalculator.php

namespace App\Helpers;

use App\Models\Worker;
use App\Models\Project;
use App\Services\RateService;

class RateCalculator
{
    protected RateService $rateService;

    public function __construct(RateService $rateService)
    {
        $this->rateService = $rateService;
    }

    /**
     * Calculate total amount for timesheet
     */
    public function calculateTimesheetAmount(
        Worker $worker,
        Project $project,
        float $hours,
        string $shiftType = 'day'
    ): float {
        $rate = $this->rateService->getApplicableRate($worker, $project, $shiftType);

        if (!$rate) {
            return 0;
        }

        return match($rate->rate_type) {
            'hourly' => $hours * $rate->rate_amount,
            'daily', 'nightly', 'shift' => $rate->rate_amount,
            default => 0,
        };
    }

    /**
     * Calculate weekly earnings for a worker
     */
    public function calculateWeeklyEarnings(Worker $worker, $weekStart = null): float
    {
        $weekStart = $weekStart ?? now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        return $this->rateService->calculateWorkerEarnings($worker, $weekStart, $weekEnd);
    }

    /**
     * Calculate project labor cost to date
     */
    public function calculateProjectLaborCost(Project $project): float
    {
        return $project->timesheets()
            ->where('status', 'approved')
            ->get()
            ->sum(fn ($timesheet) => $timesheet->calculateAmount());
    }
}
