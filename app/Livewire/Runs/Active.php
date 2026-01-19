<?php

declare(strict_types=1);

namespace App\Livewire\Runs;

use App\Models\Machine;
use App\Models\Plant;
use App\Models\ProductionRun;
use App\Models\Zone;
use Livewire\Component;
use Livewire\WithPagination;

class Active extends Component
{
    use WithPagination;
    public string $plant_id = '';

    public string $zone_id = '';

    public string $machine_id = '';

    public string $search = ''; // mould code/name

    public string $lastRefreshedAt = '';

    public int $activeCount = 0;

    // simpan snapshot sebelum poll berikutnya
    public array $prevRunMap = []; // [run_id => mould_code]

    public function mount(): void
    {
        $this->lastRefreshedAt = now()->format('H:i:s');
        $this->snapshotActiveRuns(initial: true);
    }

    public function refreshNow(): void
    {
        $this->lastRefreshedAt = now()->format('H:i:s');
        $this->snapshotActiveRuns();
    }

    private function snapshotActiveRuns(bool $initial = false): void
    {
        // Ambil map run aktif: [run_id => mould_code]
        $currentMap = \App\Models\ProductionRun::query()
            ->active()
            ->with('mould:id,code')
            ->get(['id', 'mould_id'])
            ->mapWithKeys(fn ($r) => [$r->id => ($r->mould?->code ?? $r->id)])
            ->toArray();

        $this->activeCount = count($currentMap);

        if ($initial) {
            $this->prevRunMap = $currentMap;

            return;
        }

        $prevIds = array_keys($this->prevRunMap);
        $currIds = array_keys($currentMap);

        $addedIds = array_values(array_diff($currIds, $prevIds));
        $removedIds = array_values(array_diff($prevIds, $currIds));

        // Toast: Added
        if (count($addedIds) > 0) {
            $codes = array_map(fn ($id) => $currentMap[$id] ?? $id, $addedIds);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Run Started',
                'message' => 'New active run: '.implode(', ', $codes),
                'sound' => true,
            ]);
        }

        // Toast: Removed (Closed)
        if (count($removedIds) > 0) {
            $codes = array_map(fn ($id) => $this->prevRunMap[$id] ?? $id, $removedIds);

            $this->dispatch('toast', [
                'type' => 'warning',
                'title' => 'Run Closed',
                'message' => 'Run ended: '.implode(', ', $codes),
                'sound' => true,
            ]);
        }

        $this->prevRunMap = $currentMap;
    }

    public function render()
    {
        $plants = Plant::orderBy('name')->get();
        $zones = Zone::orderBy('code')->get();
        $machines = Machine::with(['plant', 'zone'])
            ->orderBy('code')
            ->get();

        $runs = ProductionRun::query()
            ->active()
            ->with(['mould', 'machine.plant', 'machine.zone'])
            ->when($this->plant_id !== '', function ($q) {
                $q->whereHas('machine', fn ($mq) => $mq->where('plant_id', $this->plant_id));
            })
            ->when($this->zone_id !== '', function ($q) {
                $q->whereHas('machine', fn ($mq) => $mq->where('zone_id', $this->zone_id));
            })
            ->when($this->machine_id !== '', fn ($q) => $q->where('machine_id', $this->machine_id))
            ->when($this->search !== '', function ($q) {
                $q->whereHas('mould', function ($mq) {
                    $mq->where('code', 'like', "%{$this->search}%")->orWhere('name', 'like', "%{$this->search}%");
                });
            })
            ->paginate(20);

        return view('livewire.runs.active', compact('runs', 'plants', 'zones', 'machines'));
    }
}
