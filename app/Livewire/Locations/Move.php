<?php

namespace App\Livewire\Locations;

use Illuminate\Support\Facades\Gate;
use App\Models\LocationHistory;
use App\Models\Mould;
use App\Models\Plant;
use App\Models\Machine;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Move extends Component
{
    public string $mould_id = '';
    public ?string $plant_id = null;
    public ?string $machine_id = null;
    public string $location = 'TOOL_ROOM';
    public ?string $note = null;

    protected function rules(): array
    {
        return [
            'mould_id' => ['required','exists:moulds,id'],
            'plant_id' => ['nullable','exists:plants,id'],
            'machine_id' => ['nullable','exists:machines,id'],
            'location' => ['required','in:TOOL_ROOM,WAREHOUSE,IN_TRANSIT,MACHINE'],
            'note' => ['nullable','string','max:255'],
        ];
    }

    public function save()
    {
        abort_if(Gate::denies('move_locations'), 403, 'Unauthorized');

        $v = $this->validate();

        if ($v['location'] === 'MACHINE' && empty($v['machine_id'])) {
            $this->addError('machine_id', 'machine_id wajib jika location = MACHINE');
            return;
        }

        $movedBy = auth()->user()?->name;

        DB::transaction(function () use ($v, $movedBy) {
            // close current location
            LocationHistory::query()
                ->where('mould_id', '=', $v['mould_id'], 'and')
                ->whereNull('end_ts')
                ->update(['end_ts' => now()]);

            // create new location
            LocationHistory::create([
                'mould_id' => $v['mould_id'],
                'plant_id' => $v['plant_id'] ?: null,
                'machine_id' => $v['location'] === 'MACHINE' ? $v['machine_id'] : null,
                'location' => $v['location'],
                'start_ts' => now(),
                'end_ts' => null,
                'moved_by' => $movedBy,
                'note' => $v['note'],
            ]);
        });

        session()->flash('success', 'Location moved successfully.');
        $this->reset(['mould_id','plant_id','machine_id','location','note']);
        $this->location = 'TOOL_ROOM';
    }

    public function render()
    {
        $moulds = Mould::orderBy('code')->get();
        $plants = Plant::orderBy('name')->get();
        $machines = Machine::with('plant','zone')->orderBy('code')->get();

        return view('livewire.locations.move', compact('moulds','plants','machines'));
    }
}
