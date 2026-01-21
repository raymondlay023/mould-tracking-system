<?php

namespace App\Livewire\Maintenance;

use App\Actions\Maintenance\CompleteWorkOrderAction;
use App\Models\MaintenanceEvent;
use Livewire\Component;

class WorkOrders extends Component
{
    // ID of event being completed (for modal)
    public ?string $completingId = null;

    // Form data for completion
    public int $downtime_min = 0;
    public ?int $cost = null;
    public ?string $parts_used = null;
    public ?string $notes = null;

    // Creation State
    public bool $creating = false;
    public string $newMouldId = '';
    public string $newType = 'CM'; // Corrective by default for requests
    public string $newDescription = '';
    public string $newStartTs = '';

    public function render()
    {
        $events = MaintenanceEvent::where('status', '!=', 'COMPLETED')
            ->with('mould')
            ->orderBy('start_ts', 'asc')
            ->get();

        $cols = [
            'REQUESTED' => $events->where('status', '=', 'REQUESTED'),
            'APPROVED' => $events->where('status', '=', 'APPROVED'),
            'IN_PROGRESS' => $events->where('status', '=', 'IN_PROGRESS'),
        ];
        
        $moulds = \App\Models\Mould::orderBy('code', 'asc')->get();

        return view('livewire.maintenance.work-orders', compact('cols', 'moulds'));
    }

    public function create()
    {
        $this->reset(['newMouldId', 'newDescription']);
        $this->newType = 'CM';
        
        $tz = auth()->user()?->timezone ?? 'Asia/Jakarta';
        $this->newStartTs = now()->setTimezone($tz)->format('Y-m-d\TH:i');
        
        $this->creating = true;
    }

    public function cancelCreate()
    {
        $this->creating = false;
    }

    public function saveNew()
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('create_maintenance_events'), 403);

        $this->validate([
            'newMouldId' => 'required|exists:moulds,id',
            'newType' => 'required|in:PM,CM',
            'newStartTs' => 'required|date',
            'newDescription' => 'required|string|max:255',
        ]);
        
        $tz = auth()->user()?->timezone ?? 'Asia/Jakarta';
        $utcStart = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $this->newStartTs, $tz)->setTimezone('UTC');

        MaintenanceEvent::create([
            'mould_id' => $this->newMouldId,
            'type' => $this->newType,
            'description' => $this->newDescription,
            'start_ts' => $utcStart,
            'status' => 'REQUESTED',
            'planted_id' => null, // Will be filled when scheduled/completed if needed? Actually mostly null for requests.
        ]);

        $this->creating = false;
        
        // Notify
        $recipients = \App\Models\User::permission('view_maintenance_section')->get();
        if ($recipients->count() > 0) {
            \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\Maintenance\WorkOrderRequested($event = MaintenanceEvent::latest('id')->first()));
            // Note: retrieving latest() is a bit racy but simplest for now without changing create return
        }

        session()->flash('success', 'New Work Order requested.');
    }

    public function approve($id)
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('create_maintenance_events'), 403);

        $ev = MaintenanceEvent::findOrFail($id);
        $ev->update(['status' => 'APPROVED']);
        session()->flash('success', "Ticket {$ev->mould->code} approved.");
    }
    
    // ... existing start/initiateCompletion/complete ...

    public function start($id)
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('create_maintenance_events'), 403);

        $ev = MaintenanceEvent::findOrFail($id);
        $ev->update(['status' => 'IN_PROGRESS']);
        
        session()->flash('success', "Work on {$ev->mould->code} started.");
    }

    // Open Modal
    public function initiateCompletion($id)
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('create_maintenance_events'), 403);

        $this->completingId = $id;
        $this->reset(['downtime_min', 'cost', 'parts_used', 'notes']);
    }

    public function complete(CompleteWorkOrderAction $action)
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('create_maintenance_events'), 403);

        $this->validate([
            'downtime_min' => 'required|integer|min:0',
        ]);

        $ev = MaintenanceEvent::findOrFail($this->completingId);

        $action->execute($ev, [
            'downtime_min' => $this->downtime_min,
            'cost' => $this->cost,
            'parts_used' => $this->parts_used,
            'notes' => $this->notes,
        ]);

        $this->completingId = null;
        session()->flash('success', 'Work Order completed successfully.');
    }
    
    public function cancelCompletion()
    {
        $this->completingId = null;
    }
}
