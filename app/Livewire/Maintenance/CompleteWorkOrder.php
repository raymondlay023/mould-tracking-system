<?php

namespace App\Livewire\Maintenance;

use App\Models\MaintenanceEvent;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;

class CompleteWorkOrder extends Component
{
    use WithFileUploads;
    public MaintenanceEvent $event;
    public array $checklist = [];
    public $downtimeMin;
    public $cost = null;
    public $partsUsed = null;
    public $notes = null;
    public $photos = [];
    public $isMobile = false;

    public function mount(MaintenanceEvent $event)
    {
        abort_if(Gate::denies('maintenance_events.create'), 403);
        $this->event = $event;
        $this->checklist = $event->checklist_data ?? [];
        $this->downtimeMin = $event->downtime_min === 0 ? null : $event->downtime_min;
        $this->isMobile = request()->routeIs('mobile.*');
    }

    public function save()
    {
        abort_if(Gate::denies('maintenance_events.create'), 403);

        try {
            $this->validate([
                'checklist' => 'array',
                'downtimeMin' => 'required|integer|min:0',
                'cost' => 'nullable|numeric|min:0',
                'partsUsed' => 'nullable|string',
                'notes' => 'nullable|string',
                'photos.*' => 'nullable|image|max:5120', // 5MB max
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('scrollToFirstError');
            throw $e;
        }

        if (!empty($this->checklist)) {
            foreach ($this->checklist as $idx => $item) {
                if ($this->event->pm_subtype === 'DAILY' || $this->event->pm_subtype === 'WEEKLY') {
                    // Allowed empty for now, just saving
                } elseif ($this->event->pm_subtype === 'PPM') {
                    if (empty($item['status'])) {
                        $this->addError('checklist', 'You must provide a status for all checks.');
                        $this->dispatch('scrollToFirstError');
                        return;
                    }
                    if ($item['status'] === 'NG' && isset($this->photos[$idx])) {
                        $path = $this->photos[$idx]->store('maintenance_photos', 'public');
                        $this->checklist[$idx]['photo_path'] = $path;
                    }
                } else {
                    if (empty($item['completed'])) {
                        $this->addError('checklist', 'You must complete all checklist items before finishing.');
                        $this->dispatch('scrollToFirstError');
                        return;
                    }
                }
            }
        }

        $this->event->checklist_data = $this->checklist;
        $this->event->save();

        app(\App\Actions\Maintenance\CompleteWorkOrderAction::class)->execute($this->event, [
            'downtime_min' => $this->downtimeMin,
            'cost' => $this->cost,
            'parts_used' => $this->partsUsed,
            'notes' => $this->notes,
        ]);

        session()->flash('success', 'Work Order Completed Successfully.');

        if ($this->isMobile) {
            return redirect()->route('mobile.dashboard');
        }

        return redirect()->route('maintenance.work-orders');
    }

    public function render()
    {
        if ($this->isMobile) {
            return view('livewire.maintenance.complete-work-order')->layout('layouts.mobile');
        }

        return view('livewire.maintenance.complete-work-order'); // uses default layout
    }
}
