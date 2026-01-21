<?php

namespace App\Livewire\Runs;

use Illuminate\Support\Facades\Gate;
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

    public function save()
    {
        if ($this->run->end_ts) {
            session()->flash('error', 'Run sudah ditutup.');
            return;
        }

        // Security Check
        abort_if(Gate::denies('close_runs'), 403, 'Unauthorized');

        $validated = $this->validate();

        try {
            // Prepare Data for Action
            $data = $validated;
            $data['defects'] = $this->defects;

            $closeRunAction = app(\App\Actions\Production\CloseRunAction::class);
            $closeRunAction->execute($this->run, $data);

            session()->flash('success', 'Run berhasil ditutup.');
            // reload for display
            $this->run->refresh()->load(['mould','machine.plant','machine.zone','defects']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw to show errors in Livewire
            throw $e;
        } catch (\Exception $e) {
            $this->addError('base', 'Error closing run: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.runs.close');
    }
}
