<?php

namespace App\Livewire\Audit;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 25;

    public function updatedSearch(): void { $this->resetPage(); }

    public function render()
    {
        $logs = Activity::query()
            ->with('causer')
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('description', 'like', "%{$this->search}%")
                        ->orWhere('log_name', 'like', "%{$this->search}%")
                        ->orWhere('subject_type', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.audit.index', compact('logs'));
    }
}
