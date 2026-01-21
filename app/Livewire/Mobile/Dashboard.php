<?php

namespace App\Livewire\Mobile;

use Livewire\Component;
use App\Models\ProductionRun;

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

        return view('livewire.mobile.dashboard', compact('myActiveRuns'))
            ->layout('layouts.mobile');
    }
}
