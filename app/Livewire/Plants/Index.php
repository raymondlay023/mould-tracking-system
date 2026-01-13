<?php

namespace App\Livewire\Plants;

use App\Models\Plant;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    public ?string $plantId = null;
    public string $name = '';

    public function updatedSearch(): void { $this->resetPage(); }

    protected function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        Plant::updateOrCreate(
            ['id' => $this->plantId],
            $validated
        );

        session()->flash('success', $this->plantId ? 'Plant diupdate.' : 'Plant ditambahkan.');
        $this->resetForm();
    }

    public function edit(string $id): void
    {
        $p = Plant::findOrFail($id);
        $this->plantId = $p->id;
        $this->name = $p->name;
        $this->resetValidation();
    }

    public function createNew(): void
    {
        $this->resetForm();
        $this->resetValidation();
    }

    public function delete(string $id): void
    {
        Plant::where('id', $id)->delete();
        session()->flash('success', 'Plant dihapus.');
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->plantId = null;
        $this->name = '';
    }

    public function render()
    {
        $plants = Plant::query()
            ->when($this->search !== '', fn($q) => $q->where('name','like',"%{$this->search}%"))
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.plants.index', compact('plants'));
    }
}
