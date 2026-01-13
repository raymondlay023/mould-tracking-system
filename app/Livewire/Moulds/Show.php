<?php

namespace App\Livewire\Moulds;

use App\Models\Mould;
use Livewire\Component;

class Show extends Component
{
    public Mould $mould;

    public function mount(Mould $mould): void
    {
        $this->mould = $mould;
    }

    public function render()
    {
        return view('livewire.moulds.show');
    }
}
