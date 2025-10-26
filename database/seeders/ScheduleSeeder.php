<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\Project;
use App\Models\Engineer;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $activeProjects = Project::where('status', 'active')->get();
        $engineers = Engineer::where('active', true)->with('user')->get();

        if ($activeProjects->isEmpty() || $engineers->isEmpty()) {
            $this->command->error('No active projects or engineers found!');
            return;
        }

        // Generează schedules pentru ultimele 7 zile și următoarele 14 zile
        $startDate = Carbon::now()->subDays(7);
        $endDate = Carbon::now()->addDays(14);

        $locations = [
            'Main Site - Ground Floor',
            'Main Site - First Floor',
            'Building A',
            'Building B',
            'Car Park Area',
            'Warehouse Section',
            'Office Block',
            'Retail Area',
        ];

        foreach ($activeProjects as $project) {
            // Câți ingineri pe zi pentru acest proiect (2-4)
            $engineersPerDay = rand(2, 4);

            // Selectează ingineri pentru acest proiect
            $projectEngineers = $engineers->random(min($engineersPerDay, $engineers->count()));

            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                // Skip weekends (opțional)
                if ($currentDate->isWeekend() && rand(0, 3) > 0) {
                    $currentDate->addDay();
                    continue;
                }

                // Shuffle engineers pentru varietate
                $dailyEngineers = $projectEngineers->shuffle()->take(rand(2, $engineersPerDay));

                foreach ($dailyEngineers as $engineer) {
                    // Verifică dacă engineerul nu e deja programat în acel proiect în acea zi
                    $exists = Schedule::where('project_id', $project->id)
                        ->where('engineer_id', $engineer->user_id)
                        ->whereDate('date', $currentDate)
                        ->exists();

                    if (!$exists) {

                        Schedule::create([
                            'project_id' => $project->id,
                            'engineer_id' => $engineer->user_id,
                            'date' => $currentDate->format('Y-m-d'),
                            'location' => $locations[array_rand($locations)],
                            'notes' => rand(0, 4) > 0 ? null : 'Special requirements: ' . ['PPE required', 'Access restricted', 'Client liaison needed', 'Equipment delivery'][rand(0, 3)],
                        ]);
                    }
                }

                $currentDate->addDay();
            }
        }

        $this->command->info('Schedules created successfully!');
    }
}
