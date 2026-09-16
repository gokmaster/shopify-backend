<?php

namespace App\Services\AutoFetch;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ProductAutoFetchService
{
    /**
     * Fetch a product page and extract a best-effort title, description and image
     * from Open Graph / standard meta tags. Used to speed up manual product entry.
     *
     * @throws ProductAutoFetchException
     */
    public function fetch(string $url): FetchedProductDetails
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (compatible; RSMarketBot/1.0; +product-import)',
        ])->timeout(15)->get($url);

        if (! $response->successful()) {
            throw new ProductAutoFetchException("Could not fetch that URL (HTTP {$response->status()}).");
        }

        $crawler = new Crawler($response->body());

        $title = $this->meta($crawler, 'og:title') ?? $this->text($crawler, 'title');
        $description = $this->meta($crawler, 'og:description') ?? $this->meta($crawler, 'description', 'name');
        $image = $this->meta($crawler, 'og:image') ?? $this->firstImageSrc($crawler);

        return new FetchedProductDetails(
            title: $this->clean($title),
            description: $this->clean($description),
            imageUrl: $image ? $this->toAbsoluteUrl($url, $image) : null,
        );
    }

    protected function meta(Crawler $crawler, string $value, string $attribute = 'property'): ?string
    {
        $node = $crawler->filter("meta[{$attribute}=\"{$value}\"]");

        if ($node->count() === 0) {
            return null;
        }

        return $node->first()->attr('content');
    }

    protected function text(Crawler $crawler, string $selector): ?string
    {
        $node = $crawler->filter($selector);

        return $node->count() > 0 ? $node->first()->text() : null;
    }

    protected function firstImageSrc(Crawler $crawler): ?string
    {
        $node = $crawler->filter('img');

        return $node->count() > 0 ? $node->first()->attr('src') : null;
    }

    protected function clean(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function toAbsoluteUrl(string $pageUrl, string $imageUrl): string
    {
        if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
            return $imageUrl;
        }

        $parts = parse_url($pageUrl);
        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? '';

        if (str_starts_with($imageUrl, '//')) {
            return "{$scheme}:{$imageUrl}";
        }

        if (str_starts_with($imageUrl, '/')) {
            return "{$scheme}://{$host}{$imageUrl}";
        }

        return "{$scheme}://{$host}/".ltrim($imageUrl, '/');
    }
}
