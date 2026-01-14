<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MaintenanceReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(private Collection $rows) {}

    public function headings(): array
    {
        return [
            'Group Code','Group Name',
            'Events','PM Count','CM Count',
            'Downtime (min)','Cost',
        ];
    }

    public function collection(): Collection
    {
        return $this->rows->map(fn($r) => [
            $r->group_code,
            $r->group_name,
            (int)$r->events_count,
            (int)$r->pm_count,
            (int)$r->cm_count,
            (int)$r->downtime_min,
            (int)$r->cost_sum,
        ]);
    }
}
