<?php

namespace App\Livewire\Mobile;

use Livewire\Component;
use App\Models\ProductionRun;

class Jobs extends Component
{
    public function render()
    {
        $activeRuns = ProductionRun::with(['mould', 'machine'])
            ->whereNull('end_ts')
            ->latest('start_ts')
            ->get();

        return view('livewire.mobile.jobs', compact('activeRuns'))
            ->layout('layouts.mobile');
    }
}
