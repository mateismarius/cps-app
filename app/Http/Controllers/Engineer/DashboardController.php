<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the engineer dashboard
     */
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;

        // Get upcoming shifts (next 7 days)
        $upcomingShifts = Schedule::where('engineer_id', $userId)
            ->where('date', '>=', now())
            ->where('date', '<=', now()->addDays(7))
            ->with('project:id,name')
            ->orderBy('date')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'project' => [
                        'name' => $schedule->project->name,
                    ],
                    'date' => $schedule->date->format('d M Y'),
                    'role' => $schedule->role,
                    'status' => 'upcoming',
                ];
            });

        // Get pending reports (past shifts without timesheets)
        $pendingReports = Schedule::where('engineer_id', $userId)
            ->where('date', '<', now())
            ->whereDoesntHave('timesheets')
            ->with('project:id,name')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'project' => [
                        'name' => $schedule->project->name,
                    ],
                    'date' => $schedule->date->format('d M Y'),
                    'role' => $schedule->role,
                ];
            });

        // Calculate stats
        $stats = [
            'upcomingShifts' => Schedule::where('engineer_id', $userId)
                ->where('date', '>=', now())
                ->where('date', '<=', now()->addDays(7))
                ->count(),

            'shiftsThisMonth' => Schedule::where('engineer_id', $userId)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),

            'pendingReports' => Schedule::where('engineer_id', $userId)
                ->where('date', '<', now())
                ->whereDoesntHave('timesheets')
                ->count(),

            'earnings' => $this->calculateMonthlyEarnings($userId),
        ];

        return Inertia::render('engineer/dashboard', [
            'upcomingShifts' => $upcomingShifts,
            'pendingReports' => $pendingReports,
            'stats' => $stats,
        ]);
    }

    /**
     * Calculate monthly earnings based on completed shifts
     */
    private function calculateMonthlyEarnings(int $userId): string
    {
        // Get the engineer's rate and calculate earnings
        // This is a simplified example - adjust based on your business logic
        $completedShifts = Schedule::where('engineer_id', $userId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->whereHas('timesheets')
            ->count();

        // Assuming a fixed rate - replace with actual rate from engineers table
        $engineer = \App\Models\Engineer::where('user_id', $userId)->first();
        $rate = $engineer ? $engineer->rate_to_main : 0;

        $earnings = $completedShifts * $rate;

        return number_format($earnings, 2);
    }
}
