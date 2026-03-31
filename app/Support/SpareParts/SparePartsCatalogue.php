<?php

namespace App\Support\SpareParts;

use App\Support\SpareParts\Providers\CatalogueProvider;
use App\Support\SpareParts\Providers\ConfigCatalogueProvider;
use App\Support\SpareParts\Providers\DbCatalogueProvider;
use App\Support\SpareParts\Providers\ExternalCatalogueProvider;

class SparePartsCatalogue
{
    /** @var list<CatalogueProvider> */
    private array $providers;

    public function __construct()
    {
        $mode = (string) config('spareparts.provider_mode', 'both_parallel');
        $this->providers = match ($mode) {
            'db' => [new DbCatalogueProvider, new ConfigCatalogueProvider],
            'external' => [new ExternalCatalogueProvider, new DbCatalogueProvider, new ConfigCatalogueProvider],
            default => [new DbCatalogueProvider, new ExternalCatalogueProvider, new ConfigCatalogueProvider],
        };
    }

    public function manufacturers(): array
    {
        return $this->firstNonEmpty(fn (CatalogueProvider $provider) => $provider->manufacturers(), []);
    }

    public function models(string $manufacturer): array
    {
        return $this->firstNonEmpty(fn (CatalogueProvider $provider) => $provider->models($manufacturer), []);
    }

    public function years(string $manufacturer, string $model): array
    {
        return $this->firstNonEmpty(fn (CatalogueProvider $provider) => $provider->years($manufacturer, $model), []);
    }

    public function countries(string $manufacturer, string $model, string $year): array
    {
        return $this->firstNonEmpty(fn (CatalogueProvider $provider) => $provider->countries($manufacturer, $model, $year), []);
    }

    public function colours(string $manufacturer, string $model, string $year, string $country): array
    {
        return $this->firstNonEmpty(fn (CatalogueProvider $provider) => $provider->colours($manufacturer, $model, $year, $country), []);
    }

    public function assemblies(string $manufacturer, string $model, string $year, string $country, string $colour): array
    {
        return $this->firstNonEmpty(fn (CatalogueProvider $provider) => $provider->assemblies($manufacturer, $model, $year, $country, $colour), []);
    }

    public function parts(
        string $manufacturer,
        string $model,
        string $year,
        string $country,
        string $colour,
        string $assemblySlug
    ): array {
        return $this->firstNonEmpty(fn (CatalogueProvider $provider) => $provider->parts(
            $manufacturer,
            $model,
            $year,
            $country,
            $colour,
            $assemblySlug
        ), []);
    }

    public function findPart(string $partNumber): ?array
    {
        $needle = $this->normalisePartNumber($partNumber);
        if ($needle === '') {
            return null;
        }

        foreach ($this->providers as $provider) {
            $result = $provider->findPart($needle);
            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    public function normalisePartNumber(string $partNumber): string
    {
        return strtoupper(str_replace(' ', '', trim($partNumber)));
    }

    /**
     * @template T
     *
     * @param  callable(CatalogueProvider):T  $resolver
     * @param  T  $default
     * @return T
     */
    private function firstNonEmpty(callable $resolver, mixed $default): mixed
    {
        foreach ($this->providers as $provider) {
            $result = $resolver($provider);

            if (is_array($result) && $result !== []) {
                return $result;
            }

            if ($result !== null && ! is_array($result)) {
                return $result;
            }
        }

        return $default;
    }
}
