<?php

namespace App\Livewire\Qa;

use App\Models\TrialEvent;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        // 1. Pending Trials (Need Approval)
        $pendingTrials = TrialEvent::with(['mould', 'machine'])
            ->where('approved', false)
            ->orderBy('start_ts') // Oldest first (FIFO)
            ->get();

        // 2. Top NG (Last 30 days for broader QA analysis)
        $ngFrom = now()->subDays(30)->toDateString();
        $topNg = DB::table('production_runs as pr')
            ->join('moulds as mo', 'pr.mould_id', '=', 'mo.id')
            ->whereNotNull('pr.end_ts')
            ->whereDate('pr.end_ts', '>=', $ngFrom)
            ->selectRaw('
                mo.code as mould_code,
                mo.name as mould_name,
                SUM(pr.ok_part) as ok_sum,
                SUM(pr.ng_part) as ng_sum
            ')
            ->groupBy('mo.code', 'mo.name')
            ->orderByRaw('(CASE WHEN (SUM(pr.ok_part)+SUM(pr.ng_part))=0 THEN 0 ELSE (SUM(pr.ng_part)/(SUM(pr.ok_part)+SUM(pr.ng_part))) END) DESC')
            ->limit(10)
            ->get();

        // 3. Recent Trial Results (History)
        $recentTrials = TrialEvent::with(['mould'])
            ->where('approved', true)
            ->orderByDesc('approved_at')
            ->limit(5)
            ->get();

        return view('livewire.qa.dashboard', compact('pendingTrials', 'topNg', 'recentTrials'));
    }
}
