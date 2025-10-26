<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Timesheet;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TimesheetController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        $today = now()->format('Y-m-d');

        // Get engineer's schedules for current month
        $schedules = Schedule::with('project')
            ->where('engineer_id', $user->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn($schedule) => [
                'id' => $schedule->id,
                'date' => $schedule->date,
                'project' => [
                    'id' => $schedule->project->id,
                    'name' => $schedule->project->name,
                ],
                'location' => $schedule->location,
                'notes' => $schedule->notes,
            ]);

        // Get existing timesheets for current month
        $timesheets = Timesheet::with('project', 'schedule')
            ->where('engineer_id', $user->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn($timesheet) => [
                'id' => $timesheet->id,
                'date' => $timesheet->date,
                'project' => [
                    'id' => $timesheet->project->id,
                    'name' => $timesheet->project->name,
                ],
                'schedule' => $timesheet->schedule ? [
                    'id' => $timesheet->schedule->id,
                ] : null,
                'notes' => $timesheet->notes,
                'approved' => $timesheet->approved,
            ]);

        // Get all active projects for exceptional cases
        $projects = Project::where('status', 'active')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('engineer/timesheet/index', [
            'schedules' => $schedules,
            'timesheets' => $timesheets,
            'projects' => $projects,
            'today' => $today,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'schedule_id' => 'nullable|exists:schedules,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();

        // Check if timesheet already exists for this date
        $existingTimesheet = Timesheet::where('engineer_id', $user->id)
            ->where('date', $validated['date'])
            ->first();

        if ($existingTimesheet) {
            return back()->with('error', 'Timesheet already exists for this date.');
        }

        Timesheet::create([
            'engineer_id' => $user->id,
            'project_id' => $validated['project_id'],
            'schedule_id' => $validated['schedule_id'],
            'date' => $validated['date'],
            'notes' => $validated['notes'],
            'approved' => false,
        ]);

        return back()->with('success', 'Timesheet submitted successfully.');
    }

    public function update(Request $request, Timesheet $timesheet)
    {
        // Check if timesheet belongs to current user and is not approved
        if ($timesheet->engineer_id !== auth()->id()) {
            abort(403);
        }

        if ($timesheet->approved) {
            return back()->with('error', 'Cannot edit approved timesheet.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $timesheet->update($validated);

        return back()->with('success', 'Timesheet updated successfully.');
    }
}
