<?php

namespace App\Livewire\Production;

use App\Models\Mould;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        // 1. Active Runs
        $activeRuns = DB::table('production_runs as pr')
            ->join('moulds as mo', 'pr.mould_id', '=', 'mo.id')
            ->join('machines as mc', 'pr.machine_id', '=', 'mc.id')
            ->whereNull('pr.end_ts')
            ->orderByDesc('pr.start_ts')
            ->get([
                'pr.id',
                'pr.start_ts',
                'pr.shot_total',
                'mo.code as mould_code',
                'mo.name as mould_name',
                'mc.code as machine_code',
            ]);

        // 2. Available Moulds (Ready for Production)
        $availableMoulds = Mould::query()
            ->where('status', 'AVAILABLE')
            ->orderBy('code')
            ->limit(20)
            ->get(['id', 'code', 'name', 'cavities', 'customer']);

        // 3. Top NG (Quality Issues) - Last 7 Days
        $ngFrom = now()->subDays(7)->toDateString();
        $topNg = DB::table('production_runs as pr')
            ->join('moulds as mo', 'pr.mould_id', '=', 'mo.id')
            ->whereNotNull('pr.end_ts')
            ->whereDate('pr.end_ts', '>=', $ngFrom)
            ->selectRaw('
                mo.code as mould_code,
                SUM(pr.ok_part) as ok_sum,
                SUM(pr.ng_part) as ng_sum
            ')
            ->groupBy('mo.code')
            ->orderByRaw('(CASE WHEN (SUM(pr.ok_part)+SUM(pr.ng_part))=0 THEN 0 ELSE (SUM(pr.ng_part)/(SUM(pr.ok_part)+SUM(pr.ng_part))) END) DESC')
            ->limit(5)
            ->get();

        // 4. OEE Performance (Recent Closed Runs)
        $closedRuns = \App\Models\ProductionRun::with('mould')
            ->whereNotNull('end_ts')
            ->latest('end_ts')
            ->limit(5)
            ->get();
            
        $recentOee = $closedRuns->map(function ($run) {
            $stats = \App\Stats\OeeCalculator::calculate($run);
            return (object) [
                'mould' => $run->mould->code,
                'end_ts' => $run->end_ts,
                'oee' => $stats['oee'] * 100,
                'availability' => $stats['availability'] * 100,
                'performance' => $stats['performance'] * 100,
                'quality' => $stats['quality'] * 100,
            ];
        });

        return view('livewire.production.dashboard', compact('activeRuns', 'availableMoulds', 'topNg', 'recentOee'));
    }
}
