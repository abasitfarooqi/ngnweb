<?php

namespace App\Support\SpareParts\Importers;

use App\Models\SpareParts\SpAssembly;
use App\Models\SpareParts\SpAssemblyPart;
use App\Models\SpareParts\SpFitment;
use App\Models\SpareParts\SpMake;
use App\Models\SpareParts\SpModel;
use App\Models\SpareParts\SpPart;
use Illuminate\Support\Facades\DB;

class ConfigCatalogueImporter
{
    public function import(array $catalogue): array
    {
        $stats = [
            'makes' => 0,
            'models' => 0,
            'fitments' => 0,
            'assemblies' => 0,
            'parts' => 0,
            'assembly_parts' => 0,
        ];

        DB::transaction(function () use ($catalogue, &$stats): void {
            foreach ($catalogue as $makeSlug => $makeData) {
                $make = SpMake::query()->updateOrCreate(
                    ['slug' => $makeSlug],
                    [
                        'name' => (string) ($makeData['name'] ?? strtoupper($makeSlug)),
                        'source' => 'config',
                        'is_active' => true,
                    ]
                );
                $stats['makes']++;

                foreach (($makeData['models'] ?? []) as $modelSlug => $modelData) {
                    $model = SpModel::query()->updateOrCreate(
                        ['make_id' => $make->id, 'slug' => $modelSlug],
                        [
                            'name' => (string) ($modelData['name'] ?? strtoupper($modelSlug)),
                            'is_active' => true,
                        ]
                    );
                    $stats['models']++;

                    foreach (($modelData['years'] ?? []) as $year => $yearData) {
                        foreach (($yearData['countries'] ?? []) as $countrySlug => $countryData) {
                            foreach (($countryData['colours'] ?? []) as $colourSlug => $colourData) {
                                $fitment = SpFitment::query()->updateOrCreate(
                                    [
                                        'model_id' => $model->id,
                                        'year' => (string) $year,
                                        'country_slug' => $countrySlug,
                                        'colour_slug' => $colourSlug,
                                    ],
                                    [
                                        'country_name' => (string) ($countryData['name'] ?? strtoupper($countrySlug)),
                                        'colour_name' => (string) ($colourData['name'] ?? strtoupper($colourSlug)),
                                        'is_active' => true,
                                    ]
                                );
                                $stats['fitments']++;

                                foreach (($colourData['assemblies'] ?? []) as $index => $assemblyData) {
                                    $assembly = SpAssembly::query()->updateOrCreate(
                                        [
                                            'fitment_id' => $fitment->id,
                                            'slug' => (string) ($assemblyData['slug'] ?? ''),
                                        ],
                                        [
                                            'external_id' => isset($assemblyData['id']) ? (string) $assemblyData['id'] : null,
                                            'name' => (string) ($assemblyData['name'] ?? ''),
                                            'image_url' => (string) ($assemblyData['image_url'] ?? ''),
                                            'diagram_url' => (string) ($assemblyData['diagram_url'] ?? ''),
                                            'sort_order' => $index,
                                            'is_active' => true,
                                        ]
                                    );
                                    $stats['assemblies']++;

                                    foreach (($assemblyData['parts'] ?? []) as $partIndex => $partData) {
                                        $partNumber = strtoupper(str_replace(' ', '', (string) ($partData['part_number'] ?? '')));
                                        if ($partNumber === '') {
                                            continue;
                                        }

                                        $part = SpPart::query()->updateOrCreate(
                                            ['part_number' => $partNumber],
                                            [
                                                'name' => (string) ($partData['name'] ?? ''),
                                                'note' => (string) ($partData['note'] ?? ''),
                                                'stock_status' => (string) ($partData['stock'] ?? 'NOT IN STOCK'),
                                                'price_gbp_inc_vat' => (float) ($partData['price_gbp_inc_vat'] ?? 0),
                                                'meta' => null,
                                                'last_synced_at' => now(),
                                                'is_active' => true,
                                            ]
                                        );
                                        $stats['parts']++;

                                        SpAssemblyPart::query()->updateOrCreate(
                                            [
                                                'assembly_id' => $assembly->id,
                                                'part_id' => $part->id,
                                            ],
                                            [
                                                'qty_used' => (int) ($partData['qty_used'] ?? 1),
                                                'sort_order' => $partIndex,
                                                'note_override' => null,
                                                'price_override' => null,
                                                'stock_override' => null,
                                            ]
                                        );
                                        $stats['assembly_parts']++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        });

        return $stats;
    }
}
