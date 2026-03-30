<?php

namespace App\Support\SpareParts\Providers;

use App\Models\SpAssembly;
use App\Models\SpFitment;
use App\Models\SpMake;
use App\Models\SpModel;
use App\Models\SpPart;
use Illuminate\Support\Facades\Schema;

class DbCatalogueProvider implements CatalogueProvider
{
    public function manufacturers(): array
    {
        if (! $this->isReady()) {
            return [];
        }

        return SpMake::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['slug', 'name'])
            ->map(fn ($row) => ['slug' => $row->slug, 'name' => $row->name])
            ->values()
            ->all();
    }

    public function models(string $manufacturer): array
    {
        if (! $this->isReady()) {
            return [];
        }

        return SpModel::query()
            ->whereHas('make', fn ($q) => $q->where('slug', $manufacturer)->where('is_active', true))
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['slug', 'name'])
            ->map(fn ($row) => ['slug' => $row->slug, 'name' => $row->name])
            ->values()
            ->all();
    }

    public function years(string $manufacturer, string $model): array
    {
        if (! $this->isReady()) {
            return [];
        }

        $years = SpFitment::query()
            ->whereHas('model', fn ($q) => $q->where('slug', $model)->whereHas('make', fn ($qq) => $qq->where('slug', $manufacturer)))
            ->where('is_active', true)
            ->distinct()
            ->pluck('year')
            ->toArray();
        rsort($years);

        return array_values($years);
    }

    public function countries(string $manufacturer, string $model, string $year): array
    {
        if (! $this->isReady()) {
            return [];
        }

        return SpFitment::query()
            ->whereHas('model', fn ($q) => $q->where('slug', $model)->whereHas('make', fn ($qq) => $qq->where('slug', $manufacturer)))
            ->where('year', $year)
            ->where('is_active', true)
            ->select('country_slug', 'country_name')
            ->distinct()
            ->orderBy('country_name')
            ->get()
            ->map(fn ($row) => ['slug' => $row->country_slug, 'name' => $row->country_name])
            ->values()
            ->all();
    }

    public function colours(string $manufacturer, string $model, string $year, string $country): array
    {
        if (! $this->isReady()) {
            return [];
        }

        return SpFitment::query()
            ->whereHas('model', fn ($q) => $q->where('slug', $model)->whereHas('make', fn ($qq) => $qq->where('slug', $manufacturer)))
            ->where('year', $year)
            ->where('country_slug', $country)
            ->where('is_active', true)
            ->select('colour_slug', 'colour_name')
            ->distinct()
            ->orderBy('colour_name')
            ->get()
            ->map(fn ($row) => ['slug' => $row->colour_slug, 'name' => $row->colour_name])
            ->values()
            ->all();
    }

    public function assemblies(string $manufacturer, string $model, string $year, string $country, string $colour): array
    {
        if (! $this->isReady()) {
            return [];
        }

        return SpAssembly::query()
            ->whereHas('fitment', function ($q) use ($manufacturer, $model, $year, $country, $colour) {
                $q->where('year', $year)
                    ->where('country_slug', $country)
                    ->where('colour_slug', $colour)
                    ->whereHas('model', fn ($qq) => $qq->where('slug', $model)->whereHas('make', fn ($qqq) => $qqq->where('slug', $manufacturer)));
            })
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['external_id', 'slug', 'name', 'image_url'])
            ->map(fn ($row) => [
                'id' => (string) ($row->external_id ?: $row->id),
                'slug' => $row->slug,
                'name' => $row->name,
                'image_url' => (string) ($row->image_url ?: ''),
            ])
            ->values()
            ->all();
    }

    public function parts(string $manufacturer, string $model, string $year, string $country, string $colour, string $assemblySlug): array
    {
        if (! $this->isReady()) {
            return [];
        }

        $assembly = SpAssembly::query()
            ->where('slug', $assemblySlug)
            ->whereHas('fitment', function ($q) use ($manufacturer, $model, $year, $country, $colour) {
                $q->where('year', $year)
                    ->where('country_slug', $country)
                    ->where('colour_slug', $colour)
                    ->whereHas('model', fn ($qq) => $qq->where('slug', $model)->whereHas('make', fn ($qqq) => $qqq->where('slug', $manufacturer)));
            })
            ->first();

        if (! $assembly) {
            return [];
        }

        return $assembly->assemblyParts()
            ->with('part')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($line) use ($assembly) {
                $part = $line->part;

                return [
                    'part_number' => $part->part_number,
                    'sp_part_id' => (int) $part->id,
                    'sp_assembly_id' => (int) $assembly->id,
                    'name' => $part->name,
                    'note' => $line->note_override ?: ($part->note ?? ''),
                    'stock' => $line->stock_override ?: ($part->stock_status ?? 'NOT IN STOCK'),
                    'price_gbp_inc_vat' => (float) ($line->price_override ?? $part->price_gbp_inc_vat),
                    'qty_used' => (int) $line->qty_used,
                ];
            })
            ->values()
            ->all();
    }

    public function findPart(string $partNumber): ?array
    {
        if (! $this->isReady()) {
            return null;
        }

        $needle = $this->normalisePartNumber($partNumber);
        if ($needle === '') {
            return null;
        }

        $part = SpPart::query()->where('part_number', $needle)->first();
        if (! $part) {
            return null;
        }

        $fitments = $part->assemblyParts()
            ->with(['assembly.fitment.model.make'])
            ->get()
            ->map(function ($line) {
                $assembly = $line->assembly;
                $fitment = $assembly?->fitment;
                $model = $fitment?->model;
                $make = $model?->make;

                return [
                    'manufacturer' => (string) ($make->name ?? ''),
                    'manufacturer_slug' => (string) ($make->slug ?? ''),
                    'model' => (string) ($model->name ?? ''),
                    'model_slug' => (string) ($model->slug ?? ''),
                    'year' => (string) ($fitment->year ?? ''),
                    'country' => (string) ($fitment->country_name ?? ''),
                    'country_slug' => (string) ($fitment->country_slug ?? ''),
                    'colour' => (string) ($fitment->colour_name ?? ''),
                    'colour_slug' => (string) ($fitment->colour_slug ?? ''),
                    'assembly' => (string) ($assembly->name ?? ''),
                    'assembly_slug' => (string) ($assembly->slug ?? ''),
                ];
            })
            ->values()
            ->all();

        return [
            'part_number' => $part->part_number,
            'name' => $part->name,
            'note' => (string) ($part->note ?? ''),
            'stock' => (string) $part->stock_status,
            'price_gbp_inc_vat' => (float) $part->price_gbp_inc_vat,
            'qty_used' => 1,
            'fitments' => $fitments,
        ];
    }

    public function normalisePartNumber(string $partNumber): string
    {
        return strtoupper(str_replace(' ', '', trim($partNumber)));
    }

    private function isReady(): bool
    {
        return Schema::hasTable('sp_makes')
            && Schema::hasTable('sp_models')
            && Schema::hasTable('sp_fitments')
            && Schema::hasTable('sp_assemblies')
            && Schema::hasTable('sp_parts')
            && Schema::hasTable('sp_assembly_parts');
    }
}
