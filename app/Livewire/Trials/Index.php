<?php

namespace App\Livewire\Trials;

use App\Models\TrialEvent;
use App\Models\Mould;
use App\Models\Machine;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    public ?string $trialId = null;

    public string $mould_id = '';
    public string $machine_id = '';
    public string $start_ts = '';
    public string $end_ts = '';
    public ?string $purpose = null;
    public ?string $parameters = null;
    public ?string $notes = null;

    public function updatedSearch(): void { $this->resetPage(); }

    protected function rules(): array
    {
        return [
            'mould_id' => ['required','exists:moulds,id'],
            'machine_id' => ['required','exists:machines,id'],
            'start_ts' => ['required','date'],
            'end_ts' => ['required','date','after:start_ts'],
            'purpose' => ['nullable','string','max:255'],
            'parameters' => ['nullable','string','max:5000'],
            'notes' => ['nullable','string','max:5000'],
        ];
    }

    public function mount(): void
    {
        $this->resetForm();
    }

    public function createNew(): void
    {
        $this->resetForm();
        $this->resetValidation();
    }

    public function edit(string $id): void
    {
        $t = TrialEvent::findOrFail($id);
        $this->trialId = $t->id;
        $this->mould_id = $t->mould_id;
        $this->machine_id = $t->machine_id;
        $this->start_ts = $t->start_ts?->format('Y-m-d\TH:i') ?? '';
        $this->end_ts = $t->end_ts?->format('Y-m-d\TH:i') ?? '';
        $this->purpose = $t->purpose;
        $this->parameters = $t->parameters;
        $this->notes = $t->notes;
        $this->resetValidation();
    }

    public function save(): void
    {
        $v = $this->validate();

        TrialEvent::updateOrCreate(
            ['id' => $this->trialId],
            $v
        );

        session()->flash('success', $this->trialId ? 'Trial updated.' : 'Trial created.');
        $this->createNew();
    }

    public function delete(string $id): void
    {
        TrialEvent::where('id', $id)->delete();
        session()->flash('success', 'Trial deleted.');
        $this->createNew();
    }

    public function approveGo(string $id): void
    {
        $this->approve($id, true);
    }

    public function approveNoGo(string $id): void
    {
        $this->approve($id, false);
    }

    private function approve(string $id, bool $go): void
    {
        $userName = auth()->user()?->name ?? 'Unknown';
        $now = now();

        DB::transaction(function () use ($id, $go, $userName, $now) {
            $trial = TrialEvent::lockForUpdate()->findOrFail($id);

            $trial->update([
                'approved' => true,
                'approved_go' => $go,
                'approved_by' => $userName,
                'approved_at' => $now,
            ]);

            if ($go) {
                // update RMP on mould
                $trial->mould()->update([
                    'rmp_last_at' => $now,
                    'rmp_approved_by' => $userName,
                ]);
            }
        });

        session()->flash('success', $go ? 'Trial approved (GO) + RMP updated.' : 'Trial approved (NO-GO).');
    }

    private function resetForm(): void
    {
        $this->trialId = null;
        $this->mould_id = '';
        $this->machine_id = '';
        $this->start_ts = now()->subMinutes(30)->format('Y-m-d\TH:i');
        $this->end_ts = now()->format('Y-m-d\TH:i');
        $this->purpose = null;
        $this->parameters = null;
        $this->notes = null;
    }

    public function render()
    {
        $moulds = Mould::orderBy('code')->get();
        $machines = Machine::orderBy('code')->get();

        $trials = TrialEvent::query()
            ->with(['mould','machine'])
            ->when($this->search !== '', function($q){
                $q->whereHas('mould', fn($mq) => $mq->where('code','like',"%{$this->search}%")->orWhere('name','like',"%{$this->search}%"))
                  ->orWhereHas('machine', fn($mq) => $mq->where('code','like',"%{$this->search}%")->orWhere('name','like',"%{$this->search}%"));
            })
            ->orderByDesc('end_ts')
            ->paginate($this->perPage);

        return view('livewire.trials.index', compact('trials','moulds','machines'));
    }
}
