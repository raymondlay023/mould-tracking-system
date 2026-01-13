<?php

namespace App\Livewire\Setups;

use App\Models\SetupEvent;
use App\Models\Mould;
use App\Models\Machine;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    public ?string $setupId = null;

    public string $mould_id = '';
    public string $machine_id = '';
    public string $start_ts = '';
    public string $end_ts = '';
    public ?int $target_min = null;
    public ?int $actual_min = null;
    public ?string $loss_reason = null;
    public ?string $operator_name = null;
    public ?string $notes = null;

    public function updatedSearch(): void { $this->resetPage(); }

    protected function rules(): array
    {
        return [
            'mould_id' => ['required','exists:moulds,id'],
            'machine_id' => ['required','exists:machines,id'],
            'start_ts' => ['required','date'],
            'end_ts' => ['required','date','after:start_ts'],
            'target_min' => ['nullable','integer','min:0'],
            'actual_min' => ['nullable','integer','min:0'],
            'loss_reason' => ['nullable','string','max:255'],
            'operator_name' => ['nullable','string','max:100'],
            'notes' => ['nullable','string','max:2000'],
        ];
    }

    public function createNew(): void
    {
        $this->resetForm();
        $this->resetValidation();
    }

    public function edit(string $id): void
    {
        $s = SetupEvent::findOrFail($id);
        $this->setupId = $s->id;
        $this->mould_id = $s->mould_id;
        $this->machine_id = $s->machine_id;
        $this->start_ts = $s->start_ts?->format('Y-m-d\TH:i') ?? '';
        $this->end_ts = $s->end_ts?->format('Y-m-d\TH:i') ?? '';
        $this->target_min = $s->target_min;
        $this->actual_min = $s->actual_min;
        $this->loss_reason = $s->loss_reason;
        $this->operator_name = $s->operator_name;
        $this->notes = $s->notes;
        $this->resetValidation();
    }

    public function save(): void
    {
        $v = $this->validate();

        SetupEvent::updateOrCreate(
            ['id' => $this->setupId],
            [
                ...$v,
                'start_ts' => $v['start_ts'],
                'end_ts' => $v['end_ts'],
            ]
        );

        session()->flash('success', $this->setupId ? 'Setup updated.' : 'Setup created.');
        $this->createNew();
    }

    public function delete(string $id): void
    {
        SetupEvent::where('id', $id)->delete();
        session()->flash('success', 'Setup deleted.');
        $this->createNew();
    }

    private function resetForm(): void
    {
        $this->setupId = null;
        $this->mould_id = '';
        $this->machine_id = '';
        $this->start_ts = now()->subMinutes(30)->format('Y-m-d\TH:i');
        $this->end_ts = now()->format('Y-m-d\TH:i');
        $this->target_min = null;
        $this->actual_min = null;
        $this->loss_reason = null;
        $this->operator_name = null;
        $this->notes = null;
    }

    public function mount(): void
    {
        $this->resetForm();
    }

    public function render()
    {
        $moulds = Mould::orderBy('code')->get();
        $machines = Machine::orderBy('code')->get();

        $setups = SetupEvent::query()
            ->with(['mould','machine'])
            ->when($this->search !== '', function($q){
                $q->whereHas('mould', fn($mq) => $mq->where('code','like',"%{$this->search}%")->orWhere('name','like',"%{$this->search}%"))
                  ->orWhereHas('machine', fn($mq) => $mq->where('code','like',"%{$this->search}%")->orWhere('name','like',"%{$this->search}%"));
            })
            ->orderByDesc('end_ts')
            ->paginate($this->perPage);

        return view('livewire.setups.index', compact('setups','moulds','machines'));
    }
}
