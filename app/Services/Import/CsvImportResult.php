<?php

namespace App\Services\Import;

class CsvImportResult
{
    public int $created = 0;

    public int $updated = 0;

    public int $skipped = 0;

    /** @var array<int, array{row: int, message: string}> */
    public array $errors = [];

    public function addError(int $row, string $message): void
    {
        $this->skipped++;
        $this->errors[] = ['row' => $row, 'message' => $message];
    }

    public function totalProcessed(): int
    {
        return $this->created + $this->updated + $this->skipped;
    }
}
