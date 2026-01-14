<?php

namespace App\Livewire\Maintenance;

use App\Models\MaintenanceEvent;
use App\Models\Mould;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 10;

    public ?string $idEdit = null;

    public string $mould_id = '';

    public string $start_ts = '';

    public string $end_ts = '';

    public string $type = 'PM';

    public ?string $description = null;

    public ?string $parts_used = null;

    public int $downtime_min = 0;

    public ?int $cost = null;

    public ?int $next_due_shot = null;

    public ?string $next_due_date = null;

    public ?string $performed_by = null;

    public ?string $notes = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->idEdit = null;
        $this->mould_id = '';
        $this->start_ts = now()->subMinutes(30)->format('Y-m-d\TH:i');
        $this->end_ts = now()->format('Y-m-d\TH:i');
        $this->type = 'PM';
        $this->description = null;
        $this->parts_used = null;
        $this->downtime_min = 0;
        $this->cost = null;
        $this->next_due_shot = null;
        $this->next_due_date = null;
        $this->performed_by = auth()->user()?->name;
        $this->notes = null;
    }

    protected function rules(): array
    {
        return [
            'mould_id' => ['required', 'exists:moulds,id'],
            'start_ts' => ['required', 'date'],
            'end_ts' => ['required', 'date', 'after:start_ts'],
            'type' => ['required', 'in:PM,CM'],
            'description' => ['nullable', 'string', 'max:255'],
            'parts_used' => ['nullable', 'string', 'max:5000'],
            'downtime_min' => ['required', 'integer', 'min:0'],
            'cost' => ['nullable', 'integer', 'min:0'],
            'next_due_shot' => ['nullable', 'integer', 'min:0'],
            'next_due_date' => ['nullable', 'date'],
            'performed_by' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function createNew(): void
    {
        $this->resetForm();
        $this->resetValidation();
    }

    public function edit(string $id): void
    {
        $e = MaintenanceEvent::findOrFail($id);
        $this->idEdit = $e->id;
        $this->mould_id = $e->mould_id;
        $this->start_ts = $e->start_ts?->format('Y-m-d\TH:i') ?? '';
        $this->end_ts = $e->end_ts?->format('Y-m-d\TH:i') ?? '';
        $this->type = $e->type;
        $this->description = $e->description;
        $this->parts_used = $e->parts_used;
        $this->downtime_min = (int) $e->downtime_min;
        $this->cost = $e->cost;
        $this->next_due_shot = $e->next_due_shot;
        $this->next_due_date = $e->next_due_date?->format('Y-m-d');
        $this->performed_by = $e->performed_by;
        $this->notes = $e->notes;
        $this->resetValidation();
    }

    public function save(): void
    {
        $v = $this->validate();

        $loc = \App\Models\LocationHistory::query()
            ->where('mould_id', $v['mould_id'])
            ->whereNull('end_ts')
            ->first();

        $autoMachineId = null;
        $autoPlantId = null;

        if ($loc) {
            $autoPlantId = $loc->plant_id;

            if ($loc->location === 'MACHINE') {
                $autoMachineId = $loc->machine_id;
            }
        }

        MaintenanceEvent::updateOrCreate(
            ['id' => $this->idEdit],
            [
                ...$v,
                'next_due_date' => $v['next_due_date'] ?: null,
                'performed_by' => $v['performed_by'] ?: (auth()->user()?->name),
                'machine_id' => $autoMachineId,
                'plant_id' => $autoPlantId,

            ]
        );

        session()->flash('success', $this->idEdit ? 'Maintenance updated.' : 'Maintenance created.');
        $this->createNew();
    }

    public function delete(string $id): void
    {
        MaintenanceEvent::where('id', $id)->delete();
        session()->flash('success', 'Maintenance deleted.');
        $this->createNew();
    }

    public function render()
    {
        $moulds = Mould::orderBy('code')->get();

        $events = MaintenanceEvent::query()
            ->with('mould')
            ->when($this->search !== '', function ($q) {
                $q->whereHas('mould', fn ($mq) => $mq->where('code', 'like', "%{$this->search}%")->orWhere('name', 'like', "%{$this->search}%"))
                    ->orWhere('description', 'like', "%{$this->search}%");
            })
            ->orderByDesc('end_ts')
            ->paginate($this->perPage);

        return view('livewire.maintenance.index', compact('events', 'moulds'));
    }
}
