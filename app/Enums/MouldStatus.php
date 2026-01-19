<?php

declare(strict_types=1);

namespace App\Enums;

enum MouldStatus: string
{
    case AVAILABLE = 'AVAILABLE';
    case IN_SETUP = 'IN_SETUP';
    case IN_RUN = 'IN_RUN';
    case IN_MAINTENANCE = 'IN_MAINTENANCE';
    case IN_TRANSIT = 'IN_TRANSIT';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::IN_SETUP => 'In Setup',
            self::IN_RUN => 'In Run',
            self::IN_MAINTENANCE => 'In Maintenance',
            self::IN_TRANSIT => 'In Transit',
        };
    }
}
