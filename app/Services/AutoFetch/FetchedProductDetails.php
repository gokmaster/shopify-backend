<?php

namespace App\Services\AutoFetch;

class FetchedProductDetails
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $imageUrl = null,
    ) {}
}
