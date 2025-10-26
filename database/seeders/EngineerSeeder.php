<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Engineer;
use App\Models\User;
use App\Models\Trade;
use Illuminate\Database\Seeder;

class EngineerSeeder extends Seeder
{
    public function run(): void
    {
        // Creează trades dacă nu există
        $trades = [
            ['name' => 'Electrician', 'description' => 'Electrical installations and maintenance', 'active' => true],
            ['name' => 'Plumber', 'description' => 'Plumbing and heating systems', 'active' => true],
            ['name' => 'HVAC Technician', 'description' => 'Heating, ventilation and air conditioning', 'active' => true],
            ['name' => 'General Builder', 'description' => 'General construction work', 'active' => true],
            ['name' => 'Carpenter', 'description' => 'Carpentry and joinery', 'active' => true],
        ];

        foreach ($trades as $trade) {
            Trade::firstOrCreate(['name' => $trade['name']], $trade);
        }

        $mainCompany = Company::where('type', 'main')->first();

        // Engineers from LTD Companies
        $ltdCompanies = Company::where('type', 'ltd')->get();

        foreach ($ltdCompanies as $company) {
            $companyUsers = User::whereIn('email', [
                'james.wilson@tvc-ltd.co.uk',
                'tom.harris@tvc-ltd.co.uk',
                'william.jones@tvc-ltd.co.uk',
            ])->get();

            if ($company->name === 'Thames Valley Contractors Ltd') {
                $this->createEngineersForCompany($company, $companyUsers, $mainCompany);
            }

            $companyUsers = User::whereIn('email', [
                'peter.davies@nes-ltd.co.uk',
                'mark.johnson@nes-ltd.co.uk',
                'oliver.white@nes-ltd.co.uk',
            ])->get();

            if ($company->name === 'Northern Engineering Solutions Ltd') {
                $this->createEngineersForCompany($company, $companyUsers, $mainCompany);
            }

            $companyUsers = User::whereIn('email', [
                'andrew.macdonald@sts-ltd.co.uk',
                'craig.robertson@sts-ltd.co.uk',
                'george.martin@sts-ltd.co.uk',
            ])->get();

            if ($company->name === 'Scottish Technical Services Ltd') {
                $this->createEngineersForCompany($company, $companyUsers, $mainCompany);
            }
        }

        // Self-Employed Engineers (doar 1 per company)
        $selfEmployedCompanies = Company::where('type', 'self-employed')->get();

        foreach ($selfEmployedCompanies as $company) {
            $user = User::where('email', 'like', '%' . strtolower(str_replace(' ', '.', explode(' ', $company->contact_person)[0])) . '%')->first();

            if ($user) {
                Engineer::create([
                    'company_id' => $company->id,
                    'user_id' => $user->id,
                    'trade_id' => Trade::inRandomOrder()->first()->id,
                    'direct_to_main' => true, // Self-employed lucrează direct cu main
                    'rate_to_subcontractor' => null, // N/A for self-employed
                    'rate_to_main' => rand(250, 400) + (rand(0, 99) / 100), // £250-400/day
                    'active' => true,
                ]);
            }
        }
    }

    private function createEngineersForCompany($company, $users, $mainCompany)
    {
        $isDirect = rand(0, 1) === 1; // Random dacă lucrează direct cu main sau prin LTD

        foreach ($users as $user) {
            Engineer::create([
                'company_id' => $company->id,
                'user_id' => $user->id,
                'trade_id' => Trade::inRandomOrder()->first()->id,
                'direct_to_main' => $isDirect,
                'rate_to_subcontractor' => $isDirect ? null : (rand(200, 350) + (rand(0, 99) / 100)),
                'rate_to_main' => rand(250, 450) + (rand(0, 99) / 100),
                'active' => true,
            ]);
        }
    }
}
