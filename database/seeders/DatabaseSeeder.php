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
            RolesAndPermissionsSeeder::class,
        ]);

        // Create demo user
        $user = \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@cpsnetwork.com',
        ]);

        $user->assignRole('super_admin');

        // Seed company data
        \App\Models\Company::create([
            'name' => 'CPS Network Communications',
            'trading_name' => 'CPS Network',
            'registration_number' => '12345678',
            'vat_number' => 'GB123456789',
            'email' => 'info@cpsnetwork.com',
            'phone' => '+44 20 1234 5678',
            'address' => '123 Business Street',
            'city' => 'London',
            'postcode' => 'E1 6AN',
            'country' => 'UK',
            'status' => 'active',
        ]);
    }
}
