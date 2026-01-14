<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TopCmExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(private Collection $rows, private string $from) {}

    public function headings(): array
    {
        return ['Since', 'Mould Code', 'Mould Name', 'CM Count', 'Downtime (min)'];
    }

    public function collection(): Collection
    {
        return $this->rows->map(fn($r) => [
            $this->from,
            $r->mould_code,
            $r->mould_name,
            (int)$r->cm_count,
            (int)$r->downtime_sum,
        ]);
    }
}
