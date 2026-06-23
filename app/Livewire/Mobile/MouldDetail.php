<?php

namespace App\Livewire\Mobile;

use App\Models\Mould;
use App\Models\ProductionRun;
use Livewire\Component;

class MouldDetail extends Component
{
    public Mould $mould;
    public $activeRun = null;

    public bool $showMaintenanceModal = false;
    public string $maintenanceType = 'CM';
    public string $maintenanceDescription = '';

    public function mount(Mould $mould)
    {
        $this->mould = $mould;
        $this->refreshActiveRun();
    }

    private function refreshActiveRun()
    {
        $this->activeRun = ProductionRun::where('mould_id', $this->mould->id)
            ->whereNull('end_ts')
            ->first();
    }

    public function openMaintenanceModal()
    {
        $this->maintenanceType = 'CM';
        $this->maintenanceDescription = '';
        $this->showMaintenanceModal = true;
    }

    public function submitMaintenance()
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('create_maintenance_events'), 403);

        $this->validate([
            'maintenanceType' => 'required|in:PM,CM',
            'maintenanceDescription' => 'required|string|max:255',
        ]);

        \App\Models\MaintenanceEvent::create([
            'mould_id' => $this->mould->id,
            'type' => $this->maintenanceType,
            'description' => $this->maintenanceDescription,
            'start_ts' => now(),
            'status' => 'REQUESTED',
        ]);

        $this->showMaintenanceModal = false;

        $recipients = \App\Models\User::permission('view_maintenance_section')->get();
        if ($recipients->count() > 0) {
            \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\Maintenance\WorkOrderRequested(\App\Models\MaintenanceEvent::latest('id')->first()));
        }

        session()->flash('success', 'Maintenance log submitted successfully.');
    }

    public function render()
    {
        return view('livewire.mobile.mould-detail')
            ->layout('layouts.mobile');
    }
}
