<?php

namespace App\Livewire\Zones;

use App\Models\Zone;
use App\Models\Plant;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    public ?string $zoneId = null;
    public string $plant_id = '';
    public string $code = '';
    public string $name = '';

    public function updatedSearch(): void { $this->resetPage(); }

    protected function rules(): array
    {
        return [
            'plant_id' => ['required','exists:plants,id'],
            'code' => [
                'required','string','max:50',
                Rule::unique('zones','code')->where(fn($q) => $q->where('plant_id', $this->plant_id))
                    ->ignore($this->zoneId)
            ],
            'name' => ['required','string','max:255'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();
        $validated['code'] = strtoupper(trim($validated['code']));

        Zone::updateOrCreate(['id' => $this->zoneId], $validated);

        session()->flash('success', $this->zoneId ? 'Zone diupdate.' : 'Zone ditambahkan.');
        $this->resetForm();
    }

    public function edit(string $id): void
    {
        $z = Zone::findOrFail($id);
        $this->zoneId = $z->id;
        $this->plant_id = $z->plant_id;
        $this->code = $z->code;
        $this->name = $z->name;
        $this->resetValidation();
    }

    public function createNew(): void
    {
        $this->resetForm();
        $this->resetValidation();
    }

    public function delete(string $id): void
    {
        Zone::where('id', $id)->delete();
        session()->flash('success', 'Zone dihapus.');
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->zoneId = null;
        $this->plant_id = '';
        $this->code = '';
        $this->name = '';
    }

    public function render()
    {
        $plants = Plant::orderBy('name')->get();

        $zones = Zone::query()
            ->with('plant')
            ->when($this->search !== '', function ($q) {
                $q->where('code','like',"%{$this->search}%")
                  ->orWhere('name','like',"%{$this->search}%");
            })
            ->orderBy('code')
            ->paginate($this->perPage);

        return view('livewire.zones.index', compact('zones','plants'));
    }
}
