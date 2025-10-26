<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Managers/Admin
            [
                'name' => 'David Thompson',
                'email' => 'david@bes-uk.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sarah Mitchell',
                'email' => 'sarah@bes-uk.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],

            // Engineers from LTD companies
            [
                'name' => 'James Wilson',
                'email' => 'james.wilson@tvc-ltd.co.uk',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Tom Harris',
                'email' => 'tom.harris@tvc-ltd.co.uk',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Peter Davies',
                'email' => 'peter.davies@nes-ltd.co.uk',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Mark Johnson',
                'email' => 'mark.johnson@nes-ltd.co.uk',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Andrew MacDonald',
                'email' => 'andrew.macdonald@sts-ltd.co.uk',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Craig Robertson',
                'email' => 'craig.robertson@sts-ltd.co.uk',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],

            // Self-Employed Engineers
            [
                'name' => 'John Smith',
                'email' => 'john.smith@contractor.uk',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@engineer.uk',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'robert.taylor@trades.uk',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],

            // Extra Engineers
            [
                'name' => 'William Jones',
                'email' => 'william.jones@tvc-ltd.co.uk',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Oliver White',
                'email' => 'oliver.white@nes-ltd.co.uk',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'George Martin',
                'email' => 'george.martin@sts-ltd.co.uk',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
