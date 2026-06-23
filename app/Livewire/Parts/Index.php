<?php

declare(strict_types=1);

namespace App\Livewire\Parts;

use App\Models\Mould;
use App\Models\Part;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 15;
    public string $mouldFilter = '';

    // Form fields
    public ?string $partId = null;
    public string $mould_id = '';
    public string $part_number = '';
    public string $part_name = '';
    public ?int $cavity_number = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedMouldFilter(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'mould_id'     => ['required', 'exists:moulds,id'],
            'part_number'  => [
                'required',
                'string',
                'max:100',
                Rule::unique('parts', 'part_number')->ignore($this->partId),
            ],
            'part_name'    => ['required', 'string', 'max:255'],
            'cavity_number' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'mould_id'      => 'mould',
            'part_number'   => 'part number',
            'part_name'     => 'part name',
            'cavity_number' => 'cavity number',
        ];
    }

    public function save(): void
    {
        abort_if(!auth()->user()->can('admin_panel.view'), 403);

        $validated = $this->validate();
        $validated['part_number'] = strtoupper(trim($validated['part_number']));

        Part::updateOrCreate(['id' => $this->partId], $validated);

        session()->flash('success', $this->partId ? 'Part updated successfully.' : 'Part added successfully.');
        $this->resetForm();
    }

    public function edit(string $id): void
    {
        abort_if(!auth()->user()->can('admin_panel.view'), 403);

        $part = Part::findOrFail($id);

        $this->partId       = $part->id;
        $this->mould_id     = $part->mould_id;
        $this->part_number  = $part->part_number;
        $this->part_name    = $part->part_name;
        $this->cavity_number = $part->cavity_number;

        $this->resetValidation();
    }

    public function createNew(): void
    {
        abort_if(!auth()->user()->can('admin_panel.view'), 403);

        $this->resetForm();
        $this->resetValidation();
    }

    public function delete(string $id): void
    {
        abort_if(!auth()->user()->can('admin_panel.view'), 403);

        Part::where('id', $id)->delete();
        session()->flash('success', 'Part deleted successfully.');
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->partId       = null;
        $this->mould_id     = '';
        $this->part_number  = '';
        $this->part_name    = '';
        $this->cavity_number = null;
    }

    public function render()
    {
        $moulds = Mould::orderBy('code')->get(['id', 'code', 'name']);

        $parts = Part::query()
            ->with('mould')
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('part_number', 'like', "%{$this->search}%")
                       ->orWhere('part_name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->mouldFilter !== '', function ($q) {
                $q->where('mould_id', $this->mouldFilter);
            })
            ->orderBy('part_number')
            ->paginate($this->perPage);

        return view('livewire.parts.index', compact('parts', 'moulds'));
    }
}
