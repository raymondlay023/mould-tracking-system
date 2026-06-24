<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Enums\Role as RoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionsByRole = [
            RoleEnum::Admin->value => array_column(PermissionEnum::cases(), 'value'),
            RoleEnum::Production->value => [
                PermissionEnum::ViewMainDashboard->value,
                PermissionEnum::ViewProductionSection->value,
                PermissionEnum::AccessOperations->value,
                PermissionEnum::ManageTrials->value,
                PermissionEnum::ManageSetups->value,
                PermissionEnum::CloseRuns->value,
                PermissionEnum::ManageMoulds->value,
                PermissionEnum::CreateMaintenanceEvents->value,
                PermissionEnum::UpdateMaintenanceEvents->value,
                PermissionEnum::MoveLocations->value,
            ],
            RoleEnum::Maintenance->value => [
                PermissionEnum::ViewMainDashboard->value,
                PermissionEnum::ViewMaintenanceSection->value,
                PermissionEnum::AccessOperations->value,
                PermissionEnum::CreateMaintenanceEvents->value,
                PermissionEnum::UpdateMaintenanceEvents->value,
                PermissionEnum::DeleteMaintenanceEvents->value,
                PermissionEnum::MoveLocations->value,
            ],
            RoleEnum::QA->value => [
                PermissionEnum::ViewMainDashboard->value,
                PermissionEnum::ViewQaSection->value,
                PermissionEnum::AccessOperations->value,
                PermissionEnum::ManageTrials->value,
                PermissionEnum::VerifyTrials->value,
                PermissionEnum::MoveLocations->value,
            ],
            RoleEnum::Viewer->value => [
                PermissionEnum::ViewMainDashboard->value,
                PermissionEnum::AccessOperations->value,
            ],
            RoleEnum::Supervisor->value => [
                PermissionEnum::ViewMainDashboard->value,
                PermissionEnum::ViewProductionSection->value,
                PermissionEnum::ViewQaSection->value,
                PermissionEnum::AccessOperations->value,
                PermissionEnum::ViewReports->value,
                PermissionEnum::ManageTrials->value,
                PermissionEnum::ManageSetups->value,
                PermissionEnum::CloseRuns->value,
                PermissionEnum::ManageMoulds->value,
                PermissionEnum::MoveLocations->value,
                PermissionEnum::VerifyTrials->value,
            ],
            RoleEnum::Manager->value => [
                PermissionEnum::ViewMainDashboard->value,
                PermissionEnum::ViewProductionSection->value,
                PermissionEnum::ViewMaintenanceSection->value,
                PermissionEnum::ViewQaSection->value,
                PermissionEnum::AccessOperations->value,
                PermissionEnum::ViewReports->value,
                PermissionEnum::ManageMachines->value,
            ],
        ];

        DB::transaction(function () use ($permissionsByRole) {
            // Bulk-create all unique permissions first (eliminates N+1)
            collect($permissionsByRole)
                ->flatten()
                ->unique()
                ->each(fn (string $name) => Permission::firstOrCreate([
                    'name'       => $name,
                    'guard_name' => 'web',
                ]));

            // Create each role and sync its permissions (single query per role)
            foreach ($permissionsByRole as $roleName => $permissions) {
                $role = Role::firstOrCreate([
                    'name'       => $roleName,
                    'guard_name' => 'web',
                ]);

                $role->syncPermissions($permissions);
            }
        });
    }
}
