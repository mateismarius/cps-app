<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Company;
use App\Models\Client;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $mainCompany = Company::where('type', 'main')->first();
        $clients = Client::all();

        if (!$mainCompany || $clients->isEmpty()) {
            $this->command->error('Main company or clients not found!');
            return;
        }

        $projects = [
            // Active Projects
            [
                'main_company_id' => $mainCompany->id,
                'client_id' => $clients->where('name', 'Tesco Stores Ltd')->first()->id,
                'name' => 'Tesco Birmingham - HVAC Installation',
                'description' => 'Complete HVAC system installation and commissioning for new store',
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(45),
                'status' => 'active',
                'billing_type' => 'shifts',
            ],
            [
                'main_company_id' => $mainCompany->id,
                'client_id' => $clients->where('name', 'Sainsbury\'s Supermarkets Ltd')->first()->id,
                'name' => 'Sainsbury\'s Manchester - Electrical Upgrade',
                'description' => 'Store-wide electrical system upgrade and LED lighting installation',
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(30),
                'status' => 'active',
                'billing_type' => 'shifts',
            ],
            [
                'main_company_id' => $mainCompany->id,
                'client_id' => $clients->where('name', 'Westfield Shopping Centres')->first()->id,
                'name' => 'Westfield London - Retail Unit Fit-Out',
                'description' => 'Complete fit-out of 5 retail units including electrical, plumbing, and HVAC',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(60),
                'status' => 'active',
                'billing_type' => 'shifts',
            ],
            [
                'main_company_id' => $mainCompany->id,
                'client_id' => $clients->where('name', 'John Lewis Partnership')->first()->id,
                'name' => 'John Lewis Edinburgh - Maintenance Contract',
                'description' => 'Ongoing maintenance and emergency repairs',
                'start_date' => Carbon::now()->subDays(90),
                'end_date' => Carbon::now()->addMonths(6),
                'status' => 'active',
                'billing_type' => 'shifts',
            ],

            // pending Projects
            [
                'main_company_id' => $mainCompany->id,
                'client_id' => $clients->where('name', 'Marks & Spencer')->first()->id,
                'name' => 'M&S Bristol - Store Refurbishment',
                'description' => 'Complete store refurbishment including mechanical and electrical works',
                'start_date' => Carbon::now()->addDays(14),
                'end_date' => Carbon::now()->addDays(90),
                'status' => 'pending',
                'billing_type' => 'shifts',
            ],
            [
                'main_company_id' => $mainCompany->id,
                'client_id' => $clients->where('name', 'Asda Stores Ltd')->first()->id,
                'name' => 'Asda Leeds - Car Park Lighting',
                'description' => 'Installation of new LED lighting system in car park',
                'start_date' => Carbon::now()->addDays(21),
                'end_date' => Carbon::now()->addDays(35),
                'status' => 'pending',
                'billing_type' => 'fixed',
            ],

            // Completed Project
            [
                'main_company_id' => $mainCompany->id,
                'client_id' => $clients->where('name', 'British Retail Consortium')->first()->id,
                'name' => 'BRC Head Office - Office Refit',
                'description' => 'Complete office refurbishment with new electrical and HVAC systems',
                'start_date' => Carbon::now()->subDays(120),
                'end_date' => Carbon::now()->subDays(15),
                'status' => 'completed',
                'billing_type' => 'shifts',
            ],

            // More active projects for volume
            [
                'main_company_id' => $mainCompany->id,
                'client_id' => $clients->random()->id,
                'name' => 'Emergency Electrical Works - Multiple Sites',
                'description' => 'Emergency electrical maintenance across 10 locations',
                'start_date' => Carbon::now()->subDays(3),
                'end_date' => Carbon::now()->addDays(10),
                'status' => 'active',
                'billing_type' => 'shifts',
            ],
            [
                'main_company_id' => $mainCompany->id,
                'client_id' => $clients->random()->id,
                'name' => 'Plumbing Upgrades - Central London',
                'description' => 'Plumbing system upgrades and water efficiency improvements',
                'start_date' => Carbon::now()->subDays(7),
                'end_date' => Carbon::now()->addDays(20),
                'status' => 'active',
                'billing_type' => 'shifts',
            ],
            [
                'main_company_id' => $mainCompany->id,
                'client_id' => $clients->random()->id,
                'name' => 'Fire Alarm System Installation',
                'description' => 'New fire alarm and emergency lighting system',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(25),
                'status' => 'active',
                'billing_type' => 'shifts',
            ],
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }
    }
}
