<?php

namespace App\Livewire\Products;

use App\Services\Import\CsvProductImporter;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app', ['header' => 'Import CSV'])]
class CsvImport extends Component
{
    use WithFileUploads;

    public $file = null;

    /** @var array{created: int, updated: int, skipped: int, errors: array<int, array{row: int, message: string}>}|null */
    public ?array $result = null;

    public function import(CsvProductImporter $importer): void
    {
        $this->validate([
            'file' => 'required|mimes:csv,txt|max:10240',
        ]);

        $result = $importer->import($this->file->getRealPath());

        $this->result = [
            'created' => $result->created,
            'updated' => $result->updated,
            'skipped' => $result->skipped,
            'errors' => $result->errors,
        ];

        $this->file = null;
    }

    public function render(): View
    {
        return view('livewire.products.csv-import');
    }
}
