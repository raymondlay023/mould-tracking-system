<?php

namespace App\Livewire\Qr;

use App\Models\Mould;
use Livewire\Component;
use Livewire\WithPagination;

class MouldQrBatch extends Component
{
    use WithPagination;

    public int $perPage = 24;
    public string $search = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function render()
    {
        $moulds = Mould::query()
            ->when($this->search !== '', function ($q) {
                $q->where('code', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%");
            })
            ->orderBy('code')
            ->paginate($this->perPage);

        return view('livewire.qr.mould-qr-batch', compact('moulds'));
    }
}
