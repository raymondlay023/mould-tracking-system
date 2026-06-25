<?php

namespace App\Livewire\Maintenance;

use App\Actions\Maintenance\CompleteWorkOrderAction;
use App\Models\MaintenanceEvent;
use Livewire\Component;

class WorkOrders extends Component
{
    // Creation State
    public bool $creating = false;
    public string $newMouldId = '';
    public string $newType = 'CM'; // Corrective by default for requests
    public ?string $newPmSubtype = null;
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
            'IN_REVIEW' => $events->where('status', '=', 'IN_REVIEW'),
        ];
        
        $moulds = \App\Models\Mould::orderBy('code', 'asc')->get();

        return view('livewire.maintenance.work-orders', compact('cols', 'moulds'));
    }

    public function create()
    {
        $this->reset(['newMouldId', 'newDescription', 'newPmSubtype']);
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
        abort_if(\Illuminate\Support\Facades\Gate::denies('maintenance_events.create'), 403);

        $this->validate([
            'newMouldId' => 'required|exists:moulds,id',
            'newType' => 'required|in:PM,CM',
            'newPmSubtype' => 'nullable|in:DAILY,WEEKLY,PPM',
            'newStartTs' => 'required|date',
            'newDescription' => 'required_if:newType,CM|nullable|string|max:255',
        ]);
        
        $tz = auth()->user()?->timezone ?? 'Asia/Jakarta';
        $utcStart = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $this->newStartTs, $tz)->setTimezone('UTC');

        $event = MaintenanceEvent::create([
            'mould_id' => $this->newMouldId,
            'type' => $this->newType,
            'pm_subtype' => $this->newType === 'PM' ? $this->newPmSubtype : null,
            'description' => $this->newDescription ?: ($this->newPmSubtype ? $this->newPmSubtype . ' Maintenance' : 'Preventive Maintenance'),
            'start_ts' => $utcStart,
            'status' => 'REQUESTED',
            'planted_id' => null, // Will be filled when scheduled/completed if needed? Actually mostly null for requests.
        ]);

        $this->creating = false;
        
        // Notify
        $recipients = \App\Models\User::permission('maintenance.view')->get();
        if ($recipients->count() > 0) {
            \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\Maintenance\WorkOrderRequested($event));
        }

        session()->flash('success', 'New Work Order requested.');
    }

    public function approve($id)
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('maintenance_events.create'), 403);

        $ev = MaintenanceEvent::findOrFail($id);
        $ev->update(['status' => 'APPROVED']);
        session()->flash('success', "Ticket {$ev->mould->code} approved.");
    }
    
    // ... existing start/initiateCompletion/complete ...

    public function start($id)
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('maintenance_events.create'), 403);

        $ev = MaintenanceEvent::findOrFail($id);
        $ev->update(['status' => 'IN_PROGRESS']);
        
        session()->flash('success', "Work on {$ev->mould->code} started.");
    }

    public function signOff($id)
    {
        abort_if(\Illuminate\Support\Facades\Gate::denies('maintenance_events.create'), 403);

        $ev = MaintenanceEvent::findOrFail($id);
        
        if ($ev->type === 'PM' && $ev->pm_subtype === 'PPM') {
            $mould = $ev->mould;
            // PM Reset Logic
            $mould->last_pm_at_shot = $mould->total_shots ?? 0;
            $mould->last_pm_at_ts = now();
            
            // If mould was in IN_MAINTENANCE status, free it
            if ($mould->status === \App\Enums\MouldStatus::IN_MAINTENANCE) {
                $mould->status = \App\Enums\MouldStatus::AVAILABLE;
            }
            $mould->save();
        }

        $ev->update(['status' => 'COMPLETED']);
        
        session()->flash('success', "Work on {$ev->mould->code} signed off and completed.");
    }
}
