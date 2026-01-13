<?php

namespace App\Livewire\Runs;

use App\Models\ProductionRun;
use App\Models\RunDefect;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Close extends Component
{
    public ProductionRun $run;

    public int $shot_total = 0;
    public int $ok_part = 0;
    public int $ng_part = 0;
    public ?int $cycle_time_avg_sec = null;
    public ?string $notes = null;

    public array $defects = []; // [ ['defect_code'=>'FLASH','qty'=>1], ... ]

    public function mount(ProductionRun $run): void
    {
        $this->run = $run->load(['mould','machine.plant','machine.zone','defects']);

        if ($this->run->end_ts) {
            // already closed; still show but prevent save
        }

        // preload existing (kalau nanti edit)
        $this->shot_total = (int) $this->run->shot_total;
        $this->ok_part = (int) $this->run->ok_part;
        $this->ng_part = (int) $this->run->ng_part;
        $this->cycle_time_avg_sec = $this->run->cycle_time_avg_sec;
        $this->notes = $this->run->notes;

        $this->defects = $this->run->defects->map(fn($d) => [
            'defect_code' => $d->defect_code,
            'qty' => (int) $d->qty,
        ])->values()->all();

        if (empty($this->defects)) {
            $this->defects = [
                ['defect_code' => '', 'qty' => 0],
            ];
        }
    }

    protected function rules(): array
    {
        return [
            'shot_total' => ['required','integer','min:0'],
            'ok_part' => ['required','integer','min:0'],
            'ng_part' => ['required','integer','min:0'],
            'cycle_time_avg_sec' => ['nullable','integer','min:1'],
            'notes' => ['nullable','string','max:2000'],

            'defects' => ['array'],
            'defects.*.defect_code' => ['nullable','string','max:50'],
            'defects.*.qty' => ['nullable','integer','min:0'],
        ];
    }

    public function addDefectRow(): void
    {
        $this->defects[] = ['defect_code' => '', 'qty' => 0];
    }

    public function removeDefectRow(int $i): void
    {
        unset($this->defects[$i]);
        $this->defects = array_values($this->defects);
    }

    public function closeRun(): void
    {
        if ($this->run->end_ts) {
            session()->flash('error', 'Run sudah ditutup.');
            return;
        }

        $v = $this->validate();

        $cav = (int) $this->run->cavities_snapshot;
        $part_total = (int) $v['shot_total'] * $cav;

        // Rule utama: ok+ng harus = part_total
        if (($v['ok_part'] + $v['ng_part']) !== $part_total) {
            $this->addError('ok_part', "ok_part + ng_part harus = part_total ({$part_total}).");
            return;
        }

        // Hitung total defect qty (abaikan row kosong)
        $filtered = collect($this->defects)
            ->filter(fn($d) => trim((string)($d['defect_code'] ?? '')) !== '' || (int)($d['qty'] ?? 0) > 0)
            ->map(function($d){
                return [
                    'defect_code' => strtoupper(trim((string)$d['defect_code'])),
                    'qty' => (int)($d['qty'] ?? 0),
                ];
            })
            ->values();

        $sumDef = (int) $filtered->sum('qty');
        if ($sumDef !== (int)$v['ng_part']) {
            $this->addError('ng_part', "Total qty defect ({$sumDef}) harus sama dengan ng_part ({$v['ng_part']}).");
            return;
        }

        DB::transaction(function () use ($v, $part_total, $filtered) {
            // update run
            $this->run->update([
                'end_ts' => now(),
                'shot_total' => (int)$v['shot_total'],
                'part_total' => $part_total,
                'ok_part' => (int)$v['ok_part'],
                'ng_part' => (int)$v['ng_part'],
                'cycle_time_avg_sec' => $v['cycle_time_avg_sec'],
                'notes' => $v['notes'],
            ]);

            // replace defect rows
            RunDefect::where('run_id', $this->run->id)->delete();
            foreach ($filtered as $d) {
                RunDefect::create([
                    'run_id' => $this->run->id,
                    'defect_code' => $d['defect_code'],
                    'qty' => $d['qty'],
                ]);
            }

            // update mould status back
            $this->run->mould->update(['status' => 'AVAILABLE']);
        });

        session()->flash('success', 'Run berhasil ditutup.');
        // reload for display
        $this->run->refresh()->load(['mould','machine.plant','machine.zone','defects']);
    }

    public function render()
    {
        return view('livewire.runs.close');
    }
}
