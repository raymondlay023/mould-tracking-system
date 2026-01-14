<?php

namespace App\Livewire\Reports;

use App\Exports\MaintenanceReportExport;
use App\Models\Machine;
use App\Models\Plant;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class MaintenanceReport extends Component
{
    public ?string $plant_id = null;

    public ?string $zone_id = null;

    public ?string $machine_id = null;

    public string $date_from;

    public string $date_to;

    public string $group_by = 'mould'; // mould|machine

    public string $sort = 'downtime_desc'; // downtime_desc|cm_desc|pm_desc|cost_desc|count_desc

    public function mount(): void
    {
        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to = now()->toDateString();
    }

    public function exportExcel()
    {
        $rows = $this->buildQuery()->get();

        $filename = 'maintenance_report_'.$this->date_from.'_to_'.$this->date_to.'.xlsx';

        return Excel::download(new MaintenanceReportExport($rows), $filename);
    }

    private function buildQuery()
    {
        // maintenance_events has mould_id but no machine_id,
        // so we determine plant/zone/machine through current location or last known location.
        // MVP: use current location (location_histories end_ts null).
        $curLoc = DB::table('location_histories as lh')
            ->whereNull('lh.end_ts')
            ->select(['lh.mould_id', 'lh.plant_id', 'lh.machine_id']);

        $base = DB::table('maintenance_events as me')
            ->join('moulds as mo', 'me.mould_id', '=', 'mo.id')
            ->leftJoinSub($curLoc, 'loc', fn ($j) => $j->on('mo.id', '=', 'loc.mould_id'))
            ->when($this->plant_id, fn ($q) => $q->where('me.plant_id', $this->plant_id))
            ->when($this->zone_id, fn ($q) => $q->where('mc.zone_id', $this->zone_id))
            ->when($this->machine_id, fn ($q) => $q->where('me.machine_id', $this->machine_id))
            ->whereDate('me.end_ts', '>=', $this->date_from)
            ->whereDate('me.end_ts', '<=', $this->date_to)
            ->when($this->plant_id, fn ($q) => $q->where('loc.plant_id', $this->plant_id))
            ->when($this->zone_id, fn ($q) => $q->where('mc.zone_id', $this->zone_id))
            ->when($this->machine_id, fn ($q) => $q->where('mc.id', $this->machine_id));

        if ($this->group_by === 'machine') {
            // only moulds that currently attached to machine will show
            $q = $base->whereNotNull('mc.id')
                ->selectRaw("
                    mc.id as group_id,
                    mc.code as group_code,
                    CONCAT(mc.code, ' - ', mc.name) as group_name,
                    COUNT(*) as events_count,
                    SUM(CASE WHEN me.type='PM' THEN 1 ELSE 0 END) as pm_count,
                    SUM(CASE WHEN me.type='CM' THEN 1 ELSE 0 END) as cm_count,
                    COALESCE(SUM(me.downtime_min),0) as downtime_min,
                    COALESCE(SUM(me.cost),0) as cost_sum
                ")
                ->groupBy('mc.id', 'mc.code', 'mc.name');
        } else {
            $q = $base->selectRaw("
                    mo.id as group_id,
                    mo.code as group_code,
                    CONCAT(mo.code, ' - ', mo.name) as group_name,
                    COUNT(*) as events_count,
                    SUM(CASE WHEN me.type='PM' THEN 1 ELSE 0 END) as pm_count,
                    SUM(CASE WHEN me.type='CM' THEN 1 ELSE 0 END) as cm_count,
                    COALESCE(SUM(me.downtime_min),0) as downtime_min,
                    COALESCE(SUM(me.cost),0) as cost_sum
                ")
                ->groupBy('mo.id', 'mo.code', 'mo.name');
        }

        return match ($this->sort) {
            'cm_desc' => $q->orderByDesc('cm_count'),
            'pm_desc' => $q->orderByDesc('pm_count'),
            'cost_desc' => $q->orderByDesc('cost_sum'),
            'count_desc' => $q->orderByDesc('events_count'),
            default => $q->orderByDesc('downtime_min'),
        };
    }

    public function render()
    {
        $rows = $this->buildQuery()->get();

        $kpi = [
            'events' => (int) $rows->sum('events_count'),
            'pm' => (int) $rows->sum('pm_count'),
            'cm' => (int) $rows->sum('cm_count'),
            'downtime_min' => (int) $rows->sum('downtime_min'),
            'cost_sum' => (int) $rows->sum('cost_sum'),
        ];

        $plants = Plant::orderBy('name')->get();
        $zones = Zone::orderBy('code')->get();
        $machines = Machine::with('plant', 'zone')->orderBy('code')->get();

        return view('livewire.reports.maintenance-report', compact('rows', 'kpi', 'plants', 'zones', 'machines'));
    }
}
