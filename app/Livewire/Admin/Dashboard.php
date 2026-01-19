<?php

namespace App\Livewire\Admin;

use App\Models\Mould;
use App\Models\User;
use App\Models\Machine;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Dashboard extends Component
{
    public function render()
    {
        // 1. System Stats
        $stats = [
            'moulds' => Mould::count(),
            'users' => User::count(),
            'machines' => Machine::count(),
            'active_runs' => \App\Models\ProductionRun::active()->count(),
        ];

        // 2. Recent System Activity (Audit Log)
        $activities = Activity::with('causer')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        // 3. User Roles Breakdown
        $usersByRole = \Illuminate\Support\Facades\DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->selectRaw('roles.name, count(*) as count')
            ->groupBy('roles.name')
            ->get();

        return view('livewire.admin.dashboard', compact('stats', 'activities', 'usersByRole'));
    }
}
