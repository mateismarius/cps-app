<?php

// database/seeders/RolesAndPermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions grouped by module
        $permissions = [
            // Company Management
            'view_company',
            'edit_company',

            // Clients
            'view_clients',
            'create_clients',
            'edit_clients',
            'delete_clients',

            // Subcontractors
            'view_subcontractors',
            'create_subcontractors',
            'edit_subcontractors',
            'delete_subcontractors',

            // Employees
            'view_employees',
            'create_employees',
            'edit_employees',
            'delete_employees',

            // Workers
            'view_workers',
            'create_workers',
            'edit_workers',
            'delete_workers',

            // Projects
            'view_projects',
            'create_projects',
            'edit_projects',
            'delete_projects',
            'manage_project_team',

            // Timesheets
            'view_all_timesheets',
            'view_own_timesheets',
            'create_timesheets',
            'edit_timesheets',
            'approve_timesheets',
            'delete_timesheets',

            // Invoices
            'view_all_invoices',
            'view_own_invoices',
            'create_invoices',
            'edit_invoices',
            'approve_invoices',
            'delete_invoices',

            // Rates
            'view_rates',
            'create_rates',
            'edit_rates',
            'delete_rates',

            // Schedule
            'view_all_schedules',
            'view_own_schedule',
            'create_schedules',
            'edit_schedules',
            'delete_schedules',

            // Leave Management
            'view_all_leaves',
            'view_own_leaves',
            'request_leave',
            'approve_leaves',
            'delete_leaves',

            // Equipment & Materials
            'view_equipment',
            'create_equipment',
            'edit_equipment',
            'delete_equipment',
            'view_materials',
            'create_materials',
            'edit_materials',
            'delete_materials',

            // Documents & Certifications
            'view_documents',
            'upload_documents',
            'delete_documents',
            'view_certifications',
            'create_certifications',
            'edit_certifications',
            'delete_certifications',

            // Reports
            'view_reports',
            'create_shift_reports',
            'edit_shift_reports',

            // Tasks
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'delete_tasks',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin - Full access
        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Operations Manager - Manages day-to-day operations
        $operationsManager = Role::create(['name' => 'operations_manager']);
        $operationsManager->givePermissionTo([
            'view_company',
            'view_clients', 'create_clients', 'edit_clients',
            'view_subcontractors', 'create_subcontractors', 'edit_subcontractors',
            'view_employees', 'create_employees', 'edit_employees',
            'view_workers', 'create_workers', 'edit_workers',
            'view_projects', 'create_projects', 'edit_projects', 'manage_project_team',
            'view_all_timesheets', 'approve_timesheets',
            'view_all_invoices', 'create_invoices', 'approve_invoices',
            'view_rates', 'create_rates', 'edit_rates',
            'view_all_schedules', 'create_schedules', 'edit_schedules',
            'view_all_leaves', 'approve_leaves',
            'view_equipment', 'create_equipment', 'edit_equipment',
            'view_materials', 'create_materials', 'edit_materials',
            'view_documents', 'upload_documents',
            'view_certifications', 'create_certifications', 'edit_certifications',
            'view_reports', 'create_shift_reports',
            'view_tasks', 'create_tasks', 'edit_tasks',
        ]);

        // Project Manager - Manages specific projects
        $projectManager = Role::create(['name' => 'project_manager']);
        $projectManager->givePermissionTo([
            'view_company',
            'view_clients',
            'view_subcontractors',
            'view_workers',
            'view_projects', 'edit_projects', 'manage_project_team',
            'view_all_timesheets', 'approve_timesheets',
            'view_all_schedules', 'create_schedules', 'edit_schedules',
            'view_equipment', 'view_materials',
            'view_documents', 'upload_documents',
            'view_reports', 'create_shift_reports', 'edit_shift_reports',
            'view_tasks', 'create_tasks', 'edit_tasks',
        ]);

        // Project Leader/Supervisor - Team leadership on site
        $supervisor = Role::create(['name' => 'supervisor']);
        $supervisor->givePermissionTo([
            'view_projects',
            'view_workers',
            'view_all_timesheets', 'approve_timesheets',
            'view_all_schedules',
            'view_equipment', 'view_materials',
            'view_documents', 'upload_documents',
            'view_reports', 'create_shift_reports', 'edit_shift_reports',
            'view_tasks', 'edit_tasks',
        ]);

        // Worker - Basic worker access
        $worker = Role::create(['name' => 'worker']);
        $worker->givePermissionTo([
            'view_own_timesheets', 'create_timesheets',
            'view_own_schedule',
            'view_own_leaves', 'request_leave',
            'view_documents',
        ]);

        // Accountant - Financial management
        $accountant = Role::create(['name' => 'accountant']);
        $accountant->givePermissionTo([
            'view_company', 'edit_company',
            'view_clients', 'edit_clients',
            'view_subcontractors', 'edit_subcontractors',
            'view_employees', 'edit_employees',
            'view_all_timesheets',
            'view_all_invoices', 'create_invoices', 'edit_invoices', 'approve_invoices',
            'view_rates', 'create_rates', 'edit_rates',
            'view_reports',
        ]);

        // HR Manager - Human resources
        $hrManager = Role::create(['name' => 'hr_manager']);
        $hrManager->givePermissionTo([
            'view_employees', 'create_employees', 'edit_employees',
            'view_workers', 'create_workers', 'edit_workers',
            'view_all_leaves', 'approve_leaves',
            'view_certifications', 'create_certifications', 'edit_certifications',
            'view_documents', 'upload_documents',
        ]);

        // Subcontractor - Limited access for external contractors
        $subcontractor = Role::create(['name' => 'subcontractor']);
        $subcontractor->givePermissionTo([
            'view_own_timesheets', 'create_timesheets',
            'view_own_invoices', 'create_invoices',
            'view_own_schedule',
            'view_certifications', 'create_certifications',
        ]);
    }
}

// Don't forget to call this seeder in DatabaseSeeder.php:
// $this->call(RolesAndPermissionsSeeder::class);
