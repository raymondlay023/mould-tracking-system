<?php

namespace App\Enums;

enum Role: string
{
    case Admin       = 'admin';
    case Production  = 'production';
    case Maintenance = 'maintenance';
    case QA          = 'qa';
    case Viewer      = 'viewer';
    case Supervisor  = 'supervisor';
    case Manager     = 'manager';
}
