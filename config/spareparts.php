<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Provider Mode
    |--------------------------------------------------------------------------
    |
    | both_parallel: DB first, then external adapter, then config fallback.
    | db:            DB first, then config fallback.
    | external:      External first, then DB, then config fallback.
    |
    */
    'provider_mode' => env('SPAREPARTS_PROVIDER_MODE', 'both_parallel'),

    /*
    |--------------------------------------------------------------------------
    | Spare Parts Catalogue Seed
    |--------------------------------------------------------------------------
    |
    | This is the initial catalogue structure used by the Livewire spare parts
    | browser. It mirrors the OEM-style journey:
    | manufacturer -> model -> year -> country -> colour -> assembly -> parts.
    |
    | The structure is intentionally data-driven so it can later be replaced by
    | importer jobs/API sync without changing page logic.
    |
    */
    'catalogue' => [
        'honda' => [
            'name' => 'Honda',
            'models' => [
                'afs110-wave' => [
                    'name' => 'AFS110 WAVE',
                    'years' => [
                        '2015' => [
                            'countries' => [
                                'all-countries' => [
                                    'name' => 'All Countries',
                                    'colours' => [
                                        'candy-lucid-redr264' => [
                                            'name' => 'CANDY LUCID RED(R264)',
                                            'assemblies' => [
                                                [
                                                    'id' => '5826019',
                                                    'slug' => 'front-brake-caliper',
                                                    'name' => 'FRONT BRAKE CALIPER',
                                                    'parts' => [
                                                        [
                                                            'part_number' => '06451961405',
                                                            'name' => 'SEAL SET, PISTON',
                                                            'stock' => 'IN STOCK',
                                                            'price_gbp_inc_vat' => 14.78,
                                                            'qty_used' => 1,
                                                        ],
                                                        [
                                                            'part_number' => '06455KWWC51',
                                                            'name' => 'PAD SET, FR.',
                                                            'stock' => 'NOT IN STOCK',
                                                            'price_gbp_inc_vat' => 36.17,
                                                            'qty_used' => 1,
                                                        ],
                                                        [
                                                            'part_number' => '43352568003',
                                                            'name' => 'SCREW, BLEEDER (NISSIN)',
                                                            'stock' => 'IN STOCK',
                                                            'price_gbp_inc_vat' => 9.74,
                                                            'qty_used' => 1,
                                                        ],
                                                        [
                                                            'part_number' => '43353461771',
                                                            'name' => 'CAP, BLEEDER',
                                                            'stock' => 'IN STOCK',
                                                            'price_gbp_inc_vat' => 6.97,
                                                            'qty_used' => 1,
                                                        ],
                                                        [
                                                            'part_number' => '45111MAJG41',
                                                            'name' => 'RING, STOPPER',
                                                            'stock' => 'NOT IN STOCK',
                                                            'price_gbp_inc_vat' => 4.61,
                                                            'qty_used' => 1,
                                                        ],
                                                        [
                                                            'part_number' => '45131GZ0006',
                                                            'name' => 'BOLT, PIN',
                                                            'stock' => '1-3 IN STOCK',
                                                            'price_gbp_inc_vat' => 7.51,
                                                            'qty_used' => 1,
                                                        ],
                                                    ],
                                                ],
                                                ['id' => '5826025', 'slug' => 'body-cover', 'name' => 'BODY COVER', 'parts' => []],
                                                ['id' => '5825992', 'slug' => 'cam-chaintensioner', 'name' => 'CAM CHAIN/TENSIONER', 'parts' => []],
                                                ['id' => '5825991', 'slug' => 'camshaftvalve', 'name' => 'CAMSHAFT/VALVE', 'parts' => []],
                                                ['id' => '5826041', 'slug' => 'caution-label', 'name' => 'CAUTION LABEL', 'parts' => []],
                                                ['id' => '5825996', 'slug' => 'clutch', 'name' => 'CLUTCH', 'parts' => []],
                                                ['id' => '5826002', 'slug' => 'crankcase', 'name' => 'CRANKCASE', 'parts' => []],
                                                ['id' => '5826003', 'slug' => 'crankshaftpiston', 'name' => 'CRANKSHAFT/PISTON', 'parts' => []],
                                                ['id' => '5825993', 'slug' => 'cylinder', 'name' => 'CYLINDER', 'parts' => []],
                                                ['id' => '5825990', 'slug' => 'cylinder-head', 'name' => 'CYLINDER HEAD', 'parts' => []],
                                                ['id' => '5825989', 'slug' => 'cylinder-head-cover', 'name' => 'CYLINDER HEAD COVER', 'parts' => []],
                                                ['id' => '5826027', 'slug' => 'exhaust-muffler', 'name' => 'EXHAUST MUFFLER', 'parts' => []],
                                            ],
                                        ],
                                        'pearl-procyon-blacknha06' => [
                                            'name' => 'PEARL PROCYON BLACK(NHA06)',
                                            'assemblies' => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        '2012' => [
                            'countries' => [
                                'all-countries' => [
                                    'name' => 'All Countries',
                                    'colours' => [],
                                ],
                            ],
                        ],
                    ],
                ],
                'cb2504' => [
                    'name' => 'CB2504',
                    'years' => [
                        '2004' => [
                            'countries' => [
                                'hong-kong' => [
                                    'name' => 'hong kong',
                                    'colours' => [
                                        'all-colours' => [
                                            'name' => 'All Colours',
                                            'assemblies' => [
                                                [
                                                    'id' => '4761482',
                                                    'slug' => 'cylinder-head-cover',
                                                    'name' => 'CYLINDER HEAD COVER',
                                                    'parts' => [
                                                        [
                                                            'part_number' => '12231399010',
                                                            'name' => 'HOLDER, CAMSHAFT',
                                                            'stock' => 'NOT IN STOCK',
                                                            'price_gbp_inc_vat' => 72.22,
                                                            'qty_used' => 2,
                                                            'note' => 'PLEASE INFORM YOUR CUSTOMER THAT THIS IS A HONDA NON CONFORMING PART AND CANCELLATION IS NOT ALLOWED FOR 30 DAYS. IF IT ARRIVES WITHIN THIS TIME IT IS NOT ALLOWED TO BE CANCELLED OR RETURNED',
                                                        ],
                                                        [
                                                            'part_number' => '12311KB4700',
                                                            'name' => 'COVER, CYLINDER HEAD',
                                                            'stock' => 'NOT IN STOCK',
                                                            'price_gbp_inc_vat' => 106.15,
                                                            'qty_used' => 1,
                                                        ],
                                                        [
                                                            'part_number' => '12391KB4670',
                                                            'name' => 'PACKING, HEAD COVER',
                                                            'stock' => 'NOT IN STOCK',
                                                            'price_gbp_inc_vat' => 25.06,
                                                            'qty_used' => 1,
                                                        ],
                                                        [
                                                            'part_number' => '90036KB4670',
                                                            'name' => 'BOLT, FLANGE, 6X33',
                                                            'stock' => '1-3 IN STOCK',
                                                            'price_gbp_inc_vat' => 7.34,
                                                            'qty_used' => 2,
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'yamaha' => ['name' => 'Yamaha', 'models' => []],
        'kawasaki' => ['name' => 'Kawasaki', 'models' => []],
        'suzuki' => ['name' => 'Suzuki', 'models' => []],
    ],
];
