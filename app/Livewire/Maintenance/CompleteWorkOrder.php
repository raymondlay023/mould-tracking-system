<?php

namespace App\Livewire\Maintenance;

use App\Models\MaintenanceEvent;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class CompleteWorkOrder extends Component
{
    public MaintenanceEvent $event;
    public array $checklist = [];
    public $downtimeMin = 0;

    public function mount(MaintenanceEvent $event)
    {
        abort_if(Gate::denies('maintenance_events.create'), 403);
        $this->event = $event;
        $this->checklist = $event->checklist_data ?? [];
        $this->downtimeMin = $event->downtime_min ?? 0;
    }

    public function save()
    {
        abort_if(Gate::denies('maintenance_events.create'), 403);

        $this->validate([
            'checklist' => 'array',
            'downtimeMin' => 'required|integer|min:0',
        ]);

        if (!empty($this->checklist)) {
            foreach ($this->checklist as $item) {
                if ($this->event->pm_subtype === 'DAILY' || $this->event->pm_subtype === 'WEEKLY') {
                    // Allowed empty for now, just saving
                } elseif ($this->event->pm_subtype === 'PPM') {
                    if (empty($item['status'])) {
                        $this->addError('checklist', 'You must provide a status for all checks.');
                        return;
                    }
                } else {
                    if (empty($item['completed'])) {
                        $this->addError('checklist', 'You must complete all checklist items before finishing.');
                        return;
                    }
                }
            }
        }

        $this->event->checklist_data = $this->checklist;
        $this->event->save();

        app(\App\Actions\Maintenance\CompleteWorkOrderAction::class)->execute($this->event->id, $this->downtimeMin);

        session()->flash('success', 'Work Order Completed Successfully.');

        if (request()->routeIs('mobile.*')) {
            return redirect()->route('mobile.dashboard');
        }

        return redirect()->route('maintenance.work-orders');
    }

    public function render()
    {
        if (request()->routeIs('mobile.*')) {
            return view('livewire.maintenance.complete-work-order')->layout('layouts.mobile');
        }

        return view('livewire.maintenance.complete-work-order'); // uses default layout
    }
}
