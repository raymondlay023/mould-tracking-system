<?php

namespace App\Livewire\Moulds;

use App\Models\Mould;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    // form fields
    public ?string $mouldId = null;
    public string $code = '';
    public string $name = '';
    public int $cavities = 1;
    public ?string $customer = null;
    public ?string $resin = null;
    public ?int $min_tonnage_t = null;
    public ?int $max_tonnage_t = null;
    public ?int $pm_interval_shot = null;
    public ?int $pm_interval_days = null;
    public ?string $commissioned_at = null; // YYYY-MM-DD
    public string $status = 'AVAILABLE';

    public array $statusOptions = [
        'AVAILABLE',
        'IN_SETUP',
        'IN_RUN',
        'IN_MAINTENANCE',
        'IN_TRANSIT',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('moulds', 'code')->ignore($this->mouldId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'cavities' => ['required', 'integer', 'min:1'],

            'customer' => ['nullable', 'string', 'max:255'],
            'resin' => ['nullable', 'string', 'max:255'],

            'min_tonnage_t' => ['nullable', 'integer', 'min:0'],
            'max_tonnage_t' => ['nullable', 'integer', 'min:0'],

            'pm_interval_shot' => ['nullable', 'integer', 'min:0'],
            'pm_interval_days' => ['nullable', 'integer', 'min:0'],

            'commissioned_at' => ['nullable', 'date_format:Y-m-d'],

            'status' => ['required', Rule::in($this->statusOptions)],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        // extra rule: min <= max (kalau dua-duanya ada)
        if ($this->min_tonnage_t !== null && $this->max_tonnage_t !== null) {
            if ($this->min_tonnage_t > $this->max_tonnage_t) {
                $this->addError('min_tonnage_t', 'Min tonnage tidak boleh lebih besar dari max tonnage.');
                return;
            }
        }

        // trim code biar rapi
        $validated['code'] = trim($validated['code']);

        Mould::updateOrCreate(
            ['id' => $this->mouldId],
            $validated
        );

        session()->flash('success', $this->mouldId ? 'Mould berhasil diupdate.' : 'Mould berhasil ditambahkan.');

        $this->resetForm();
    }

    public function edit(string $id): void
    {
        $mould = Mould::findOrFail($id);

        $this->mouldId = $mould->id;
        $this->code = $mould->code;
        $this->name = $mould->name;
        $this->cavities = (int) $mould->cavities;
        $this->customer = $mould->customer;
        $this->resin = $mould->resin;
        $this->min_tonnage_t = $mould->min_tonnage_t;
        $this->max_tonnage_t = $mould->max_tonnage_t;
        $this->pm_interval_shot = $mould->pm_interval_shot;
        $this->pm_interval_days = $mould->pm_interval_days;
        $this->commissioned_at = optional($mould->commissioned_at)->format('Y-m-d');
        $this->status = $mould->status ?? 'AVAILABLE';

        $this->resetValidation();
    }

    public function createNew(): void
    {
        $this->resetForm();
        $this->resetValidation();
    }

    public function delete(string $id): void
    {
        Mould::where('id', $id)->delete();
        session()->flash('success', 'Mould berhasil dihapus.');
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->mouldId = null;
        $this->code = '';
        $this->name = '';
        $this->cavities = 1;
        $this->customer = null;
        $this->resin = null;
        $this->min_tonnage_t = null;
        $this->max_tonnage_t = null;
        $this->pm_interval_shot = null;
        $this->pm_interval_days = null;
        $this->commissioned_at = null;
        $this->status = 'AVAILABLE';
    }

    public function render()
    {
        $moulds = Mould::query()
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('code', 'like', "%{$this->search}%")
                       ->orWhere('name', 'like', "%{$this->search}%")
                       ->orWhere('customer', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('code')
            ->paginate($this->perPage);

        return view('livewire.moulds.index', compact('moulds'));
    }
}
