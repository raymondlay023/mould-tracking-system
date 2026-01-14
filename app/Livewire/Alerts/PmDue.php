<?php

namespace App\Livewire\Alerts;

use App\Models\Machine;
use App\Models\Mould;
use App\Models\Plant;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PmDue extends Component
{
    public ?string $plant_id = null;

    public ?string $zone_id = null;

    public ?string $machine_id = null;

    public string $level = 'all'; // all|due|overdue

    public function render()
    {
        $today = now()->toDateString();

        // subquery: last maintenance per mould
        $lastMaint = DB::table('maintenance_events as me')
            ->selectRaw('me.mould_id, MAX(me.end_ts) as last_end_ts')
            ->groupBy('me.mould_id');

        // join to fetch last maintenance row (with next_due)
        $maint = DB::table('maintenance_events as me')
            ->joinSub($lastMaint, 'lm', function ($j) {
                $j->on('me.mould_id', '=', 'lm.mould_id')
                    ->on('me.end_ts', '=', 'lm.last_end_ts');
            })
            ->select([
                'me.mould_id',
                'me.end_ts as last_maint_end_ts',
                'me.type as last_maint_type',
                'me.next_due_date',
                'me.next_due_shot',
                'me.machine_id as last_maint_machine_id',
                'me.plant_id as last_maint_plant_id',
            ]);

        // subquery: total shot per mould (MVP)
        $shotAgg = DB::table('production_runs as pr')
            ->selectRaw('pr.mould_id, COALESCE(SUM(pr.shot_total),0) as total_shot')
            ->groupBy('pr.mould_id');

        // current location (optional)
        $curLoc = DB::table('location_histories as lh')
            ->whereNull('lh.end_ts')
            ->select([
                'lh.mould_id',
                'lh.plant_id',
                'lh.machine_id',
                'lh.location',
                'lh.start_ts',
            ]);

        $rows = DB::table('moulds as m')
            ->leftJoinSub($maint, 'lastm', fn ($j) => $j->on('m.id', '=', 'lastm.mould_id'))
            ->leftJoinSub($shotAgg, 'shots', fn ($j) => $j->on('m.id', '=', 'shots.mould_id'))
            ->leftJoinSub($curLoc, 'loc', fn ($j) => $j->on('m.id', '=', 'loc.mould_id'))
            ->leftJoin('machines as mc', 'loc.machine_id', '=', 'mc.id')
            ->leftJoin('zones as z', 'mc.zone_id', '=', 'z.id')
            ->leftJoin('plants as p', 'loc.plant_id', '=', 'p.id')
            ->leftJoin('machines as mc_last', 'lastm.last_maint_machine_id', '=', 'mc_last.id')
            ->leftJoin('zones as z_last', 'mc_last.zone_id', '=', 'z_last.id')
            ->leftJoin('plants as p_last', 'lastm.last_maint_plant_id', '=', 'p_last.id')

            ->select([
                'm.id',
                'm.code',
                'm.name',
                'm.pm_interval_shot',
                'm.pm_interval_days',

                'lastm.last_maint_end_ts',
                'lastm.next_due_date',
                'lastm.next_due_shot',

                DB::raw('COALESCE(shots.total_shot,0) as total_shot'),

                'loc.location as current_location',
                'loc.start_ts as location_since',
                'p.name as plant_name',
                'z.code as zone_code',
                'mc.code as machine_code',

                'p_last.name as last_maint_plant_name',
                'z_last.code as last_maint_zone_code',
                'mc_last.code as last_maint_machine_code',

            ])
            ->whereNotNull('lastm.mould_id') // hanya yang pernah punya maintenance event
            ->when($this->plant_id, fn ($q) => $q->where('lastm.last_maint_plant_id', $this->plant_id))
            ->when($this->zone_id, fn ($q) => $q->where('mc_last.zone_id', $this->zone_id))
            ->when($this->machine_id, fn ($q) => $q->where('lastm.last_maint_machine_id', $this->machine_id))

            ->orderBy('m.code');

        // apply level filter
        $data = $rows->get()->map(function ($r) use ($today) {
            $dueByDate = $r->next_due_date && $r->next_due_date <= $today;

            $dueByShot = $r->next_due_shot !== null && (int) $r->total_shot >= (int) $r->next_due_shot;

            $isDue = $dueByDate || $dueByShot;

            // overdue logic (simple MVP):
            // - overdue date: next_due_date < today
            // - overdue shot: total_shot > next_due_shot
            $overByDate = $r->next_due_date && $r->next_due_date < $today;
            $overByShot = $r->next_due_shot !== null && (int) $r->total_shot > (int) $r->next_due_shot;
            $isOverdue = $overByDate || $overByShot;

            $r->due_by = trim(($dueByDate ? 'DATE ' : '').($dueByShot ? 'SHOT' : ''));
            $r->pm_status = $isOverdue ? 'OVERDUE' : ($isDue ? 'DUE' : 'OK');

            return $r;
        });

        if ($this->level === 'due') {
            $data = $data->where('pm_status', 'DUE')->values();
        } elseif ($this->level === 'overdue') {
            $data = $data->where('pm_status', 'OVERDUE')->values();
        }

        $plants = Plant::orderBy('name')->get();
        $zones = Zone::orderBy('code')->get();
        $machines = Machine::with('plant', 'zone')->orderBy('code')->get();

        $counts = [
            'all' => $data->count(),
            'due' => $data->where('pm_status', 'DUE')->count(),
            'overdue' => $data->where('pm_status', 'OVERDUE')->count(),
        ];

        return view('livewire.alerts.pm-due', [
            'items' => $data,
            'plants' => $plants,
            'zones' => $zones,
            'machines' => $machines,
            'counts' => $counts,
            'today' => $today,
        ]);
    }
}
