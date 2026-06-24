<?php

namespace App\Enums;

enum Permission: string
{
    // Admin
    case ViewAdminPanel          = 'admin_panel.view';
    case ManageUsers             = 'users.manage';
    case ManagePlants            = 'plants.manage';
    case ManageZones             = 'zones.manage';
    case ManageMachines          = 'machines.manage';
    case ImportData              = 'data.import';
    case ViewAuditLogs           = 'audit_logs.view';
    case DeleteMoulds            = 'moulds.delete';

    // General / Dashboard
    case ViewMainDashboard       = 'dashboard.view';
    case AccessOperations        = 'operations.access';
    case ViewReports             = 'reports.view';

    // Production
    case ViewProductionSection   = 'production.view';
    case ManageTrials            = 'trials.manage';
    case ManageSetups            = 'setups.manage';
    case CloseRuns               = 'runs.close';
    case ManageMoulds            = 'moulds.manage';
    case MoveLocations           = 'locations.move';

    // Maintenance
    case ViewMaintenanceSection      = 'maintenance.view';
    case CreateMaintenanceEvents     = 'maintenance_events.create';
    case UpdateMaintenanceEvents     = 'maintenance_events.update';
    case DeleteMaintenanceEvents     = 'maintenance_events.delete';

    // QA
    case ViewQaSection           = 'qa.view';
    case VerifyTrials            = 'trials.verify';
}
