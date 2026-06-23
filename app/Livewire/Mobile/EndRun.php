<?php

namespace App\Livewire\Mobile;

use App\Models\ProductionRun;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class EndRun extends Component
{
    public ProductionRun $run;

    public int $shot_total = 0;
    public int $ok_part = 0;
    public int $ng_part = 0;
    public ?int $cycle_time_avg_sec = null;
    public ?string $notes = null;

    public array $defects = [];

    public function mount(ProductionRun $run): void
    {
        abort_if(Gate::denies('runs.close'), 403);
        $this->run = $run->load(['mould', 'machine']);

        if ($this->run->end_ts) {
            session()->flash('error', 'This run is already closed.');
            return;
        }

        $this->shot_total = (int) $this->run->shot_total;
        $this->ok_part = (int) $this->run->ok_part;
        $this->ng_part = (int) $this->run->ng_part;
        $this->cycle_time_avg_sec = $this->run->cycle_time_avg_sec;
        $this->notes = $this->run->notes;

        if (empty($this->defects)) {
            $this->defects = [
                ['defect_code' => '', 'qty' => 0],
            ];
        }
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

    protected function rules(): array
    {
        return [
            'shot_total' => ['required', 'integer', 'min:0'],
            'ok_part' => ['required', 'integer', 'min:0'],
            'ng_part' => ['required', 'integer', 'min:0'],
            'cycle_time_avg_sec' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'defects' => ['array'],
            'defects.*.defect_code' => ['nullable', 'string', 'max:50'],
            'defects.*.qty' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function save()
    {
        abort_if(Gate::denies('runs.close'), 403);

        if ($this->run->end_ts) {
            session()->flash('error', 'Run is already closed.');
            return redirect()->route('mobile.mould-detail', ['mould' => $this->run->mould_id]);
        }

        $validated = $this->validate();

        try {
            $data = $validated;
            $data['defects'] = $this->defects;

            $closeRunAction = app(\App\Actions\Production\CloseRunAction::class);
            $closeRunAction->execute($this->run, $data);

            session()->flash('success', 'Production Run ended successfully.');
            return redirect()->route('mobile.mould-detail', ['mould' => $this->run->mould_id]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error closing run: ' . $e->getMessage());
            $this->addError('base', 'System error closing run. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.mobile.end-run')
            ->layout('layouts.mobile');
    }
}
