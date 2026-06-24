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

    public function edit(string $id): void
    {
        abort_if(auth()->user()->cannot('admin_panel.view'), 403);

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
        abort_if(!$this->idEdit, 403, 'Creation from this form is disabled.');
        abort_if(auth()->user()->cannot('admin_panel.view'), 403);

        $this->validate();

        $event = MaintenanceEvent::findOrFail($this->idEdit);

        $event->update([
            'mould_id' => $this->mould_id,
            'type' => $this->type,
            'description' => $this->description,
            'start_ts' => $this->toUtc($this->start_ts),
            'end_ts' => $this->toUtc($this->end_ts),
            'downtime_min' => $this->downtime_min ?: 0,
            'cost' => $this->cost,
            'parts_used' => $this->parts_used,
            'next_due_shot' => $this->next_due_shot,
            'next_due_date' => $this->next_due_date,
            'performed_by' => $this->performed_by,
            'notes' => $this->notes,
        ]);

        session()->flash('success', 'Maintenance event updated successfully.');
        $this->resetForm();
    }

    public function delete(string $id): void
    {
        abort_if(auth()->user()->cannot('admin_panel.view'), 403);
        
        MaintenanceEvent::where('id', '=', $id)->delete();
        session()->flash('success', 'Maintenance deleted.');
        $this->resetForm();
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
