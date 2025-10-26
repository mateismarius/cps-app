<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Presupun că există deja o companie Main în DB
        $mainCompany = Company::where('type', 'main')->first();

        if (!$mainCompany) {
            // Dacă totuși nu există, o creăm
            $mainCompany = Company::create([
                'name' => 'British Engineering Services Ltd',
                'type' => 'main',
                'contact_person' => 'David Thompson',
                'email' => 'david@bes-uk.com',
                'phone' => '+44 20 7946 0958',
                'address' => '123 Victoria Street, London, SW1E 6DE, UK',
                'active' => true,
            ]);
        }

        // LTD Companies (subcontractors direct cu Main)
        $ltdCompanies = [
            [
                'parent_company_id' => $mainCompany->id,
                'name' => 'Thames Valley Contractors Ltd',
                'type' => 'ltd',
                'contact_person' => 'James Wilson',
                'email' => 'james@tvc-ltd.co.uk',
                'phone' => '+44 118 957 2345',
                'address' => 'Unit 15, Reading Business Park, Reading, RG2 6GP',
                'active' => true,
            ],
            [
                'parent_company_id' => $mainCompany->id,
                'name' => 'Northern Engineering Solutions Ltd',
                'type' => 'ltd',
                'contact_person' => 'Sarah Mitchell',
                'email' => 'sarah@nes-ltd.co.uk',
                'phone' => '+44 161 234 5678',
                'address' => '45 Deansgate, Manchester, M3 2AY',
                'active' => true,
            ],
            [
                'parent_company_id' => $mainCompany->id,
                'name' => 'Scottish Technical Services Ltd',
                'type' => 'ltd',
                'contact_person' => 'Andrew MacDonald',
                'email' => 'andrew@sts-ltd.co.uk',
                'phone' => '+44 131 556 8900',
                'address' => '78 George Street, Edinburgh, EH2 3BU',
                'active' => true,
            ],
        ];

        foreach ($ltdCompanies as $company) {
            Company::create($company);
        }

        // self_employed (direct cu Main)
        $selfEmployed = [
            [
                'parent_company_id' => $mainCompany->id,
                'name' => 'John Smith Trading',
                'type' => 'self_employed',
                'contact_person' => 'John Smith',
                'email' => 'john.smith@contractor.uk',
                'phone' => '+44 7700 900123',
                'address' => '12 Oak Avenue, Birmingham, B15 2TT',
                'active' => true,
            ],
            [
                'parent_company_id' => $mainCompany->id,
                'name' => 'Michael Brown Engineering',
                'type' => 'self_employed',
                'contact_person' => 'Michael Brown',
                'email' => 'michael.brown@engineer.uk',
                'phone' => '+44 7700 900456',
                'address' => '34 High Street, Bristol, BS1 4DJ',
                'active' => true,
            ],
            [
                'parent_company_id' => $mainCompany->id,
                'name' => 'Robert Taylor Services',
                'type' => 'self_employed',
                'contact_person' => 'Robert Taylor',
                'email' => 'robert.taylor@trades.uk',
                'phone' => '+44 7700 900789',
                'address' => '56 Station Road, Leeds, LS1 4DY',
                'active' => true,
            ],
        ];

        foreach ($selfEmployed as $company) {
            Company::create($company);
        }

        // LTD sub-LTD (un LTD care lucrează prin alt LTD)
        $firstLtd = Company::where('type', 'ltd')->first();
        if ($firstLtd) {
            Company::create([
                'parent_company_id' => $firstLtd->id,
                'name' => 'Precision Build Ltd',
                'type' => 'ltd',
                'contact_person' => 'Thomas Evans',
                'email' => 'thomas@precisionbuild.co.uk',
                'phone' => '+44 20 7123 4567',
                'address' => '90 Fleet Street, London, EC4Y 1DH',
                'active' => true,
            ]);
        }
    }
}
