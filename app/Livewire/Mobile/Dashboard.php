<?php

namespace App\Livewire\Mobile;

use Livewire\Component;
use App\Models\ProductionRun;
use App\Models\MaintenanceEvent;
use Illuminate\Support\Facades\Gate;

class Dashboard extends Component
{
    public function render()
    {
        $myActiveRuns = ProductionRun::with(['mould', 'machine'])
            ->whereNull('end_ts')
            // ->where('created_by', auth()->id()) // Optional: restrict to user's runs? For now, show all active in shop
            ->latest('start_ts')
            ->limit(5)
            ->get();

        $activeWorkOrders = collect();
        if (Gate::allows('maintenance_events.create')) {
            $activeWorkOrders = MaintenanceEvent::with(['mould'])
                ->whereIn('status', ['REQUESTED', 'APPROVED', 'IN_PROGRESS'])
                ->latest('updated_at')
                ->limit(5)
                ->get();
        }

        return view('livewire.mobile.dashboard', compact('myActiveRuns', 'activeWorkOrders'))
            ->layout('layouts.mobile');
    }
}
