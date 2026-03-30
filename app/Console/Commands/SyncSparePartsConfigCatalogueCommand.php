<?php

namespace App\Console\Commands;

use App\Models\SpAssembly;
use App\Models\SpAssemblyPart;
use App\Models\SpFitment;
use App\Models\SpMake;
use App\Models\SpModel;
use App\Models\SpPart;
use App\Support\SpareParts\Providers\ConfigCatalogueProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncSparePartsConfigCatalogueCommand extends Command
{
    protected $signature = 'spareparts:sync-config-catalogue {--fresh : Clear existing spare parts catalogue tables before sync}';

    protected $description = 'Sync the spare parts config catalogue into database tables.';

    public function handle(): int
    {
        if (! $this->catalogueTablesExist()) {
            $this->error('Spare parts catalogue tables do not exist yet. Run migrations first.');

            return self::FAILURE;
        }

        $catalogue = config('spareparts.catalogue', []);
        if ($catalogue === []) {
            $this->warn('No spare parts catalogue data found in config/spareparts.php.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($catalogue): void {
            if ($this->option('fresh')) {
                SpAssemblyPart::query()->delete();
                SpPart::query()->delete();
                SpAssembly::query()->delete();
                SpFitment::query()->delete();
                SpModel::query()->delete();
                SpMake::query()->delete();
            }

            foreach ($catalogue as $makeSlug => $makeData) {
                $make = SpMake::query()->updateOrCreate(
                    ['slug' => $makeSlug],
                    [
                        'name' => (string) ($makeData['name'] ?? strtoupper($makeSlug)),
                        'source' => 'config',
                        'is_active' => true,
                    ]
                );

                foreach (($makeData['models'] ?? []) as $modelSlug => $modelData) {
                    $model = SpModel::query()->updateOrCreate(
                        [
                            'make_id' => $make->id,
                            'slug' => $modelSlug,
                        ],
                        [
                            'name' => (string) ($modelData['name'] ?? strtoupper($modelSlug)),
                            'is_active' => true,
                        ]
                    );

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

                                foreach (($colourData['assemblies'] ?? []) as $assemblyIndex => $assemblyData) {
                                    $assembly = SpAssembly::query()->updateOrCreate(
                                        [
                                            'fitment_id' => $fitment->id,
                                            'slug' => (string) ($assemblyData['slug'] ?? ''),
                                        ],
                                        [
                                            'external_id' => (string) ($assemblyData['id'] ?? ''),
                                            'name' => (string) ($assemblyData['name'] ?? ''),
                                            'image_url' => (string) ($assemblyData['image_url'] ?? ''),
                                            'diagram_url' => (string) ($assemblyData['diagram_url'] ?? ''),
                                            'sort_order' => $assemblyIndex,
                                            'is_active' => true,
                                        ]
                                    );

                                    foreach (($assemblyData['parts'] ?? []) as $partIndex => $partData) {
                                        $partNumber = (new ConfigCatalogueProvider)->normalisePartNumber((string) ($partData['part_number'] ?? ''));
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

                                        SpAssemblyPart::query()->updateOrCreate(
                                            [
                                                'assembly_id' => $assembly->id,
                                                'part_id' => $part->id,
                                            ],
                                            [
                                                'qty_used' => (int) ($partData['qty_used'] ?? 1),
                                                'sort_order' => $partIndex,
                                                'note_override' => (string) ($partData['note_override'] ?? ''),
                                                'price_override' => $partData['price_override'] ?? null,
                                                'stock_override' => $partData['stock_override'] ?? null,
                                            ]
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        });

        $this->info('Spare parts config catalogue synced into database.');

        return self::SUCCESS;
    }

    private function catalogueTablesExist(): bool
    {
        return Schema::hasTable('sp_makes')
            && Schema::hasTable('sp_models')
            && Schema::hasTable('sp_fitments')
            && Schema::hasTable('sp_assemblies')
            && Schema::hasTable('sp_parts')
            && Schema::hasTable('sp_assembly_parts');
    }
}
