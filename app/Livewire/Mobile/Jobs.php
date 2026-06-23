<?php

namespace App\Livewire\Mobile;

use Livewire\Component;
use App\Models\ProductionRun;

class Jobs extends Component
{
    public ?string $machine = null;

    protected $queryString = ['machine'];

    public function render()
    {
        $activeRunsQuery = ProductionRun::with(['mould', 'machine'])
            ->whereNull('end_ts')
            ->latest('start_ts');

        if ($this->machine) {
            $activeRunsQuery->where('machine_id', $this->machine);
        }

        $activeRuns = $activeRunsQuery->get();

        return view('livewire.mobile.jobs', compact('activeRuns'))
            ->layout('layouts.mobile');
    }
}
