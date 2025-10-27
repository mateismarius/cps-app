<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // --- Create users ---
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
            User::firstOrCreate(['email' => $userData['email']], $userData);
        }

        // --- Create Roles ---
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $engineerRole = Role::firstOrCreate(['name' => 'engineer']);

        // Assign all permissions to super_admin
        $allPermissions = Permission::all();
        if ($allPermissions->count() > 0) {
            $superAdminRole->syncPermissions($allPermissions);
        }

        // --- Create Super Admin user ---
        $superAdmin = User::firstOrCreate(
            ['email' => 'marius@cpsnetwork.co.uk'],
            [
                'name' => 'Marius Matei',
                'password' => Hash::make('it.master69'),
                'email_verified_at' => now(),
            ]
        );

        $superAdmin->assignRole($superAdminRole);
        $superAdmin->givePermissionTo(Permission::all());

        // --- Assign Engineer role to selected users ---
        $engineerEmails = [
            'james.wilson@tvc-ltd.co.uk',
            'tom.harris@tvc-ltd.co.uk',
            'peter.davies@nes-ltd.co.uk',
            'mark.johnson@nes-ltd.co.uk',
            'john.smith@contractor.uk',
            'michael.brown@engineer.uk',
        ];

        foreach ($engineerEmails as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->assignRole($engineerRole);
            }
        }

        $this->command->info('✅ Super Admin & Engineer roles created and users assigned successfully!');
    }
}
