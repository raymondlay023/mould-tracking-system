<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductionReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(private Collection $rows) {}

    public function headings(): array
    {
        return [
            'Group Code','Group Name',
            'Shot Total','OK Part','NG Part','Part Total',
            'NG Rate (%)','Avg Cycle (s)','Runs',
            'Top Defect Code','Top Defect Qty',
        ];
    }

    public function collection(): Collection
    {
        return $this->rows->map(function ($r) {
            $partTotal = (int)$r->ok_part + (int)$r->ng_part;
            $ngRate = $partTotal > 0 ? round(((int)$r->ng_part / $partTotal) * 100, 2) : 0;

            return [
                $r->group_code,
                $r->group_name,
                (int)$r->shot_total,
                (int)$r->ok_part,
                (int)$r->ng_part,
                $partTotal,
                $ngRate,
                $r->avg_cycle_sec !== null ? round((float)$r->avg_cycle_sec, 2) : null,
                (int)$r->runs_count,
                $r->top_defect_code ?? null,
                $r->top_defect_qty ?? null,
            ];
        });
    }
}
