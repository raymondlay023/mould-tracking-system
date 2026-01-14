<?php

namespace App\Livewire\Reports;

use App\Exports\ProductionReportExport;
use App\Models\Machine;
use App\Models\Plant;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ProductionReport extends Component
{
    public ?string $plant_id = null;

    public ?string $zone_id = null;

    public ?string $machine_id = null;

    public string $date_from;

    public string $date_to;

    public string $group_by = 'mould'; // mould|machine

    public string $sort = 'ng_rate_desc'; // ng_rate_desc|ng_desc|ok_desc|shot_desc|cycle_desc

    public function mount(): void
    {
        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to = now()->toDateString();
    }

    public function exportExcel()
    {
        $rows = $this->buildQuery()->get();

        $filename = 'production_report_'.$this->date_from.'_to_'.$this->date_to.'.xlsx';

        return Excel::download(new ProductionReportExport($rows), $filename);
    }

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = $this->buildQuery()->get();

        $filename = 'production_report_'.$this->date_from.'_to_'.$this->date_to.'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'group', 'name',
                'shot_total', 'ok_part', 'ng_part', 'part_total',
                'ng_rate', 'avg_cycle_sec', 'runs',
            ]);

            foreach ($rows as $r) {
                $partTotal = (int) $r->ok_part + (int) $r->ng_part;
                $ngRate = $partTotal > 0 ? round(((int) $r->ng_part / $partTotal) * 100, 2) : 0;

                fputcsv($out, [
                    $r->group_code,
                    $r->group_name,
                    (int) $r->shot_total,
                    (int) $r->ok_part,
                    (int) $r->ng_part,
                    $partTotal,
                    $ngRate.'%',
                    $r->avg_cycle_sec !== null ? round((float) $r->avg_cycle_sec, 2) : '',
                    (int) $r->runs_count,
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function buildQuery()
    {
        // IMPORTANT: ganti nama tabel/kolom jika beda di project kamu
        $base = DB::table('production_runs as pr')
            ->join('moulds as mo', 'pr.mould_id', '=', 'mo.id')
            ->join('machines as mc', 'pr.machine_id', '=', 'mc.id')
            ->leftJoin('plants as p', 'mc.plant_id', '=', 'p.id')
            ->leftJoin('zones as z', 'mc.zone_id', '=', 'z.id')
            ->whereNotNull('pr.end_ts')
            ->whereDate('pr.end_ts', '>=', $this->date_from)
            ->whereDate('pr.end_ts', '<=', $this->date_to)
            ->when($this->plant_id, fn ($q) => $q->where('mc.plant_id', $this->plant_id))
            ->when($this->zone_id, fn ($q) => $q->where('mc.zone_id', $this->zone_id))
            ->when($this->machine_id, fn ($q) => $q->where('mc.id', $this->machine_id));

        if ($this->group_by === 'machine') {
            $q = $base->selectRaw("
                    mc.id as group_id,
                    mc.code as group_code,
                    CONCAT(mc.code, ' - ', mc.name) as group_name,
                    COALESCE(SUM(pr.shot_total),0) as shot_total,
                    COALESCE(SUM(pr.ok_part),0) as ok_part,
                    COALESCE(SUM(pr.ng_part),0) as ng_part,
                    AVG(pr.cycle_time_avg_sec) as avg_cycle_sec,
                    COUNT(*) as runs_count
                ")
                ->groupBy('mc.id', 'mc.code', 'mc.name');
        } else {
            $q = $base->selectRaw("
                    mo.id as group_id,
                    mo.code as group_code,
                    CONCAT(mo.code, ' - ', mo.name) as group_name,
                    COALESCE(SUM(pr.shot_total),0) as shot_total,
                    COALESCE(SUM(pr.ok_part),0) as ok_part,
                    COALESCE(SUM(pr.ng_part),0) as ng_part,
                    AVG(pr.cycle_time_avg_sec) as avg_cycle_sec,
                    COUNT(*) as runs_count
                ")
                ->groupBy('mo.id', 'mo.code', 'mo.name');
        }

        // ---- Top NG Reason (per group) ----
        $groupCol = $this->group_by === 'machine' ? 'pr.machine_id' : 'pr.mould_id';

        // aggregate defects per group
        $defAgg = DB::table('production_runs as pr')
            ->join('run_defects as rd', 'rd.run_id', '=', 'pr.id')
            ->selectRaw("
                {$groupCol} as group_id,
                rd.defect_code,
                SUM(rd.qty) as qty_sum
            ")
            ->whereNotNull('pr.end_ts')
            ->whereDate('pr.end_ts', '>=', $this->date_from)
            ->whereDate('pr.end_ts', '<=', $this->date_to)
            ->when($this->plant_id, function ($qq) {
                $qq->join('machines as mc2', 'pr.machine_id', '=', 'mc2.id')
                    ->where('mc2.plant_id', $this->plant_id);
            })
            ->when($this->zone_id, function ($qq) {
                $qq->join('machines as mc3', 'pr.machine_id', '=', 'mc3.id')
                    ->where('mc3.zone_id', $this->zone_id);
            })
            ->when($this->machine_id, fn ($qq) => $qq->where('pr.machine_id', $this->machine_id))
            ->groupBy('group_id', 'rd.defect_code');

        // pick top 1 defect per group using ROW_NUMBER
        $topDef = DB::query()
            ->fromSub($defAgg, 'd')
            ->selectRaw('
                d.group_id,
                d.defect_code,
                d.qty_sum,
                ROW_NUMBER() OVER (PARTITION BY d.group_id ORDER BY d.qty_sum DESC) as rn
            ');

        $q->leftJoinSub(
            DB::query()->fromSub($topDef, 'td')->where('td.rn', 1),
            'topd',
            fn ($j) => $j->on('topd.group_id', '=', 'group_id')
        );

        // include columns in select
        $q->addSelect(DB::raw('topd.defect_code as top_defect_code'));
        $q->addSelect(DB::raw('topd.qty_sum as top_defect_qty'));

        // sorting
        // note: ng_rate = ng / (ok+ng). MySQL: handle via expression
        return match ($this->sort) {
            'ng_desc' => $q->orderByDesc('ng_part'),
            'ok_desc' => $q->orderByDesc('ok_part'),
            'shot_desc' => $q->orderByDesc('shot_total'),
            'cycle_desc' => $q->orderByDesc('avg_cycle_sec'),
            default => $q->orderByRaw('(CASE WHEN (ok_part+ng_part)=0 THEN 0 ELSE (ng_part/(ok_part+ng_part)) END) DESC'),
        };
    }

    public function render()
    {
        $rows = $this->buildQuery()->get();

        // KPI ringkas
        $kpi = [
            'shot_total' => (int) $rows->sum('shot_total'),
            'ok_total' => (int) $rows->sum('ok_part'),
            'ng_total' => (int) $rows->sum('ng_part'),
        ];
        $partTotal = $kpi['ok_total'] + $kpi['ng_total'];
        $kpi['ng_rate'] = $partTotal > 0 ? round(($kpi['ng_total'] / $partTotal) * 100, 2) : 0;

        $plants = Plant::orderBy('name')->get();
        $zones = Zone::orderBy('code')->get();
        $machines = Machine::with('plant', 'zone')->orderBy('code')->get();

        return view('livewire.reports.production-report', compact('rows', 'kpi', 'plants', 'zones', 'machines'));
    }
}
