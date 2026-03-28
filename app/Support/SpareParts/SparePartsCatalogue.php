<?php

namespace App\Support\SpareParts;

class SparePartsCatalogue
{
    public function manufacturers(): array
    {
        $catalogue = $this->catalogue();
        $result = [];

        foreach ($catalogue as $slug => $data) {
            $result[] = [
                'slug' => $slug,
                'name' => (string) ($data['name'] ?? strtoupper($slug)),
            ];
        }

        return $result;
    }

    public function models(string $manufacturer): array
    {
        $models = $this->catalogue()[$manufacturer]['models'] ?? [];
        $result = [];

        foreach ($models as $slug => $data) {
            $result[] = [
                'slug' => $slug,
                'name' => (string) ($data['name'] ?? strtoupper($slug)),
            ];
        }

        return $result;
    }

    public function years(string $manufacturer, string $model): array
    {
        $years = array_keys($this->catalogue()[$manufacturer]['models'][$model]['years'] ?? []);
        rsort($years);

        return array_values($years);
    }

    public function countries(string $manufacturer, string $model, string $year): array
    {
        $countries = $this->catalogue()[$manufacturer]['models'][$model]['years'][$year]['countries'] ?? [];
        $result = [];

        foreach ($countries as $slug => $data) {
            $result[] = [
                'slug' => $slug,
                'name' => (string) ($data['name'] ?? strtoupper($slug)),
            ];
        }

        return $result;
    }

    public function colours(string $manufacturer, string $model, string $year, string $country): array
    {
        $colours = $this->catalogue()[$manufacturer]['models'][$model]['years'][$year]['countries'][$country]['colours'] ?? [];
        $result = [];

        foreach ($colours as $slug => $data) {
            $result[] = [
                'slug' => $slug,
                'name' => (string) ($data['name'] ?? strtoupper($slug)),
            ];
        }

        return $result;
    }

    public function assemblies(string $manufacturer, string $model, string $year, string $country, string $colour): array
    {
        $assemblies = $this->catalogue()[$manufacturer]['models'][$model]['years'][$year]['countries'][$country]['colours'][$colour]['assemblies'] ?? [];
        $result = [];

        foreach ($assemblies as $assembly) {
            $result[] = [
                'id' => (string) ($assembly['id'] ?? ''),
                'slug' => (string) ($assembly['slug'] ?? ''),
                'name' => (string) ($assembly['name'] ?? ''),
            ];
        }

        return $result;
    }

    public function parts(
        string $manufacturer,
        string $model,
        string $year,
        string $country,
        string $colour,
        string $assemblySlug
    ): array {
        $assemblies = $this->catalogue()[$manufacturer]['models'][$model]['years'][$year]['countries'][$country]['colours'][$colour]['assemblies'] ?? [];

        foreach ($assemblies as $assembly) {
            if (($assembly['slug'] ?? null) !== $assemblySlug) {
                continue;
            }

            $parts = [];
            foreach (($assembly['parts'] ?? []) as $part) {
                $parts[] = [
                    'part_number' => $this->normalisePartNumber((string) ($part['part_number'] ?? '')),
                    'name' => (string) ($part['name'] ?? ''),
                    'note' => (string) ($part['note'] ?? ''),
                    'stock' => (string) ($part['stock'] ?? 'NOT IN STOCK'),
                    'price_gbp_inc_vat' => (float) ($part['price_gbp_inc_vat'] ?? 0),
                    'qty_used' => (int) ($part['qty_used'] ?? 1),
                ];
            }

            return $parts;
        }

        return [];
    }

    public function findPart(string $partNumber): ?array
    {
        $needle = $this->normalisePartNumber($partNumber);
        if ($needle === '') {
            return null;
        }

        $fitments = [];
        $partPayload = null;

        foreach ($this->catalogue() as $manufacturerSlug => $manufacturer) {
            foreach (($manufacturer['models'] ?? []) as $modelSlug => $model) {
                foreach (($model['years'] ?? []) as $year => $yearData) {
                    foreach (($yearData['countries'] ?? []) as $countrySlug => $countryData) {
                        foreach (($countryData['colours'] ?? []) as $colourSlug => $colourData) {
                            foreach (($colourData['assemblies'] ?? []) as $assembly) {
                                foreach (($assembly['parts'] ?? []) as $part) {
                                    $currentNumber = $this->normalisePartNumber((string) ($part['part_number'] ?? ''));
                                    if ($currentNumber !== $needle) {
                                        continue;
                                    }

                                    if ($partPayload === null) {
                                        $partPayload = [
                                            'part_number' => $currentNumber,
                                            'name' => (string) ($part['name'] ?? ''),
                                            'note' => (string) ($part['note'] ?? ''),
                                            'stock' => (string) ($part['stock'] ?? 'NOT IN STOCK'),
                                            'price_gbp_inc_vat' => (float) ($part['price_gbp_inc_vat'] ?? 0),
                                            'qty_used' => (int) ($part['qty_used'] ?? 1),
                                        ];
                                    }

                                    $fitments[] = [
                                        'manufacturer' => (string) ($manufacturer['name'] ?? strtoupper($manufacturerSlug)),
                                        'manufacturer_slug' => $manufacturerSlug,
                                        'model' => (string) ($model['name'] ?? strtoupper($modelSlug)),
                                        'model_slug' => $modelSlug,
                                        'year' => (string) $year,
                                        'country' => (string) ($countryData['name'] ?? strtoupper($countrySlug)),
                                        'country_slug' => $countrySlug,
                                        'colour' => (string) ($colourData['name'] ?? strtoupper($colourSlug)),
                                        'colour_slug' => $colourSlug,
                                        'assembly' => (string) ($assembly['name'] ?? ''),
                                        'assembly_slug' => (string) ($assembly['slug'] ?? ''),
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($partPayload === null) {
            return null;
        }

        $partPayload['fitments'] = $fitments;

        return $partPayload;
    }

    public function normalisePartNumber(string $partNumber): string
    {
        return strtoupper(str_replace(' ', '', trim($partNumber)));
    }

    private function catalogue(): array
    {
        return config('spareparts.catalogue', []);
    }
}
