<?php

namespace App\Livewire\Dashboard;

use App\Exports\TopCmExport;
use App\Exports\TopNgExport;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class Summary extends Component
{
    public int $days_ng = 7;   // top NG last N days

    public int $days_cm = 30;  // top CM last N days

    public function exportTopNg()
    {
        $ngFrom = now()->subDays($this->days_ng)->toDateString();

        $topNg = DB::table('production_runs as pr')
            ->join('moulds as mo', 'pr.mould_id', '=', 'mo.id')
            ->whereNotNull('pr.end_ts')
            ->whereDate('pr.end_ts', '>=', $ngFrom)
            ->selectRaw('
            mo.code as mould_code,
            mo.name as mould_name,
            SUM(pr.ok_part) as ok_sum,
            SUM(pr.ng_part) as ng_sum,
            SUM(pr.shot_total) as shot_sum
        ')
            ->groupBy('mo.code', 'mo.name')
            ->orderByRaw('(CASE WHEN (SUM(pr.ok_part)+SUM(pr.ng_part))=0 THEN 0 ELSE (SUM(pr.ng_part)/(SUM(pr.ok_part)+SUM(pr.ng_part))) END) DESC')
            ->limit(100)
            ->get();

        return Excel::download(new TopNgExport($topNg, $ngFrom), "top_ng_since_{$ngFrom}.xlsx");
    }

    public function exportTopCm()
    {
        $cmFrom = now()->subDays($this->days_cm)->toDateString();

        $topCm = DB::table('maintenance_events as me')
            ->join('moulds as mo', 'me.mould_id', '=', 'mo.id')
            ->where('me.type', 'CM')
            ->whereDate('me.end_ts', '>=', $cmFrom)
            ->selectRaw('
            mo.code as mould_code,
            mo.name as mould_name,
            COUNT(*) as cm_count,
            COALESCE(SUM(me.downtime_min),0) as downtime_sum
        ')
            ->groupBy('mo.code', 'mo.name')
            ->orderByDesc('cm_count')
            ->limit(100)
            ->get();

        return Excel::download(new TopCmExport($topCm, $cmFrom), "top_cm_since_{$cmFrom}.xlsx");
    }

    public function render()
    {
        // 1) Active runs (adjust table/name if different)
        $activeRuns = DB::table('production_runs as pr')
            ->join('moulds as mo', 'pr.mould_id', '=', 'mo.id')
            ->join('machines as mc', 'pr.machine_id', '=', 'mc.id')
            ->leftJoin('plants as p', 'mc.plant_id', '=', 'p.id')
            ->leftJoin('zones as z', 'mc.zone_id', '=', 'z.id')
            ->whereNull('pr.end_ts')
            ->orderByDesc('pr.start_ts')
            ->limit(15)
            ->get([
                'pr.id',
                'pr.start_ts',
                'mo.id as mould_id',
                'mo.code as mould_code',
                'mo.name as mould_name',
                'mc.code as machine_code',
                'p.name as plant_name',
                'z.code as zone_code',
            ]);

        $activeCount = (int) DB::table('production_runs')->whereNull('end_ts')->count();

        $activeRunIds = $activeRuns->pluck('id')->values();

        // 2) PM Due & Overdue
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
            ->limit(10)
            ->get();

        $pmOverdueCount = (int) $pmDue->where('pm_status', 'OVERDUE')->count();
        $pmDueCount = (int) $pmDue->where('pm_status', 'DUE')->count();

        // 3) Top NG (last N days)
        $ngFrom = now()->subDays($this->days_ng)->toDateString();

        $topNg = DB::table('production_runs as pr')
            ->join('moulds as mo', 'pr.mould_id', '=', 'mo.id')
            ->whereNotNull('pr.end_ts')
            ->whereDate('pr.end_ts', '>=', $ngFrom)
            ->selectRaw('
                mo.id as mould_id,
                mo.code as mould_code,
                mo.name as mould_name,
                SUM(pr.ok_part) as ok_sum,
                SUM(pr.ng_part) as ng_sum,
                SUM(pr.shot_total) as shot_sum
            ')
            ->groupBy('mo.id', 'mo.code', 'mo.name')
            ->orderByRaw('(CASE WHEN (SUM(pr.ok_part)+SUM(pr.ng_part))=0 THEN 0 ELSE (SUM(pr.ng_part)/(SUM(pr.ok_part)+SUM(pr.ng_part))) END) DESC')
            ->limit(10)
            ->get();

        // 4) Top CM (last N days)
        $cmFrom = now()->subDays($this->days_cm)->toDateString();

        $topCm = DB::table('maintenance_events as me')
            ->join('moulds as mo', 'me.mould_id', '=', 'mo.id')
            ->where('me.type', 'CM')
            ->whereDate('me.end_ts', '>=', $cmFrom)
            ->selectRaw('
                mo.id as mould_id,
                mo.code as mould_code,
                mo.name as mould_name,
                COUNT(*) as cm_count,
                COALESCE(SUM(me.downtime_min),0) as downtime_sum
            ')
            ->groupBy('mo.id', 'mo.code', 'mo.name')
            ->orderByDesc('cm_count')
            ->limit(10)
            ->get();

        return view('livewire.dashboard.summary', compact(
            'activeRuns', 'activeCount','activeRunIds',
            'pmDue', 'pmOverdueCount', 'pmDueCount',
            'topNg', 'topCm', 'ngFrom', 'cmFrom'
        ));
    }
}
