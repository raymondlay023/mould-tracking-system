<?php

namespace App\Livewire\Mobile;

use App\Models\MaintenanceEvent;
use App\Models\ProductionRun;
use Livewire\Component;

class History extends Component
{
    public function render()
    {
        $runs = collect();
        $maintenance = collect();

        if (auth()->user()->can('production.view') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Supervisor')) {
            $runs = ProductionRun::with(['mould', 'machine'])
                ->whereNotNull('end_ts')
                ->latest('end_ts')
                ->limit(20)
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => 'Production Run',
                        'title' => 'Run Completed',
                        'mould' => $item->mould->code ?? 'Unknown Mould',
                        'subtitle' => 'Machine: ' . ($item->machine->code ?? 'N/A'),
                        'date' => $item->end_ts,
                        'id' => $item->id,
                        'icon' => 'cube',
                    ];
                });
        }

        if (auth()->user()->can('maintenance.view') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Supervisor')) {
            $maintenance = MaintenanceEvent::with(['mould'])
                ->whereNotNull('end_ts')
                ->latest('end_ts')
                ->limit(20)
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => 'Maintenance Task',
                        'title' => $item->type . ' Completed',
                        'mould' => $item->mould->code ?? 'Unknown Mould',
                        'subtitle' => \Illuminate\Support\Str::limit($item->description ?? 'No description', 40),
                        'date' => $item->end_ts,
                        'id' => $item->id,
                        'icon' => 'wrench',
                    ];
                });
        }

        $activities = $runs->concat($maintenance)->sortByDesc('date')->take(30);

        return view('livewire.mobile.history', [
            'activities' => $activities,
        ])->layout('layouts.mobile');
    }
}
