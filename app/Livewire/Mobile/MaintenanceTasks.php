<?php

namespace App\Livewire\Mobile;

use App\Models\MaintenanceEvent;
use Livewire\Component;

class MaintenanceTasks extends Component
{
    public function render()
    {
        $tasks = MaintenanceEvent::with('mould')
            ->whereIn('status', ['REQUESTED', 'APPROVED', 'IN_PROGRESS', 'IN_REVIEW'])
            ->orderByRaw("FIELD(status, 'IN_PROGRESS', 'APPROVED', 'REQUESTED', 'IN_REVIEW')")
            ->orderBy('start_ts', 'asc')
            ->get();

        return view('livewire.mobile.maintenance-tasks', compact('tasks'))
            ->layout('layouts.mobile');
    }

    public function startWork($id)
    {
        $ev = MaintenanceEvent::findOrFail($id);
        
        if ($ev->status === 'REQUESTED') {
            // Bypass logic
            $ev->update([
                'status' => 'IN_PROGRESS',
                'notes' => trim($ev->notes . "\n[AUDIT] Bypassed supervisor approval on " . now()->toDateTimeString())
            ]);
            session()->flash('success', "Bypassed approval and started work on {$ev->mould->code}");
        } elseif ($ev->status === 'APPROVED') {
            $ev->update(['status' => 'IN_PROGRESS']);
            session()->flash('success', "Started work on {$ev->mould->code}");
        }
    }
}
