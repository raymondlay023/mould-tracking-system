<?php

namespace App\Livewire\Maintenance;

use App\Models\MaintenanceEvent;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        // 1. PM Due Logic (Reused from Summary)
        $today = now()->toDateString();

        $lastMaint = DB::table('maintenance_events as me')
            ->selectRaw('me.mould_id, MAX(me.end_ts) as last_end_ts')
            ->groupBy('me.mould_id');

        $pmDue = DB::table('maintenance_events as me')
            ->joinSub($lastMaint, 'lm', function ($j) {
                $j->on('me.mould_id', '=', 'lm.mould_id')
                    ->on('me.end_ts', '=', 'lm.last_end_ts');
            })
            ->join('moulds as mo', 'me.mould_id', '=', 'mo.id')
            ->leftJoin(DB::raw('(SELECT mould_id, COALESCE(SUM(shot_total),0) total_shot FROM production_runs GROUP BY mould_id) shots'), 'shots.mould_id', '=', 'mo.id')
            ->selectRaw("
                mo.id as mould_id,
                mo.code as mould_code,
                mo.name as mould_name,
                me.next_due_date,
                me.next_due_shot,
                COALESCE(shots.total_shot,0) as total_shot,
                (CASE
                    WHEN (me.next_due_date IS NOT NULL AND me.next_due_date < '{$today}')
                      OR (me.next_due_shot IS NOT NULL AND COALESCE(shots.total_shot,0) > me.next_due_shot)
                    THEN 'OVERDUE'
                    WHEN (me.next_due_date IS NOT NULL AND me.next_due_date <= '{$today}')
                      OR (me.next_due_shot IS NOT NULL AND COALESCE(shots.total_shot,0) >= me.next_due_shot)
                    THEN 'DUE'
                    ELSE 'OK'
                 END) as pm_status
            ")
            ->havingRaw("pm_status IN ('DUE','OVERDUE')")
            ->orderByRaw("FIELD(pm_status,'OVERDUE','DUE')")
            ->limit(50)
            ->get();

        $overdueCount = $pmDue->where('pm_status', 'OVERDUE')->count();
        $dueCount = $pmDue->where('pm_status', 'DUE')->count();

        // 2. Recent Maintenance Events
        $recentEvents = MaintenanceEvent::with('mould')
            ->orderByDesc('end_ts')
            ->limit(10)
            ->get();

        // 3. Top Downtime (This Month) - Focus on recent issues
        $startMonth = now()->startOfMonth()->toDateString();
        $topDowntime = DB::table('maintenance_events as me')
            ->join('moulds as mo', 'me.mould_id', '=', 'mo.id')
            ->whereDate('me.end_ts', '>=', $startMonth)
            ->selectRaw('mo.code, SUM(me.downtime_min) as downtime_sum, COUNT(*) as ev_count')
            ->groupBy('mo.code')
            ->orderByDesc('downtime_sum')
            ->limit(5)
            ->get();

        return view('livewire.maintenance.dashboard', compact('pmDue', 'recentEvents', 'overdueCount', 'dueCount', 'topDowntime'));
    }
}
