<?php

namespace App\Livewire\Mobile;

use App\Models\Mould;
use App\Models\ProductionRun;
use Livewire\Component;

class MouldDetail extends Component
{
    public Mould $mould;
    public $activeRun = null;

    public function mount(Mould $mould)
    {
        $this->mould = $mould;
        $this->activeRun = ProductionRun::where('mould_id', $mould->id)
            ->whereNull('end_ts')
            ->first();
    }

    public function render()
    {
        return view('livewire.mobile.mould-detail')
            ->layout('layouts.mobile');
    }
}
