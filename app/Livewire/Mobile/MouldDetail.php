<?php

namespace App\Livewire\Mobile;

use App\Models\Mould;
use App\Models\ProductionRun;
use Livewire\Component;
use Livewire\Attributes\Computed;

class MouldDetail extends Component
{
    public Mould $mould;
    public $activeRun = null;

    // Create Maintenance Modal
    public bool $showMaintenanceModal = false;
    public string $maintenanceType = 'CM';
    public ?string $maintenancePmSubtype = null;
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

    #[Computed]
    public function activeWorkOrders()
    {
        if (\Illuminate\Support\Facades\Gate::allows('maintenance_events.create')) {
            return \App\Models\MaintenanceEvent::where('mould_id', $this->mould->id)
                ->whereIn('status', ['REQUESTED', 'APPROVED', 'IN_PROGRESS'])
                ->orderByRaw("FIELD(status, 'IN_PROGRESS', 'APPROVED', 'REQUESTED')")
                ->latest('start_ts')
                ->get();
        }
        return collect();
    }

    public function openMaintenanceModal()
    {
        $this->reset(['showMaintenanceModal', 'maintenanceType', 'maintenancePmSubtype', 'maintenanceDescription']);
        $this->showMaintenanceModal = true;
    }

    public function submitMaintenance()
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('maintenance_events.create'), 403);

        $this->validate([
            'maintenanceType' => 'required|in:CM,PM',
            'maintenancePmSubtype' => 'nullable|in:DAILY,WEEKLY,PPM',
            'maintenanceDescription' => 'required_if:maintenanceType,CM|nullable|string|max:500'
        ]);

        \App\Models\MaintenanceEvent::create([
            'mould_id' => $this->mould->id,
            'type' => $this->maintenanceType,
            'pm_subtype' => $this->maintenanceType === 'PM' ? $this->maintenancePmSubtype : null,
            'description' => $this->maintenanceDescription ?: ($this->maintenancePmSubtype ? $this->maintenancePmSubtype . ' Maintenance' : 'Preventive Maintenance'),
            'start_ts' => now(),
            'status' => 'REQUESTED',
        ]);

        $this->showMaintenanceModal = false;

        $recipients = \App\Models\User::permission('view_maintenance_section')->get();
        if ($recipients->count() > 0) {
            \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\Maintenance\WorkOrderRequested(\App\Models\MaintenanceEvent::latest('id')->first()));
        }

        session()->flash('success', 'Maintenance log submitted successfully.');
        $this->refreshActiveRun();
    }

    public function startWorkOrder($id)
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('maintenance_events.create'), 403);
        
        $ev = \App\Models\MaintenanceEvent::findOrFail($id);
        $ev->update(['status' => 'IN_PROGRESS']);
        
        $this->refreshActiveRun();
        session()->flash('success', "Work on {$this->mould->code} started.");
    }

    public function render()
    {
        return view('livewire.mobile.mould-detail')
            ->layout('layouts.mobile');
    }
}
