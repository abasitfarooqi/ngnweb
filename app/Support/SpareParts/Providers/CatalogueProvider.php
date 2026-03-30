<?php

namespace App\Support\SpareParts\Providers;

interface CatalogueProvider
{
    public function manufacturers(): array;

    public function models(string $manufacturer): array;

    public function years(string $manufacturer, string $model): array;

    public function countries(string $manufacturer, string $model, string $year): array;

    public function colours(string $manufacturer, string $model, string $year, string $country): array;

    public function assemblies(string $manufacturer, string $model, string $year, string $country, string $colour): array;

    public function parts(
        string $manufacturer,
        string $model,
        string $year,
        string $country,
        string $colour,
        string $assemblySlug
    ): array;

    public function findPart(string $partNumber): ?array;

    public function normalisePartNumber(string $partNumber): string;
}
