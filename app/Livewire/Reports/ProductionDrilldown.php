<?php

namespace App\Livewire\Reports;

use App\Models\Mould;
use App\Models\Machine;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductionDrilldown extends Component
{
    public string $group; // mould|machine
    public string $id;    // uuid

    public string $date_from;
    public string $date_to;

    public ?string $plant_id = null;
    public ?string $zone_id = null;
    public ?string $machine_id = null;

    public function mount(string $group, string $id): void
    {
        abort_unless(in_array($group, ['mould','machine']), 404);

        $this->group = $group;
        $this->id = $id;

        $this->date_from = request('date_from', now()->startOfMonth()->toDateString());
        $this->date_to   = request('date_to', now()->toDateString());

        $this->plant_id = request('plant_id');
        $this->zone_id = request('zone_id');
        $this->machine_id = request('machine_id');
    }

    private function baseRunsQuery()
    {
        // NOTE: ganti 'production_runs' kalau tabel kamu beda
        $q = DB::table('production_runs as pr')
            ->join('moulds as mo', 'pr.mould_id', '=', 'mo.id')
            ->join('machines as mc', 'pr.machine_id', '=', 'mc.id')
            ->leftJoin('plants as p', 'mc.plant_id', '=', 'p.id')
            ->leftJoin('zones as z', 'mc.zone_id', '=', 'z.id')
            ->whereNotNull('pr.end_ts')
            ->whereDate('pr.end_ts', '>=', $this->date_from)
            ->whereDate('pr.end_ts', '<=', $this->date_to)
            ->when($this->plant_id, fn($qq) => $qq->where('mc.plant_id', $this->plant_id))
            ->when($this->zone_id, fn($qq) => $qq->where('mc.zone_id', $this->zone_id))
            ->when($this->machine_id, fn($qq) => $qq->where('mc.id', $this->machine_id));

        if ($this->group === 'mould') {
            $q->where('pr.mould_id', $this->id);
        } else {
            $q->where('pr.machine_id', $this->id);
        }

        return $q;
    }

    public function render()
    {
        $title = $this->group === 'mould'
            ? optional(Mould::find($this->id))->code . ' - ' . optional(Mould::find($this->id))->name
            : optional(Machine::find($this->id))->code . ' - ' . optional(Machine::find($this->id))->name;

        $runs = $this->baseRunsQuery()
            ->select([
                'pr.id',
                'pr.start_ts',
                'pr.end_ts',
                'pr.shot_total',
                'pr.ok_part',
                'pr.ng_part',
                'pr.cycle_time_avg_sec',
                'mo.code as mould_code',
                'mc.code as machine_code',
                'p.name as plant_name',
                'z.code as zone_code',
            ])
            ->orderByDesc('pr.end_ts')
            ->limit(200)
            ->get();

        // Top defects for this subset
        $topDefects = DB::table('run_defects as rd')
            ->join('production_runs as pr', 'rd.run_id', '=', 'pr.id') // ganti kalau tabel beda
            ->join('machines as mc', 'pr.machine_id', '=', 'mc.id')
            ->whereNotNull('pr.end_ts')
            ->whereDate('pr.end_ts', '>=', $this->date_from)
            ->whereDate('pr.end_ts', '<=', $this->date_to)
            ->when($this->plant_id, fn($qq) => $qq->where('mc.plant_id', $this->plant_id))
            ->when($this->zone_id, fn($qq) => $qq->where('mc.zone_id', $this->zone_id))
            ->when($this->machine_id, fn($qq) => $qq->where('mc.id', $this->machine_id))
            ->when($this->group === 'mould', fn($qq) => $qq->where('pr.mould_id', $this->id))
            ->when($this->group === 'machine', fn($qq) => $qq->where('pr.machine_id', $this->id))
            ->selectRaw('rd.defect_code, SUM(rd.qty) as qty_sum')
            ->groupBy('rd.defect_code')
            ->orderByDesc('qty_sum')
            ->limit(10)
            ->get();

        // KPI
        $kpiShot = (int) $runs->sum('shot_total');
        $kpiOk = (int) $runs->sum('ok_part');
        $kpiNg = (int) $runs->sum('ng_part');
        $partTotal = $kpiOk + $kpiNg;
        $ngRate = $partTotal > 0 ? round(($kpiNg / $partTotal) * 100, 2) : 0;

        return view('livewire.reports.production-drilldown', compact(
            'title','runs','topDefects','kpiShot','kpiOk','kpiNg','ngRate'
        ));
    }
}
