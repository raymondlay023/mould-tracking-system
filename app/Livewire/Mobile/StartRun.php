<?php

namespace App\Livewire\Mobile;

use App\Models\Mould;
use App\Models\Machine;
use App\Models\ProductionRun;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class StartRun extends Component
{
    public Mould $mould;
    public string $startMachineId = '';
    public ?string $startOperatorName = null;
    public ?string $startNotes = null;

    public function mount(Mould $mould)
    {
        abort_if(Gate::denies('operations.access'), 403);
        $this->mould = $mould;
        $this->startOperatorName = auth()->user()->name ?? '';

        $hasActiveRun = ProductionRun::where('mould_id', $this->mould->id)
            ->whereNull('end_ts')
            ->exists();

        if ($hasActiveRun) {
            session()->flash('error', 'This mould already has an active run.');
            return redirect()->route('mobile.mould-detail', ['mould' => $this->mould->id]);
        }
    }

    public function submitStartRun()
    {
        abort_if(Gate::denies('operations.access'), 403);

        $this->validate([
            'startMachineId' => 'required|exists:machines,id',
            'startOperatorName' => 'nullable|string|max:100',
            'startNotes' => 'nullable|string|max:2000',
        ]);

        $machineHasActive = ProductionRun::query()
            ->where('machine_id', $this->startMachineId)
            ->whereNull('end_ts')
            ->exists();

        if ($machineHasActive) {
            $this->addError('startMachineId', 'Machine is already running another mould.');
            return;
        }

        DB::transaction(function () {
            ProductionRun::create([
                'mould_id' => $this->mould->id,
                'machine_id' => $this->startMachineId,
                'start_ts' => now(),
                'end_ts' => null,
                'cavities_snapshot' => (int) $this->mould->cavities,
                'shot_total' => 0,
                'part_total' => 0,
                'ok_part' => 0,
                'ng_part' => 0,
                'operator_name' => $this->startOperatorName,
                'notes' => $this->startNotes,
            ]);

            $this->mould->update(['status' => 'IN_RUN']);
        });

        session()->flash('success', 'Production Run started successfully.');
        return redirect()->route('mobile.mould-detail', ['mould' => $this->mould->id]);
    }

    public function render()
    {
        $machines = Machine::with(['plant','zone'])->orderBy('code')->get();
        return view('livewire.mobile.start-run', compact('machines'))
            ->layout('layouts.mobile');
    }
}
