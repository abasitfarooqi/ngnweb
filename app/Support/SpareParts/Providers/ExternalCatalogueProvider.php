<?php

namespace App\Support\SpareParts\Providers;

/**
 * External provider scaffold.
 *
 * This intentionally returns empty results until a real source adapter
 * (API/crawler/feed parser) is wired in.
 */
class ExternalCatalogueProvider implements CatalogueProvider
{
    public function manufacturers(): array
    {
        return [];
    }

    public function models(string $manufacturer): array
    {
        return [];
    }

    public function years(string $manufacturer, string $model): array
    {
        return [];
    }

    public function countries(string $manufacturer, string $model, string $year): array
    {
        return [];
    }

    public function colours(string $manufacturer, string $model, string $year, string $country): array
    {
        return [];
    }

    public function assemblies(string $manufacturer, string $model, string $year, string $country, string $colour): array
    {
        return [];
    }

    public function parts(string $manufacturer, string $model, string $year, string $country, string $colour, string $assemblySlug): array
    {
        return [];
    }

    public function findPart(string $partNumber): ?array
    {
        return null;
    }

    public function normalisePartNumber(string $partNumber): string
    {
        return strtoupper(str_replace(' ', '', trim($partNumber)));
    }
}
