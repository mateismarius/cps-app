<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Run roles and permissions first
        $this->call([
            // 1. Baza - Companies și Users
            CompanySeeder::class,
            UserSeeder::class,

            // 2. Clients și Engineers (depind de companies și users)
            ClientSeeder::class,
            EngineerSeeder::class,

            // 3. Projects (depinde de companies și clients)
            ProjectSeeder::class,

            // 4. Schedules (depinde de projects și engineers)
            ScheduleSeeder::class,

            // 5. Timesheets (depinde de schedules)
            TimesheetSeeder::class,

            // 6. Reports (depinde de timesheets)
            ReportSeeder::class,
        ]);

    }
}
