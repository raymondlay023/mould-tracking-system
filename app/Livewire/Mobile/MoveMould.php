<?php

namespace App\Livewire\Mobile;

use App\Models\Mould;
use App\Models\Machine;
use App\Models\LocationHistory;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class MoveMould extends Component
{
    public Mould $mould;
    public string $moveLocation = 'TOOL_ROOM';
    public ?string $moveMachineId = null;
    public ?string $moveNote = null;

    public function mount(Mould $mould)
    {
        abort_if(Gate::denies('move_locations'), 403);
        $this->mould = $mould;
    }

    public function submitMoveMould()
    {
        abort_if(Gate::denies('move_locations'), 403);

        $v = $this->validate([
            'moveLocation' => 'required|in:TOOL_ROOM,WAREHOUSE,IN_TRANSIT,MACHINE',
            'moveMachineId' => 'nullable|exists:machines,id',
            'moveNote' => 'nullable|string|max:255',
        ]);

        if ($v['moveLocation'] === 'MACHINE' && empty($v['moveMachineId'])) {
            $this->addError('moveMachineId', 'Machine is required if location is MACHINE');
            return;
        }

        $movedBy = auth()->user()?->name;

        DB::transaction(function () use ($v, $movedBy) {
            LocationHistory::query()
                ->where('mould_id', '=', $this->mould->id, 'and')
                ->whereNull('end_ts')
                ->update(['end_ts' => now()]);

            LocationHistory::create([
                'mould_id' => $this->mould->id,
                'plant_id' => null,
                'machine_id' => $v['moveLocation'] === 'MACHINE' ? $v['moveMachineId'] : null,
                'location' => $v['moveLocation'],
                'start_ts' => now(),
                'end_ts' => null,
                'moved_by' => $movedBy,
                'note' => $v['moveNote'],
            ]);
        });

        session()->flash('success', 'Location updated successfully.');
        return redirect()->route('mobile.mould-detail', ['mould' => $this->mould->id]);
    }

    public function render()
    {
        $machines = Machine::with(['plant','zone'])->orderBy('code')->get();
        return view('livewire.mobile.move-mould', compact('machines'))
            ->layout('layouts.mobile');
    }
}
