<?php

namespace App\Console\Commands;

use App\Models\SpAssembly;
use App\Models\SpAssemblyPart;
use App\Models\SpFitment;
use App\Models\SpMake;
use App\Models\SpModel;
use App\Models\SpPart;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SeedSparePartsDummyDataCommand extends Command
{
    protected $signature = 'spareparts:seed-dummy {--fresh : Delete existing spareparts catalogue rows before seeding}';

    protected $description = 'Seed realistic dummy spareparts catalogue data for development and UI testing.';

    public function handle(): int
    {
        DB::transaction(function (): void {
            if ($this->option('fresh')) {
                SpAssemblyPart::query()->delete();
                SpPart::query()->delete();
                SpAssembly::query()->delete();
                SpFitment::query()->delete();
                SpModel::query()->delete();
                SpMake::query()->delete();
            }

            $catalogue = $this->catalogueBlueprint();
            foreach ($catalogue as $makeData) {
                $make = SpMake::query()->updateOrCreate(
                    ['slug' => $makeData['slug']],
                    ['name' => $makeData['name'], 'source' => 'dummy', 'is_active' => true]
                );

                foreach ($makeData['models'] as $modelData) {
                    $model = SpModel::query()->updateOrCreate(
                        ['make_id' => $make->id, 'slug' => $modelData['slug']],
                        ['name' => $modelData['name'], 'is_active' => true]
                    );

                    foreach ($modelData['years'] as $year) {
                        foreach ($this->countries() as $country) {
                            foreach ($this->colours() as $colour) {
                                $fitment = SpFitment::query()->updateOrCreate(
                                    [
                                        'model_id' => $model->id,
                                        'year' => (string) $year,
                                        'country_slug' => $country['slug'],
                                        'colour_slug' => $colour['slug'],
                                    ],
                                    [
                                        'country_name' => $country['name'],
                                        'colour_name' => $colour['name'],
                                        'is_active' => true,
                                    ]
                                );

                                $this->seedAssembliesForFitment($fitment, $make->slug, $model->slug, (string) $year);
                            }
                        }
                    }
                }
            }
        });

        $this->info('Dummy spareparts catalogue ready.');
        $this->line('Tip: run `php artisan spareparts:seed-dummy --fresh` to rebuild quickly.');

        return self::SUCCESS;
    }

    private function seedAssembliesForFitment(SpFitment $fitment, string $makeSlug, string $modelSlug, string $year): void
    {
        $assemblyNames = [
            'AIR CLEANER',
            'FRONT BRAKE CALIPER',
            'CYLINDER HEAD COVER',
            'HANDLE GRIP/SWITCH/CABLE',
            'FRAME BODY',
        ];

        foreach ($assemblyNames as $assemblyIndex => $assemblyName) {
            $assemblySlug = Str::slug($assemblyName);
            $assembly = SpAssembly::query()->updateOrCreate(
                [
                    'fitment_id' => $fitment->id,
                    'slug' => $assemblySlug,
                ],
                [
                    'external_id' => 'DUMMY-'.$fitment->id.'-'.($assemblyIndex + 1),
                    'name' => $assemblyName,
                    'image_url' => null,
                    'diagram_url' => null,
                    'sort_order' => $assemblyIndex,
                    'is_active' => true,
                ]
            );

            $parts = $this->assemblyParts($makeSlug, $modelSlug, $year, $assemblySlug, $assemblyName);
            foreach ($parts as $partIndex => $partData) {
                $part = SpPart::query()->updateOrCreate(
                    ['part_number' => $partData['part_number']],
                    [
                        'name' => $partData['name'],
                        'note' => $partData['note'],
                        'stock_status' => $partData['stock'],
                        'price_gbp_inc_vat' => $partData['price'],
                        'meta' => ['seed' => 'dummy'],
                        'last_synced_at' => now(),
                        'is_active' => true,
                    ]
                );

                SpAssemblyPart::query()->updateOrCreate(
                    ['assembly_id' => $assembly->id, 'part_id' => $part->id],
                    [
                        'qty_used' => $partData['qty_used'],
                        'sort_order' => $partIndex,
                        'note_override' => $partData['note_override'],
                        'price_override' => null,
                        'stock_override' => null,
                    ]
                );
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function catalogueBlueprint(): array
    {
        return [
            [
                'slug' => 'honda',
                'name' => 'Honda',
                'models' => [
                    ['slug' => 'afs110-wave', 'name' => 'AFS110 WAVE', 'years' => [2015, 2016, 2017]],
                    ['slug' => 'ca125s', 'name' => 'CA125S', 'years' => [1995, 1996]],
                ],
            ],
            [
                'slug' => 'yamaha',
                'name' => 'Yamaha',
                'models' => [
                    ['slug' => 'nmax-125', 'name' => 'NMAX 125', 'years' => [2021, 2022, 2023]],
                    ['slug' => 'xmax-300', 'name' => 'XMAX 300', 'years' => [2020, 2021]],
                ],
            ],
            [
                'slug' => 'suzuki',
                'name' => 'Suzuki',
                'models' => [
                    ['slug' => 'burgman-125', 'name' => 'BURGAMAN 125', 'years' => [2019, 2020, 2021]],
                ],
            ],
        ];
    }

    /** @return array<int, array{slug:string,name:string}> */
    private function countries(): array
    {
        return [
            ['slug' => 'england', 'name' => 'England'],
            ['slug' => 'hong-kong', 'name' => 'Hong Kong'],
        ];
    }

    /** @return array<int, array{slug:string,name:string}> */
    private function colours(): array
    {
        return [
            ['slug' => 'all-colours', 'name' => 'All Colours'],
            ['slug' => 'candy-lucid-redr264', 'name' => 'CANDY LUCID RED(R264)'],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function assemblyParts(string $makeSlug, string $modelSlug, string $year, string $assemblySlug, string $assemblyName): array
    {
        // Canonical demo parts based on your requested examples.
        if ($makeSlug === 'honda' && $modelSlug === 'afs110-wave' && $assemblySlug === 'front-brake-caliper') {
            return [
                ['part_number' => '06451961405', 'name' => 'SEAL SET, PISTON', 'stock' => 'IN STOCK', 'price' => 14.78, 'qty_used' => 1, 'note' => '', 'note_override' => ''],
                ['part_number' => '06455KWWC51', 'name' => 'PAD SET, FR.', 'stock' => 'NOT IN STOCK', 'price' => 36.17, 'qty_used' => 1, 'note' => '', 'note_override' => ''],
                ['part_number' => '43352568003', 'name' => 'SCREW, BLEEDER (NISSIN)', 'stock' => 'IN STOCK', 'price' => 9.74, 'qty_used' => 1, 'note' => '', 'note_override' => ''],
                ['part_number' => '43353461771', 'name' => 'CAP, BLEEDER', 'stock' => 'IN STOCK', 'price' => 6.97, 'qty_used' => 1, 'note' => '', 'note_override' => ''],
                ['part_number' => '45111MAJG41', 'name' => 'RING, STOPPER', 'stock' => 'NOT IN STOCK', 'price' => 4.61, 'qty_used' => 1, 'note' => '', 'note_override' => ''],
                ['part_number' => '45131GZ0006', 'name' => 'BOLT, PIN', 'stock' => '1-3 IN STOCK', 'price' => 7.51, 'qty_used' => 1, 'note' => '', 'note_override' => ''],
            ];
        }

        if ($makeSlug === 'honda' && $modelSlug === 'ca125s' && $assemblySlug === 'cylinder-head-cover') {
            return [
                [
                    'part_number' => '12231399010',
                    'name' => 'HOLDER, CAMSHAFT',
                    'stock' => 'NOT IN STOCK',
                    'price' => 72.22,
                    'qty_used' => 2,
                    'note' => 'PLEASE INFORM YOUR CUSTOMER THAT THIS IS A HONDA NON CONFORMING PART AND CANCELLATION IS NOT ALLOWED FOR 30 DAYS.',
                    'note_override' => '',
                ],
                ['part_number' => '12311KB4700', 'name' => 'COVER, CYLINDER HEAD', 'stock' => 'NOT IN STOCK', 'price' => 106.15, 'qty_used' => 1, 'note' => '', 'note_override' => ''],
                ['part_number' => '12391KB4670', 'name' => 'PACKING, HEAD COVER', 'stock' => 'NOT IN STOCK', 'price' => 25.06, 'qty_used' => 1, 'note' => '', 'note_override' => ''],
                ['part_number' => '90001123456', 'name' => 'BOLT, FLANGE, 6X33', 'stock' => 'IN STOCK', 'price' => 5.10, 'qty_used' => 4, 'note' => '', 'note_override' => ''],
            ];
        }

        $base = strtoupper(substr($makeSlug, 0, 2).substr($modelSlug, 0, 3).str_replace('-', '', $assemblySlug).substr($year, -2));
        $parts = [];
        for ($i = 1; $i <= 8; $i++) {
            $parts[] = [
                'part_number' => strtoupper(substr($base, 0, 6)).str_pad((string) ($i * 37), 5, '0', STR_PAD_LEFT),
                'name' => $assemblyName.' COMPONENT '.$i,
                'stock' => $i % 3 === 0 ? 'NOT IN STOCK' : 'IN STOCK',
                'price' => 8.5 + ($i * 3.2),
                'qty_used' => $i % 2 === 0 ? 1 : 2,
                'note' => '',
                'note_override' => '',
            ];
        }

        return $parts;
    }
}
