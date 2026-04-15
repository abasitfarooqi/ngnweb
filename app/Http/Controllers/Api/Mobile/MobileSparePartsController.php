<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\SpAssemblyPart;
use App\Models\SpPart;
use App\Support\SpareParts\SparePartsCatalogue;
use Illuminate\Http\JsonResponse;

class MobileSparePartsController extends Controller
{
    public function __construct(private readonly SparePartsCatalogue $catalogue) {}

    public function manufacturers(): JsonResponse
    {
        return response()->json([
            'items' => $this->catalogue->manufacturers(),
        ]);
    }

    public function models(string $manufacturer): JsonResponse
    {
        return response()->json([
            'manufacturer' => $manufacturer,
            'items' => $this->catalogue->models($manufacturer),
        ]);
    }

    public function years(string $manufacturer, string $model): JsonResponse
    {
        return response()->json([
            'manufacturer' => $manufacturer,
            'model' => $model,
            'items' => $this->catalogue->years($manufacturer, $model),
        ]);
    }

    public function countries(string $manufacturer, string $model, string $year): JsonResponse
    {
        return response()->json([
            'manufacturer' => $manufacturer,
            'model' => $model,
            'year' => $year,
            'items' => $this->catalogue->countries($manufacturer, $model, $year),
        ]);
    }

    public function colours(string $manufacturer, string $model, string $year, string $country): JsonResponse
    {
        return response()->json([
            'manufacturer' => $manufacturer,
            'model' => $model,
            'year' => $year,
            'country' => $country,
            'items' => $this->catalogue->colours($manufacturer, $model, $year, $country),
        ]);
    }

    public function assemblies(string $manufacturer, string $model, string $year, string $country, string $colour): JsonResponse
    {
        return response()->json([
            'manufacturer' => $manufacturer,
            'model' => $model,
            'year' => $year,
            'country' => $country,
            'colour' => $colour,
            'items' => $this->catalogue->assemblies($manufacturer, $model, $year, $country, $colour),
        ]);
    }

    public function parts(string $manufacturer, string $model, string $year, string $country, string $colour, string $assembly): JsonResponse
    {
        return response()->json([
            'manufacturer' => $manufacturer,
            'model' => $model,
            'year' => $year,
            'country' => $country,
            'colour' => $colour,
            'assembly' => $assembly,
            'items' => $this->catalogue->parts($manufacturer, $model, $year, $country, $colour, $assembly),
        ]);
    }

    public function part(string $partNumber): JsonResponse
    {
        return response()->json([
            'part_number' => $this->catalogue->normalisePartNumber($partNumber),
            'data' => $this->catalogue->findPart($partNumber),
        ]);
    }

    public function partDetail(string $partNumber): JsonResponse
    {
        $normalised = $this->catalogue->normalisePartNumber($partNumber);
        $part = SpPart::query()
            ->where('part_number', $normalised)
            ->firstOrFail();

        $assemblyLinks = SpAssemblyPart::query()
            ->with(['assembly.fitment.model.make', 'part'])
            ->where('part_id', $part->id)
            ->orderBy('sort_order')
            ->limit(200)
            ->get();

        $fitmentTable = $assemblyLinks
            ->map(function (SpAssemblyPart $link) {
                $fitment = $link->assembly?->fitment;
                $model = $fitment?->model;
                $make = $model?->make;

                return [
                    'manufacturer' => $make?->name,
                    'manufacturer_slug' => $make?->slug,
                    'model' => $model?->name,
                    'model_slug' => $model?->slug,
                    'year' => $fitment?->year,
                    'country' => $fitment?->country_name,
                    'country_slug' => $fitment?->country_slug,
                    'colour' => $fitment?->colour_name,
                    'colour_slug' => $fitment?->colour_slug,
                    'assembly' => $link->assembly?->name,
                    'assembly_slug' => $link->assembly?->slug,
                ];
            })
            ->filter(fn (array $row) => ! empty($row['manufacturer']) && ! empty($row['model']))
            ->values();

        $alternativeParts = SpPart::query()
            ->where('id', '!=', $part->id)
            ->where('is_active', true)
            ->where(function ($q) use ($part): void {
                $q->where('name', 'like', '%'.$part->name.'%')
                    ->orWhere('part_number', 'like', substr($part->part_number, 0, 4).'%');
            })
            ->limit(8)
            ->get(['id', 'part_number', 'name', 'price_gbp_inc_vat', 'stock_status']);

        return response()->json([
            'data' => [
                'sp_part_id' => (int) $part->id,
                'part_number' => $part->part_number,
                'name' => $part->name,
                'note' => $part->note,
                'stock_status' => $part->stock_status,
                'price' => (float) ($part->price_gbp_inc_vat ?? 0),
                'global_stock' => (float) ($part->global_stock ?? 0),
                'assemblies' => $assemblyLinks->map(fn (SpAssemblyPart $link) => [
                    'assembly_id' => $link->assembly?->id,
                    'assembly_name' => $link->assembly?->name,
                    'assembly_slug' => $link->assembly?->slug,
                    'qty_used' => (int) ($link->qty_used ?? 1),
                    'diagram_url' => $link->assembly?->diagram_url,
                ])->values(),
                'fitment_table' => $fitmentTable,
                'alternatives' => $alternativeParts->map(fn (SpPart $row) => [
                    'id' => $row->id,
                    'part_number' => $row->part_number,
                    'name' => $row->name,
                    'price' => (float) ($row->price_gbp_inc_vat ?? 0),
                    'stock_status' => $row->stock_status,
                ])->values(),
                'compatibility_notes' => $part->note,
            ],
        ]);
    }
}
