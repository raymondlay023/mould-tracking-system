<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TopNgExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(private Collection $rows, private string $from) {}

    public function headings(): array
    {
        return ['Since', 'Mould Code', 'Mould Name', 'OK', 'NG', 'Part Total', 'NG Rate (%)', 'Shot'];
    }

    public function collection(): Collection
    {
        return $this->rows->map(function ($r) {
            $pt = (int)$r->ok_sum + (int)$r->ng_sum;
            $rate = $pt > 0 ? round(((int)$r->ng_sum / $pt) * 100, 2) : 0;

            return [
                $this->from,
                $r->mould_code,
                $r->mould_name,
                (int)$r->ok_sum,
                (int)$r->ng_sum,
                $pt,
                $rate,
                (int)$r->shot_sum,
            ];
        });
    }
}
