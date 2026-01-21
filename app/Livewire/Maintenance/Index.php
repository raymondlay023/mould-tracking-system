<?php

namespace App\Livewire\Maintenance;

use Illuminate\Support\Facades\Gate;
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

    public ?int $downtime_min = null;

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

    // Helper: Convert UTC DB value -> User Local Time string (for Input)
    private function toUserTime($date): string
    {
        if (!$date) return '';
        $tz = auth()->user()?->timezone ?? 'Asia/Jakarta';
        return \Carbon\Carbon::parse($date)->setTimezone($tz)->format('Y-m-d\TH:i');
    }

    // Helper: Convert User Input string -> UTC Carbon object (for DB)
    private function toUtc(?string $dateString): ?\Carbon\Carbon
    {
        if (!$dateString) return null;
        $tz = auth()->user()?->timezone ?? 'Asia/Jakarta';
        return \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $dateString, $tz)->setTimezone('UTC');
    }

    private function resetForm(): void
    {
        $this->idEdit = null;
        $this->mould_id = '';
        
        // Default Start: 30 mins ago logic, but in User Time
        $tz = auth()->user()?->timezone ?? 'Asia/Jakarta';
        $nowUser = now()->setTimezone($tz);
        
        $this->start_ts = $nowUser->copy()->subMinutes(30)->format('Y-m-d\TH:i');
        $this->end_ts = $nowUser->format('Y-m-d\TH:i');
        
        $this->type = 'PM';
        $this->description = null;
        $this->parts_used = null;
        $this->downtime_min = null;
        $this->cost = null;
        $this->next_due_shot = null;
        $this->next_due_date = null;
        $this->performed_by = auth()->user()?->name;
        $this->notes = null;
        $this->resetValidation();
    }

    protected function rules(): array
    {
        return [
            'mould_id' => ['required', 'exists:moulds,id'],
            'start_ts' => ['required', 'date'],
            'type' => ['required', 'in:PM,CM'],
            'description' => ['nullable', 'string', 'max:255'],
            'next_due_shot' => ['nullable', 'integer', 'min:0'],
            'next_due_date' => ['nullable', 'date'],
            'performed_by' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'end_ts' => ['required', 'date', 'after:start_ts'],
            'downtime_min' => ['required', 'integer', 'min:0'],
            'parts_used' => ['nullable', 'string', 'max:5000'],
            'cost' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function createNew(): void
    {
        $this->resetForm();
    }

    public function edit(string $id): void
    {
        $e = MaintenanceEvent::findOrFail($id);
        $this->idEdit = $e->id;
        $this->mould_id = $e->mould_id;
        
        // Convert DB (UTC) -> User Time
        $this->start_ts = $this->toUserTime($e->start_ts);
        $this->end_ts = $this->toUserTime($e->end_ts);
        
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
        // Security Check
        abort_if(\Illuminate\Support\Facades\Gate::denies('create_maintenance_events'), 403, 'Unauthorized');

        $v = $this->validate();

        $loc = \App\Models\LocationHistory::query()
            ->where('mould_id', '=', $this->mould_id)
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

        // Always COMPLETED for Log
        $startTs = $this->toUtc($this->start_ts);
        $endTs = $this->toUtc($this->end_ts);

        MaintenanceEvent::updateOrCreate(
            ['id' => $this->idEdit],
            [
                'mould_id' => $this->mould_id,
                'start_ts' => $startTs,
                'type' => $this->type,
                'description' => $this->description,
                'next_due_shot' => $this->next_due_shot,
                'next_due_date' => $this->next_due_date ?: null,
                'performed_by' => $this->performed_by ?: (auth()->user()?->name),
                'notes' => $this->notes,
                'machine_id' => $autoMachineId,
                'plant_id' => $autoPlantId,
                
                'status' => 'COMPLETED',
                'end_ts' => $endTs,
                'downtime_min' => $this->downtime_min,
                'parts_used' => $this->parts_used,
                'cost' => $this->cost,
            ]
        );

        session()->flash('success', $this->idEdit ? 'Maintenance updated.' : 'Maintenance logged.');
        $this->createNew();
    }

    public function delete(string $id): void
    {
        // Security Check
        abort_if(Gate::denies('delete_maintenance_events'), 403, 'Unauthorized');

        MaintenanceEvent::where('id', '=', $id, 'and')->delete();
        session()->flash('success', 'Maintenance deleted.');
        $this->createNew();
    }

    public function render()
    {
        $moulds = Mould::orderBy('code', 'asc')->get();

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
