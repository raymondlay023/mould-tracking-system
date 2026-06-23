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
    public string $maintenanceDescription = '';

    // Complete Maintenance Modal
    public bool $showCompleteModal = false;
    public ?string $completingWorkOrderId = null;
    public int $downtime_min = 0;
    public ?int $cost = null;
    public ?string $parts_used = null;
    public ?string $notes = null;

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
        $this->maintenanceType = 'CM';
        $this->maintenanceDescription = '';
        $this->showMaintenanceModal = true;
    }

    public function submitMaintenance()
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('maintenance_events.create'), 403);

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

    public function openCompleteModal($id)
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('maintenance_events.create'), 403);
        
        $this->completingWorkOrderId = $id;
        $this->downtime_min = 0;
        $this->cost = null;
        $this->parts_used = null;
        $this->notes = null;
        $this->showCompleteModal = true;
    }

    public function submitWorkOrderCompletion(\App\Actions\Maintenance\CompleteWorkOrderAction $action)
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('maintenance_events.create'), 403);

        $this->validate([
            'downtime_min' => 'required|integer|min:0',
        ]);

        $ev = \App\Models\MaintenanceEvent::findOrFail($this->completingWorkOrderId);

        $action->execute($ev, [
            'downtime_min' => $this->downtime_min,
            'cost' => $this->cost,
            'parts_used' => $this->parts_used,
            'notes' => $this->notes,
        ]);

        $this->showCompleteModal = false;
        $this->completingWorkOrderId = null;
        $this->refreshActiveRun();
        session()->flash('success', 'Work Order completed successfully.');
    }

    public function render()
    {
        return view('livewire.mobile.mould-detail')
            ->layout('layouts.mobile');
    }
}
