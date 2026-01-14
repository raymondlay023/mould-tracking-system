<?php

namespace App\Livewire\Reports;

use App\Models\Machine;
use App\Models\Mould;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MaintenanceDrilldown extends Component
{
    public string $group; // mould|machine

    public string $id;

    public string $date_from;

    public string $date_to;

    public ?string $plant_id = null;

    public ?string $zone_id = null;

    public ?string $machine_id = null;

    public function mount(string $group, string $id): void
    {
        abort_unless(in_array($group, ['mould', 'machine']), 404);

        $this->group = $group;
        $this->id = $id;

        $this->date_from = request('date_from', now()->startOfMonth()->toDateString());
        $this->date_to = request('date_to', now()->toDateString());

        $this->plant_id = request('plant_id');
        $this->zone_id = request('zone_id');
        $this->machine_id = request('machine_id');
    }

    public function render()
    {
        $title = $this->group === 'mould'
            ? optional(Mould::find($this->id))->code.' - '.optional(Mould::find($this->id))->name
            : optional(Machine::find($this->id))->code.' - '.optional(Machine::find($this->id))->name;

        $curLoc = DB::table('location_histories as lh')
            ->whereNull('lh.end_ts')
            ->select(['lh.mould_id', 'lh.plant_id', 'lh.machine_id']);

        $q = DB::table('maintenance_events as me')
            ->join('moulds as mo', 'me.mould_id', '=', 'mo.id')
            ->leftJoinSub($curLoc, 'loc', fn ($j) => $j->on('mo.id', '=', 'loc.mould_id'))
            ->leftJoin('machines as mc', 'loc.machine_id', '=', 'mc.id')
            ->leftJoin('plants as p', 'loc.plant_id', '=', 'p.id')
            ->leftJoin('zones as z', 'mc.zone_id', '=', 'z.id')
            ->whereDate('me.end_ts', '>=', $this->date_from)
            ->whereDate('me.end_ts', '<=', $this->date_to)
            ->when($this->plant_id, fn ($qq) => $qq->where('loc.plant_id', $this->plant_id))
            ->when($this->zone_id, fn ($qq) => $qq->where('mc.zone_id', $this->zone_id))
            ->when($this->machine_id, fn ($qq) => $qq->where('mc.id', $this->machine_id));

        if ($this->group === 'mould') {
            $q->where('mo.id', $this->id);
        } else {
            $q->where('mc.id', $this->id);
        }

        $events = $q->select([
            'me.id', 'me.type', 'me.start_ts', 'me.end_ts', 'me.downtime_min', 'me.cost',
            'me.description', 'me.parts_used', 'me.next_due_shot', 'me.next_due_date', 'me.performed_by',
            'mo.code as mould_code',
            'mc.code as machine_code',
            'p.name as plant_name',
            'z.code as zone_code',
        ])
            ->orderByDesc('me.end_ts')
            ->limit(300)
            ->get();

        $kpi = [
            'events' => (int) $events->count(),
            'pm' => (int) $events->where('type', 'PM')->count(),
            'cm' => (int) $events->where('type', 'CM')->count(),
            'downtime' => (int) $events->sum('downtime_min'),
            'cost' => (int) $events->sum('cost'),
        ];

        return view('livewire.reports.maintenance-drilldown', compact('title', 'events', 'kpi'));
    }
}
