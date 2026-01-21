<?php

namespace App\Livewire\Mobile;

use Livewire\Component;

use App\Models\Mould;

class Scanner extends Component
{
    public function handleScan($code)
    {
        // Expected format: MOULD:uuid or just uuid? 
        // Let's support both.
        $id = str_replace('MOULD:', '', $code);

        $mould = Mould::find($id);

        if ($mould) {
            return redirect()->route('mobile.mould-detail', $mould);
        }

        // If not found, dispatch error
        $this->dispatch('scan-error', message: 'Mould not found: ' . $code);
    }

    public function render()
    {
        return view('livewire.mobile.scanner')
            ->layout('layouts.mobile');
    }
}
