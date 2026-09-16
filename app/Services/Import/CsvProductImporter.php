<?php

namespace App\Services\Import;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class CsvProductImporter
{
    /**
     * Column names accepted from the CSV header, mapped to our internal field.
     * The header is matched case-insensitively with spaces/dashes normalized to underscores.
     *
     * @var array<string, string>
     */
    protected const COLUMN_ALIASES = [
        'title' => 'title',
        'name' => 'title',
        'product_title' => 'title',
        'description' => 'description',
        'body' => 'description',
        'category' => 'category',
        'price' => 'price',
        'compare_at_price' => 'compare_at_price',
        'compare_price' => 'compare_at_price',
        'sku' => 'sku',
        'barcode' => 'barcode',
        'upc' => 'barcode',
        'quantity' => 'quantity',
        'stock' => 'quantity',
        'vendor' => 'vendor',
        'brand' => 'vendor',
        'status' => 'status',
        'image_url' => 'image_url',
        'image' => 'image_url',
        'source_url' => 'source_url',
    ];

    public function import(string $absolutePath): CsvImportResult
    {
        $result = new CsvImportResult;

        $handle = fopen($absolutePath, 'r');

        if ($handle === false) {
            $result->addError(0, 'Could not open the uploaded file.');

            return $result;
        }

        $header = fgetcsv($handle);

        if ($header === false) {
            $result->addError(0, 'The CSV file appears to be empty.');
            fclose($handle);

            return $result;
        }

        $columns = $this->normalizeHeader($header);

        if (! in_array('title', $columns, true) || ! in_array('price', $columns, true)) {
            $result->addError(0, 'The CSV must include at least a "title" and "price" column.');
            fclose($handle);

            return $result;
        }

        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if ($this->isBlankRow($row)) {
                continue;
            }

            $data = $this->combineRow($columns, $row);

            $this->importRow($data, $rowNumber, $result);
        }

        fclose($handle);

        return $result;
    }

    /**
     * @param  array<int, string|null>  $header
     * @return array<int, string>
     */
    protected function normalizeHeader(array $header): array
    {
        return array_map(function (?string $column): string {
            $key = Str::of($column ?? '')->trim()->lower()->replace([' ', '-'], '_')->value();

            return self::COLUMN_ALIASES[$key] ?? $key;
        }, $header);
    }

    protected function isBlankRow(array $row): bool
    {
        return count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0;
    }

    /**
     * @param  array<int, string>  $columns
     * @param  array<int, string|null>  $row
     * @return array<string, string|null>
     */
    protected function combineRow(array $columns, array $row): array
    {
        $data = [];

        foreach ($columns as $index => $column) {
            $data[$column] = isset($row[$index]) ? trim((string) $row[$index]) : null;
        }

        return $data;
    }

    /**
     * @param  array<string, string|null>  $data
     */
    protected function importRow(array $data, int $rowNumber, CsvImportResult $result): void
    {
        $title = $data['title'] ?? null;
        $price = $data['price'] ?? null;

        if (blank($title)) {
            $result->addError($rowNumber, 'Missing product title.');

            return;
        }

        if (blank($price) || ! is_numeric($price)) {
            $result->addError($rowNumber, 'Missing or invalid price.');

            return;
        }

        $category = null;

        if (! blank($data['category'] ?? null)) {
            $category = Category::query()->firstOrCreate(
                ['slug' => Str::slug($data['category'])],
                ['name' => $data['category']],
            );
        }

        $sku = $data['sku'] ?? null;

        $product = null;

        if (! blank($sku)) {
            $product = Product::query()->where('sku', $sku)->first();
        }

        $product ??= Product::query()->where('title', $title)->first();

        $attributes = [
            'title' => $title,
            'description' => $data['description'] ?? null,
            'category_id' => $category?->id,
            'price' => (float) $price,
            'compare_at_price' => is_numeric($data['compare_at_price'] ?? null) ? (float) $data['compare_at_price'] : null,
            'sku' => $sku,
            'barcode' => $data['barcode'] ?? null,
            'quantity' => is_numeric($data['quantity'] ?? null) ? (int) $data['quantity'] : 0,
            'vendor' => $data['vendor'] ?? null,
            'status' => in_array($data['status'] ?? null, [Product::STATUS_ACTIVE, Product::STATUS_DRAFT, Product::STATUS_ARCHIVED], true)
                ? $data['status']
                : Product::STATUS_DRAFT,
            'source_url' => $data['source_url'] ?? null,
        ];

        $isNew = $product === null;

        if ($isNew) {
            $attributes['slug'] = Product::uniqueSlug($title);
            $product = Product::create($attributes);
        } else {
            $product->update($attributes);
        }

        if (! blank($data['image_url'] ?? null) && ! $product->images()->where('url', $data['image_url'])->exists()) {
            ProductImage::create([
                'product_id' => $product->id,
                'url' => $data['image_url'],
                'position' => $product->images()->count(),
            ]);
        }

        $isNew ? $result->created++ : $result->updated++;
    }
}
