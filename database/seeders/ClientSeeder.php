<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $mainCompany = Company::where('type', 'main')->first();

        if (!$mainCompany) {
            $this->command->error('Main company not found!');
            return;
        }

        $clients = [
            [
                'main_company_id' => $mainCompany->id,
                'name' => 'Tesco Stores Ltd',
                'contact_person' => 'Emma Roberts',
                'email' => 'facilities@tesco.com',
                'phone' => '+44 800 505 555',
                'address' => 'Tesco House, Shire Park, Welwyn Garden City, AL7 1GA',
                'default_rate' => 450.00, // £450/day - Main taxează client cu £450
                'active' => true,
            ],
            [
                'main_company_id' => $mainCompany->id,
                'name' => 'Sainsbury\'s Supermarkets Ltd',
                'contact_person' => 'Richard Green',
                'email' => 'maintenance@sainsburys.co.uk',
                'phone' => '+44 20 7695 6000',
                'address' => '33 Holborn, London, EC1N 2HT',
                'default_rate' => 425.00,
                'active' => true,
            ],
            [
                'main_company_id' => $mainCompany->id,
                'name' => 'British Retail Consortium',
                'contact_person' => 'Helen Davies',
                'email' => 'facilities@brc.org.uk',
                'phone' => '+44 20 7854 8900',
                'address' => '21 Dartmouth Street, London, SW1H 9BP',
                'default_rate' => 500.00,
                'active' => true,
            ],
            [
                'main_company_id' => $mainCompany->id,
                'name' => 'Westfield Shopping Centres',
                'contact_person' => 'Daniel Foster',
                'email' => 'operations@westfield.com',
                'phone' => '+44 20 8222 3333',
                'address' => 'Westfield London, Ariel Way, London, W12 7GF',
                'default_rate' => 550.00,
                'active' => true,
            ],
            [
                'main_company_id' => $mainCompany->id,
                'name' => 'John Lewis Partnership',
                'contact_person' => 'Victoria Turner',
                'email' => 'property@johnlewis.co.uk',
                'phone' => '+44 20 7828 1000',
                'address' => '171 Victoria Street, London, SW1E 5NN',
                'default_rate' => 475.00,
                'active' => true,
            ],
            [
                'main_company_id' => $mainCompany->id,
                'name' => 'Marks & Spencer',
                'contact_person' => 'Christopher Wright',
                'email' => 'facilities@marksandspencer.com',
                'phone' => '+44 20 7935 4422',
                'address' => 'Waterside House, 35 North Wharf Road, London, W2 1NW',
                'default_rate' => 460.00,
                'active' => true,
            ],
            [
                'main_company_id' => $mainCompany->id,
                'name' => 'Asda Stores Ltd',
                'contact_person' => 'Laura Phillips',
                'email' => 'property@asda.co.uk',
                'phone' => '+44 113 243 5435',
                'address' => 'Asda House, Southbank, Great Wilson Street, Leeds, LS11 5AD',
                'default_rate' => 420.00,
                'active' => true,
            ],
        ];

        foreach ($clients as $clientData) {
            Client::create($clientData);
        }
    }
}
