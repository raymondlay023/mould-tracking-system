<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionsByRole = [
            'Admin' => [
                'view_admin_panel',
                'manage_users',
                'manage_plants',
                'manage_zones',
                'manage_machines',
                'import_data',
                'view_audit_logs',
                'delete_moulds',
            ],
            'Production' => [
                'view_main_dashboard',
                'view_production_section',
                'access_operations',
                'manage_trials',
                'manage_setups',
                'close_runs',
                'manage_moulds',
                'create_maintenance_events',
                'move_locations',
            ],
            'Maintenance' => [
                'view_main_dashboard',
                'view_maintenance_section',
                'access_operations',
                'manage_setups',
                'close_runs',
                'manage_moulds',
                'create_maintenance_events',
                'delete_maintenance_events',
                'move_locations',
            ],
            'QA' => [
                'view_main_dashboard',
                'view_qa_section',
                'access_operations',
                'manage_trials',
                'verify_trials',
            ],
            'Viewer' => [
                'view_main_dashboard',
                'access_operations', // Assuming viewer can view moulds etc.
            ],
            'Supervisor' => [
                'view_main_dashboard',
                'access_operations',
            ],
            'Manager' => [
                'view_main_dashboard',
                'access_operations',
            ],
        ];

        foreach ($permissionsByRole as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            foreach ($permissions as $permissionName) {
                // Create permission if it doesn't exist
                $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permissionName]);
                
                // Assign permission to role
                $role->givePermissionTo($permission);
            }
        }
    }
}
