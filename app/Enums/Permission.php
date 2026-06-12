<?php

namespace App\Enums;

enum Permission: string
{
    // Admin
    case ViewAdminPanel          = 'view_admin_panel';
    case ManageUsers             = 'manage_users';
    case ManagePlants            = 'manage_plants';
    case ManageZones             = 'manage_zones';
    case ManageMachines          = 'manage_machines';
    case ImportData              = 'import_data';
    case ViewAuditLogs           = 'view_audit_logs';
    case DeleteMoulds            = 'delete_moulds';

    // General / Dashboard
    case ViewMainDashboard       = 'view_main_dashboard';
    case AccessOperations        = 'access_operations';

    // Production
    case ViewProductionSection   = 'view_production_section';
    case ManageTrials            = 'manage_trials';
    case ManageSetups            = 'manage_setups';
    case CloseRuns               = 'close_runs';
    case ManageMoulds            = 'manage_moulds';
    case MoveLocations           = 'move_locations';

    // Maintenance
    case ViewMaintenanceSection      = 'view_maintenance_section';
    case CreateMaintenanceEvents     = 'create_maintenance_events';
    case DeleteMaintenanceEvents     = 'delete_maintenance_events';

    // QA
    case ViewQaSection           = 'view_qa_section';
    case VerifyTrials            = 'verify_trials';
}
