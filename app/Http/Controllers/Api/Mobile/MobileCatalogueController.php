<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Motorbike;
use App\Models\Motorcycle;
use App\Models\NgnBrand;
use App\Models\NgnCategory;
use App\Models\NgnProduct;
use App\Models\RentingPricing;
use App\Models\SpPart;
use App\Services\ShopService;
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

    public function newBikeDetail(int $id): JsonResponse
    {
        $bike = Motorcycle::query()
            ->where('availability', 'for sale')
            ->findOrFail($id);

        return response()->json([
            'id' => $bike->id,
            'type' => 'new',
            'name' => trim((string) $bike->make.' '.$bike->model),
            'make' => $bike->make,
            'model' => $bike->model,
            'year' => $bike->year,
            'price' => (float) ($bike->sale_new_price ?? 0),
            'description' => $bike->description,
            'engine' => $bike->engine,
            'category' => $bike->category,
            'image_url' => NgnMotorcycleImage::urlForNewStock($bike->file_path),
            'actions' => [
                'finance_link_query' => [
                    'source' => 'new-bike',
                    'bike_id' => $bike->id,
                    'bike_type' => 'new',
                    'price' => (float) ($bike->sale_new_price ?? 0),
                ],
            ],
        ]);
    }

    public function usedBikeDetail(int $id): JsonResponse
    {
        $bike = Motorbike::query()
            ->join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
            ->select('motorbikes.*', 'motorbikes_sale.price', 'motorbikes_sale.image_one', 'motorbikes_sale.mileage as sale_mileage')
            ->where('motorbikes.id', $id)
            ->where('motorbikes_sale.is_sold', 0)
            ->firstOrFail();

        $saleImagePaths = array_values(array_filter([
            $bike->image_one ?? null,
            $bike->image_two ?? null,
            $bike->image_three ?? null,
            $bike->image_four ?? null,
        ], fn ($p) => is_string($p) && trim($p) !== ''));

        $galleryUrls = array_values(array_unique(array_map(
            fn ($p) => NgnMotorcycleImage::urlForUsedSale($p),
            $saleImagePaths
        )));

        if ($galleryUrls === []) {
            $galleryUrls = [NgnMotorcycleImage::urlForUsedSale($bike->image_one)];
        }

        return response()->json([
            'id' => $bike->id,
            'type' => (bool) ($bike->is_ebike ?? false) ? 'ebike' : 'used',
            'name' => trim((string) $bike->make.' '.$bike->model),
            'make' => $bike->make,
            'model' => $bike->model,
            'year' => $bike->year,
            'engine' => $bike->engine,
            'colour' => $bike->color,
            'mileage' => $bike->sale_mileage,
            'reg_hint' => $bike->reg_no ? '****'.substr((string) $bike->reg_no, -3) : null,
            'price' => (float) ($bike->price ?? 0),
            'gallery_urls' => $galleryUrls,
            'image_url' => $galleryUrls[0],
            'actions' => [
                'finance_link_query' => [
                    'source' => (bool) ($bike->is_ebike ?? false) ? 'ebike' : 'used-bike',
                    'bike_id' => $bike->id,
                    'bike_type' => (bool) ($bike->is_ebike ?? false) ? 'ebike' : 'used',
                    'price' => (float) ($bike->price ?? 0),
                ],
            ],
        ]);
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
        $page = max(1, (int) $request->integer('page', 1));
        $search = trim((string) $request->string('search', ''));
        $sort = trim((string) $request->string('sort', 'newest'));
        if (! in_array($sort, ['newest', 'price_low', 'price_high', 'name'], true)) {
            $sort = 'newest';
        }

        $categoryId = 0;
        if ($request->filled('cid')) {
            $categoryId = max(0, (int) $request->integer('cid'));
        } elseif ($request->filled('category_id')) {
            $categoryId = max(0, (int) $request->integer('category_id'));
        }

        $brandId = 0;
        if ($request->filled('bid')) {
            $brandId = max(0, (int) $request->integer('bid'));
        } elseif ($request->filled('brand_id')) {
            $brandId = max(0, (int) $request->integer('brand_id'));
        }

        $catSlug = trim((string) $request->string('cat', ''));
        if ($catSlug === '') {
            $catSlug = trim((string) $request->string('category', ''));
        }
        $brandSlug = trim((string) $request->string('brand', ''));

        $query = NgnProduct::query()
            ->with(['brand:id,name,slug', 'category:id,name,slug'])
            ->where('is_ecommerce', 1)
            ->where(function ($q) {
                $q->whereNull('dead')->orWhere('dead', 0);
            });

        $shop = app(ShopService::class);

        if ($categoryId > 0) {
            $query->where('category_id', $categoryId);
        } elseif ($catSlug !== '') {
            $catIds = $shop->resolveEcommerceCategoryIdsBySlugs([$catSlug]);
            if ($catIds === []) {
                $query->whereRaw('0 = 1');
            } else {
                $query->whereIn('category_id', $catIds);
            }
        }

        if ($brandId > 0) {
            $query->where('brand_id', $brandId);
        } elseif ($brandSlug !== '') {
            $brandIds = $shop->resolveEcommerceBrandIdsBySlugs([$brandSlug]);
            if ($brandIds === []) {
                $query->whereRaw('0 = 1');
            } else {
                $query->whereIn('brand_id', $brandIds);
            }
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('sku', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            });
        }

        match ($sort) {
            'price_low' => $query->orderBy('normal_price', 'asc')->orderBy('id', 'asc'),
            'price_high' => $query->orderBy('normal_price', 'desc')->orderBy('id', 'desc'),
            'name' => $query->orderBy('name', 'asc')->orderBy('id', 'asc'),
            default => $query->orderByDesc('id'),
        };

        $products = $query
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn (NgnProduct $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'sku' => $p->sku,
                'price' => (float) ($p->normal_price ?? 0),
                'stock' => (float) ($p->global_stock ?? 0),
                'image_url' => $p->image_url,
                'brand' => $p->brand?->name,
                'brand_slug' => $p->brand?->slug,
                'category' => $p->category?->name,
                'category_slug' => $p->category?->slug,
                'category_id' => $p->category_id,
                'brand_id' => $p->brand_id,
            ]);

        return response()->json([
            'data' => $products,
        ]);
    }

    public function shopFilters(): JsonResponse
    {
        $brands = NgnBrand::query()
            ->where('is_ecommerce', 1)
            ->where(function ($q) {
                $q->whereNull('is_active')->orWhere('is_active', 1);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $categories = NgnCategory::query()
            ->where('is_ecommerce', 1)
            ->where(function ($q) {
                $q->whereNull('is_active')->orWhere('is_active', 1);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'brands' => $brands,
            'categories' => $categories,
        ]);
    }

    public function spareParts(Request $request): JsonResponse
    {
        $perPage = max(1, min(80, (int) $request->integer('per_page', 24)));
        $page = max(1, (int) $request->integer('page', 1));
        $search = trim((string) $request->string('search', ''));
        $manufacturer = trim((string) $request->string('manufacturer', ''));
        $model = trim((string) $request->string('model', ''));
        $assembly = trim((string) $request->string('assembly', ''));

        $query = SpPart::query()
            ->where('is_active', true)
            ->with(['assemblyParts' => function ($q): void {
                $q->orderBy('sort_order')->with(['assembly.fitment.model.make']);
            }]);

        if ($search !== '') {
            $needle = str_replace(' ', '', $search);
            $query->where(function ($q) use ($search, $needle): void {
                $q->where('part_number', 'like', '%'.$needle.'%')
                    ->orWhere('name', 'like', '%'.$search.'%');
            });
        }

        if ($manufacturer !== '') {
            $query->whereHas('assemblyParts.assembly.fitment.model.make', function ($q) use ($manufacturer): void {
                $q->where('slug', $manufacturer);
            });
        }

        if ($model !== '') {
            $query->whereHas('assemblyParts.assembly.fitment.model', function ($q) use ($model): void {
                $q->where('slug', $model);
            });
        }

        if ($assembly !== '') {
            $query->whereHas('assemblyParts.assembly', function ($q) use ($assembly): void {
                $q->where('slug', $assembly);
            });
        }

        $parts = $query->orderBy('part_number')
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(function (SpPart $part) {
                $line = $part->assemblyParts->first();
                $assembly = $line?->assembly;
                $fitment = $assembly?->fitment;
                $m = $fitment?->model;
                $make = $m?->make;

                return [
                    'id' => $part->id,
                    'part_number' => $part->part_number,
                    'name' => $part->name,
                    'stock_status' => $line?->stock_override ?: ($part->stock_status ?? 'NOT IN STOCK'),
                    'price' => (float) ($line?->price_override ?? $part->price_gbp_inc_vat ?? 0),
                    'global_stock' => (float) ($part->global_stock ?? 0),
                    'image_url' => (string) ($assembly?->image_url ?? ''),
                    'manufacturer_name' => (string) ($make?->name ?? ''),
                    'model_name' => (string) ($m?->name ?? ''),
                    'assembly_name' => (string) ($assembly?->name ?? ''),
                    'sp_assembly_id' => (int) ($assembly?->id ?? 0),
                ];
            });

        return response()->json([
            'data' => $parts,
            'checkout_system' => 'shared_with_main_shop',
        ]);
    }

    public function shopProductDetail(string $idOrSlug): JsonResponse
    {
        $product = NgnProduct::query()
            ->with(['brand:id,name,slug', 'category:id,name,slug', 'productImages:id,product_id,image_url'])
            ->where('is_ecommerce', 1)
            ->where(function ($q) {
                $q->whereNull('dead')->orWhere('dead', 0);
            })
            ->where(function ($q) use ($idOrSlug): void {
                if (is_numeric($idOrSlug)) {
                    $q->orWhere('id', (int) $idOrSlug);
                }
                $q->orWhere('slug', $idOrSlug);
            })
            ->firstOrFail();

        $related = NgnProduct::query()
            ->where('is_ecommerce', 1)
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->where(function ($q) {
                $q->whereNull('dead')->orWhere('dead', 0);
            })
            ->latest('id')
            ->limit(8)
            ->get(['id', 'name', 'slug', 'normal_price', 'image_url']);

        $gallery = collect([$product->image_url])
            ->merge($product->productImages->pluck('image_url'))
            ->filter(fn ($item) => is_string($item) && trim($item) !== '')
            ->values();

        $shop = app(ShopService::class);
        $grouped = $shop->getProductBySlug((string) $product->slug);
        $variants = [];
        $stockByBranch = [];
        $effectiveStock = (float) ($product->global_stock ?? 0);
        $displayPrice = (float) ($product->normal_price ?? 0);
        $displayName = $product->name;
        $displaySlug = (string) $product->slug;
        $displayDesc = $product->description;
        $displayExtDesc = $product->extended_description;
        $displayColour = $product->colour;

        if (is_array($grouped) && ! empty($grouped['variants'])) {
            $displayName = (string) ($grouped['name'] ?? $displayName);
            $displaySlug = (string) ($grouped['slug'] ?? $displaySlug);
            $displayDesc = (string) ($grouped['description'] ?? $displayDesc);
            $displayExtDesc = (string) ($grouped['extended_description'] ?? $displayExtDesc);
            $displayColour = (string) ($grouped['colour'] ?? $displayColour);
            foreach ($grouped['variants'] as $v) {
                $variants[] = [
                    'id' => (int) ($v['id'] ?? 0),
                    'sku' => (string) ($v['sku'] ?? ''),
                    'name' => (string) ($v['name'] ?? ''),
                    'variation' => (string) ($v['variation'] ?? ''),
                    'slug' => (string) ($v['slug'] ?? ''),
                    'total_balance' => (int) ($v['total_balance'] ?? 0),
                ];
            }
            $firstVariantId = (int) ($variants[0]['id'] ?? $product->id);
            $avail = $shop->getProductAvailability($firstVariantId);
            $effectiveStock = (float) ($avail['total_balance'] ?? 0);
            foreach ($avail['branches'] ?? [] as $b) {
                $stockByBranch[] = [
                    'branch_name' => (string) ($b->branch_name ?? ''),
                    'balance' => (float) ($b->branch_balance ?? 0),
                ];
            }
            $displayPrice = (float) ($grouped['normal_price'] ?? $product->normal_price ?? 0);
        }

        return response()->json([
            'data' => [
                'id' => $product->id,
                'name' => $displayName,
                'slug' => $displaySlug,
                'sku' => $product->sku,
                'ean' => $product->ean,
                'price' => $displayPrice,
                'stock' => $effectiveStock,
                'stock_message' => $effectiveStock > 0 ? 'In stock' : 'Out of stock',
                'description' => $displayDesc,
                'extended_description' => $displayExtDesc,
                'variation' => $product->variation,
                'colour' => $displayColour,
                'brand' => $product->brand ? ['id' => $product->brand->id, 'name' => $product->brand->name, 'slug' => $product->brand->slug] : null,
                'category' => $product->category ? ['id' => $product->category->id, 'name' => $product->category->name, 'slug' => $product->category->slug] : null,
                'gallery' => $gallery,
                'variants' => $variants,
                'stock_by_branch' => $stockByBranch,
                'attributes' => [
                    'vatable' => (bool) ($product->vatable ?? false),
                    'is_oxford' => (bool) ($product->is_oxford ?? false),
                ],
                'related_items' => $related->map(fn (NgnProduct $row) => [
                    'id' => $row->id,
                    'name' => $row->name,
                    'slug' => $row->slug,
                    'price' => (float) ($row->normal_price ?? 0),
                    'image_url' => $row->image_url,
                ])->values(),
            ],
        ]);
    }

    public function rentalDetail(int $id): JsonResponse
    {
        $bike = Motorbike::query()
            ->with(['images:id,motorbike_id,image_path', 'branch:id,name,address,city'])
            ->findOrFail($id);
        $pricing = RentingPricing::query()->where('motorbike_id', $bike->id)->where('iscurrent', true)->latest('id')->first();

        $galleryPaths = $bike->images->pluck('image_path')->filter()->values();
        $galleryUrls = $galleryPaths->map(fn ($p) => NgnMotorcycleImage::urlForUsedSale((string) $p))->values()->all();
        $primaryImage = $galleryUrls[0] ?? NgnMotorcycleImage::urlForUsedSale(null);

        return response()->json([
            'data' => [
                'id' => $bike->id,
                'name' => trim((string) $bike->make.' '.$bike->model),
                'make' => $bike->make,
                'model' => $bike->model,
                'year' => $bike->year,
                'engine' => $bike->engine,
                'fuel_type' => $bike->fuel_type,
                'colour' => $bike->color,
                'reg_hint' => $bike->reg_no ? '****'.substr((string) $bike->reg_no, -3) : null,
                'image_url' => $primaryImage,
                'gallery_urls' => $galleryUrls,
                'branch' => $bike->branch ? [
                    'id' => $bike->branch->id,
                    'name' => $bike->branch->name,
                    'address' => $bike->branch->address,
                    'city' => $bike->branch->city,
                ] : null,
                'pricing' => [
                    'weekly_price' => $pricing ? (float) ($pricing->weekly_price ?? 0) : null,
                    'minimum_deposit' => $pricing ? (float) ($pricing->minimum_deposit ?? 0) : null,
                    'initial_payment' => $pricing ? round((float) ($pricing->weekly_price ?? 0) + (float) ($pricing->minimum_deposit ?? 0), 2) : null,
                ],
                'requirements' => [
                    'valid_licence' => true,
                    'address_proof' => true,
                    'deposit_required' => true,
                ],
                'terms' => [
                    'enquiry_only' => true,
                    'api_enquiry_endpoint' => '/api/v1/mobile/enquiries',
                ],
                'gallery' => $bike->images->pluck('image_path')->filter()->values(),
            ],
        ]);
    }
}
