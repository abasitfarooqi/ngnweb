<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Motorbike;
use App\Models\Motorcycle;
use App\Models\NgnProduct;
use App\Models\SpPart;
use App\Support\NgnMotorcycleImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileCatalogueController extends Controller
{
    public function homeFeed(Request $request): JsonResponse
    {
        $limit = max(1, min(24, (int) $request->integer('limit', 6)));

        $new = Motorcycle::query()
            ->where('availability', 'for sale')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (Motorcycle $m) => [
                'id' => $m->id,
                'type' => 'new',
                'name' => trim((string) $m->make.' '.$m->model),
                'year' => $m->year,
                'price' => (float) ($m->sale_new_price ?? 0),
                'image_url' => NgnMotorcycleImage::urlForNewStock($m->file_path),
            ]);

        $used = Motorbike::query()
            ->join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
            ->select('motorbikes.*', 'motorbikes_sale.price', 'motorbikes_sale.image_one')
            ->where('motorbikes_sale.is_sold', 0)
            ->where(function ($q) {
                $q->where('motorbikes.is_ebike', false)->orWhereNull('motorbikes.is_ebike');
            })
            ->orderByDesc('motorbikes.id')
            ->limit($limit)
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'type' => 'used',
                'name' => trim((string) $m->make.' '.$m->model),
                'year' => $m->year,
                'price' => (float) ($m->price ?? 0),
                'reg_hint' => $m->reg_no ? '****'.substr((string) $m->reg_no, -3) : null,
                'image_url' => NgnMotorcycleImage::urlForUsedSale($m->image_one),
            ]);

        $products = NgnProduct::query()
            ->where('is_ecommerce', 1)
            ->where(function ($q) {
                $q->whereNull('dead')->orWhere('dead', 0);
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'normal_price', 'image_url'])
            ->map(fn (NgnProduct $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => (float) ($p->normal_price ?? 0),
                'image_url' => $p->image_url,
            ]);

        return response()->json([
            'bikes_new' => $new,
            'bikes_used' => $used,
            'shop_products' => $products,
            'links' => [
                'full_bikes' => '/bikes',
                'full_rentals' => '/rentals',
                'full_shop' => '/shop',
                'full_spare_parts' => '/spareparts',
            ],
        ]);
    }

    public function branches(): JsonResponse
    {
        $branches = Branch::query()
            ->orderBy('name')
            ->get(['id', 'name', 'address', 'city', 'postal_code', 'latitude', 'longitude']);

        return response()->json([
            'data' => $branches,
        ]);
    }

    public function bikes(Request $request): JsonResponse
    {
        $type = strtolower(trim((string) $request->string('type', 'all')));
        $perPage = max(1, min(60, (int) $request->integer('per_page', 20)));

        $payload = [];

        if (in_array($type, ['all', 'new'], true)) {
            $payload['new'] = Motorcycle::query()
                ->where('availability', 'for sale')
                ->orderByDesc('id')
                ->paginate($perPage, ['id', 'make', 'model', 'year', 'sale_new_price', 'file_path'], 'new_page')
                ->through(fn (Motorcycle $m) => [
                    'id' => $m->id,
                    'name' => trim((string) $m->make.' '.$m->model),
                    'make' => $m->make,
                    'model' => $m->model,
                    'year' => $m->year,
                    'price' => (float) ($m->sale_new_price ?? 0),
                    'image_url' => NgnMotorcycleImage::urlForNewStock($m->file_path),
                    'prefill_query' => [
                        'prefill_new' => $m->id,
                        'prefill_price' => (float) ($m->sale_new_price ?? 0),
                    ],
                ]);
        }

        if (in_array($type, ['all', 'used', 'ebike'], true)) {
            $usedQuery = Motorbike::query()
                ->join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
                ->select('motorbikes.*', 'motorbikes_sale.price', 'motorbikes_sale.image_one')
                ->where('motorbikes_sale.is_sold', 0);

            if ($type === 'ebike') {
                $usedQuery->where('motorbikes.is_ebike', true);
            } elseif ($type === 'used') {
                $usedQuery->where(function ($q) {
                    $q->where('motorbikes.is_ebike', false)->orWhereNull('motorbikes.is_ebike');
                });
            }

            $usedData = $usedQuery
                ->orderByDesc('motorbikes.id')
                ->paginate($perPage, ['*'], $type === 'ebike' ? 'ebike_page' : 'used_page')
                ->through(function ($m) {
                    $isEbike = (bool) ($m->is_ebike ?? false);

                    return [
                        'id' => $m->id,
                        'name' => trim((string) $m->make.' '.$m->model),
                        'make' => $m->make,
                        'model' => $m->model,
                        'year' => $m->year,
                        'is_ebike' => $isEbike,
                        'price' => (float) ($m->price ?? 0),
                        'reg_hint' => $m->reg_no ? '****'.substr((string) $m->reg_no, -3) : null,
                        'image_url' => NgnMotorcycleImage::urlForUsedSale($m->image_one),
                        'prefill_query' => [
                            $isEbike ? 'prefill_ebike' : 'prefill_used' => $m->id,
                            'prefill_price' => (float) ($m->price ?? 0),
                        ],
                    ];
                });

            if ($type === 'ebike') {
                $payload['ebikes'] = $usedData;
            } elseif ($type === 'used') {
                $payload['used'] = $usedData;
            } else {
                $payload['used_or_ebike'] = $usedData;
            }
        }

        return response()->json($payload);
    }

    public function rentals(Request $request): JsonResponse
    {
        $perPage = max(1, min(60, (int) $request->integer('per_page', 20)));

        $rentals = Motorbike::query()
            ->whereHas('rentingPricings')
            ->with('currentRentingPricing')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->through(function (Motorbike $bike) {
                $pricing = $bike->currentRentingPricing;

                return [
                    'id' => $bike->id,
                    'name' => trim((string) $bike->make.' '.$bike->model),
                    'make' => $bike->make,
                    'model' => $bike->model,
                    'year' => $bike->year,
                    'reg_hint' => $bike->reg_no ? '****'.substr((string) $bike->reg_no, -3) : null,
                    'weekly_price' => $pricing ? (float) ($pricing->weekly_price ?? 0) : null,
                    'minimum_deposit' => $pricing ? (float) ($pricing->minimum_deposit ?? 0) : null,
                ];
            });

        return response()->json([
            'data' => $rentals,
            'enquiry_mode' => 'enquiry_only',
        ]);
    }

    public function services(): JsonResponse
    {
        return response()->json([
            'categories' => [
                [
                    'key' => 'mot',
                    'label' => 'MOT',
                    'pages' => ['/mot', '/mot/book'],
                    'enquiry_form' => '/contact/service-booking',
                ],
                [
                    'key' => 'repairs',
                    'label' => 'Repairs and servicing',
                    'pages' => [
                        '/repairs',
                        '/motorbike-basic-service-london',
                        '/motorbike-full-service-london',
                        '/motorbike-repair-services',
                        '/motorbike-service-comparison',
                    ],
                    'enquiry_form' => '/contact/service-booking',
                ],
                [
                    'key' => 'delivery_recovery',
                    'label' => 'Delivery and recovery',
                    'pages' => ['/motorcycle-delivery', '/recovery'],
                    'enquiry_form' => '/contact/service-booking',
                ],
                [
                    'key' => 'finance',
                    'label' => 'Finance',
                    'pages' => ['/finance', '/account/finance/browse'],
                    'enquiry_form' => '/account/finance/browse#finance-enquiry',
                ],
                [
                    'key' => 'rentals',
                    'label' => 'Rentals',
                    'pages' => ['/rentals'],
                    'enquiry_form' => '/contact/service-booking',
                ],
            ],
        ]);
    }

    public function shopProducts(Request $request): JsonResponse
    {
        $perPage = max(1, min(80, (int) $request->integer('per_page', 24)));
        $search = trim((string) $request->string('search', ''));

        $query = NgnProduct::query()
            ->with(['brand:id,name', 'category:id,name'])
            ->where('is_ecommerce', 1)
            ->where(function ($q) {
                $q->whereNull('dead')->orWhere('dead', 0);
            });

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            });
        }

        $products = $query->orderByDesc('id')
            ->paginate($perPage)
            ->through(fn (NgnProduct $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'sku' => $p->sku,
                'price' => (float) ($p->normal_price ?? 0),
                'stock' => (float) ($p->global_stock ?? 0),
                'image_url' => $p->image_url,
                'brand' => $p->brand?->name,
                'category' => $p->category?->name,
            ]);

        return response()->json([
            'data' => $products,
        ]);
    }

    public function spareParts(Request $request): JsonResponse
    {
        $perPage = max(1, min(80, (int) $request->integer('per_page', 24)));
        $search = trim((string) $request->string('search', ''));

        $query = SpPart::query()
            ->where('is_active', true);

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('part_number', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%');
            });
        }

        $parts = $query->orderBy('part_number')
            ->paginate($perPage)
            ->through(fn (SpPart $part) => [
                'id' => $part->id,
                'part_number' => $part->part_number,
                'name' => $part->name,
                'stock_status' => $part->stock_status,
                'price' => (float) ($part->price_gbp_inc_vat ?? 0),
                'global_stock' => (float) ($part->global_stock ?? 0),
            ]);

        return response()->json([
            'data' => $parts,
            'checkout_system' => 'shared_with_main_shop',
        ]);
    }
}
