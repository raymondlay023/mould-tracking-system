<?php

namespace App\Livewire\Machines;

use App\Models\Machine;
use App\Models\Plant;
use App\Models\Zone;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    public ?string $machineId = null;
    public string $plant_id = '';
    public ?string $zone_id = null;
    public string $code = '';
    public string $name = '';
    public ?int $tonnage_t = null;
    public bool $plc_connected = false;

    public function updatedSearch(): void { $this->resetPage(); }

    protected function rules(): array
    {
        return [
            'plant_id' => ['required','exists:plants,id'],
            'zone_id' => ['nullable','exists:zones,id'],
            'code' => ['required','string','max:50', Rule::unique('machines','code')->ignore($this->machineId)],
            'name' => ['required','string','max:255'],
            'tonnage_t' => ['nullable','integer','min:0'],
            'plc_connected' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();
        $validated['code'] = strtoupper(trim($validated['code']));

        Machine::updateOrCreate(['id' => $this->machineId], $validated);

        session()->flash('success', $this->machineId ? 'Machine diupdate.' : 'Machine ditambahkan.');
        $this->resetForm();
    }

    public function edit(string $id): void
    {
        $m = Machine::findOrFail($id);
        $this->machineId = $m->id;
        $this->plant_id = $m->plant_id;
        $this->zone_id = $m->zone_id;
        $this->code = $m->code;
        $this->name = $m->name;
        $this->tonnage_t = $m->tonnage_t;
        $this->plc_connected = (bool)$m->plc_connected;
        $this->resetValidation();
    }

    public function createNew(): void
    {
        $this->resetForm();
        $this->resetValidation();
    }

    public function delete(string $id): void
    {
        Machine::where('id', $id)->delete();
        session()->flash('success', 'Machine dihapus.');
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->machineId = null;
        $this->plant_id = '';
        $this->zone_id = null;
        $this->code = '';
        $this->name = '';
        $this->tonnage_t = null;
        $this->plc_connected = false;
    }

    public function render()
    {
        $plants = Plant::orderBy('name')->get();
        $zones = Zone::orderBy('code')->get();

        $machines = Machine::query()
            ->with(['plant','zone'])
            ->when($this->search !== '', function($q){
                $q->where('code','like',"%{$this->search}%")
                  ->orWhere('name','like',"%{$this->search}%");
            })
            ->orderBy('code')
            ->paginate($this->perPage);

        return view('livewire.machines.index', compact('machines','plants','zones'));
    }
}
