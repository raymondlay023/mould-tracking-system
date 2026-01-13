<?php

namespace App\Livewire\Imports;

use App\Imports\MouldsImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class MouldImport extends Component
{
    use WithFileUploads;

    public $file;

    public bool $upsert = true;

    public ?array $result = null; // inserted, updated, errors

    protected function rules(): array
    {
        return [
            'file' => 'required|file|max:5120|mimes:xlsx,xls,csv',
        ];
    }

    public function import(): void
    {
        $this->validate();

        $importer = new MouldsImport(upsert: $this->upsert);

        Excel::import($importer, $this->file->getRealPath());

        $this->result = [
            'inserted' => $importer->inserted,
            'updated' => $importer->updated,
            'failed' => count($importer->errors),
            'errors' => $importer->errors,
        ];

        session()->flash('success', 'Import selesai.');
    }

    public function render()
    {
        return view('livewire.imports.mould-import');
    }
}
