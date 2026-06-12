<?php

namespace App\Enums;

enum Role: string
{
    case Admin       = 'Admin';
    case Production  = 'Production';
    case Maintenance = 'Maintenance';
    case QA          = 'QA';
    case Viewer      = 'Viewer';
    case Supervisor  = 'Supervisor';
    case Manager     = 'Manager';
}
