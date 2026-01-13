<?php

namespace App\Livewire\Moulds;

use App\Models\Mould;
use App\Models\Machine;
use App\Models\ProductionRun;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Show extends Component
{
    public Mould $mould;

    public ?ProductionRun $activeRun = null;

    public string $machine_id = '';
    public ?string $operator_name = null;
    public ?string $notes = null;

    public function mount(Mould $mould): void
    {
        $this->mould = $mould;
        $this->refreshActiveRun();
    }

    private function refreshActiveRun(): void
    {
        $this->activeRun = ProductionRun::query()
            ->where('mould_id', $this->mould->id)
            ->whereNull('end_ts')
            ->with('machine.plant','machine.zone')
            ->first();
    }

    protected function rules(): array
    {
        return [
            'machine_id' => ['required','exists:machines,id'],
            'operator_name' => ['nullable','string','max:100'],
            'notes' => ['nullable','string','max:2000'],
        ];
    }

    public function startRun(): void
    {
        if ($this->activeRun) {
            session()->flash('error', 'Mould ini masih punya run aktif. Silakan close dulu.');
            return;
        }

        $validated = $this->validate();

        // optional: 1 run aktif per machine
        $machineHasActive = ProductionRun::query()
            ->where('machine_id', $validated['machine_id'])
            ->whereNull('end_ts')
            ->exists();

        if ($machineHasActive) {
            $this->addError('machine_id', 'Machine ini sedang dipakai run aktif.');
            return;
        }

        DB::transaction(function () use ($validated) {
            ProductionRun::create([
                'mould_id' => $this->mould->id,
                'machine_id' => $validated['machine_id'],
                'start_ts' => now(),
                'end_ts' => null,
                'cavities_snapshot' => (int) $this->mould->cavities,
                'shot_total' => 0,
                'part_total' => 0,
                'ok_part' => 0,
                'ng_part' => 0,
                'operator_name' => $validated['operator_name'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // update status mould
            $this->mould->update(['status' => 'IN_RUN']);
        });

        session()->flash('success', 'Run berhasil dimulai.');
        $this->machine_id = '';
        $this->operator_name = null;
        $this->notes = null;

        $this->refreshActiveRun();
    }

    public function render()
    {
        $machines = Machine::with(['plant','zone'])->orderBy('code')->get();
        return view('livewire.moulds.show', compact('machines'));
    }
}
