<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\NgnBrand;
use App\Models\NgnCategory;
use App\Models\NgnProduct;
use App\Models\SystemCountry;
use App\Models\TermsVersion;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopService
{
    public function __construct(
        private readonly NgnProductCatalogService $catalog
    ) {}

    /**
     * Get paginated products with optional filters.
     */
    public function getProducts(
        int $perPage = 12,
        int $page = 1,
        ?string $search = null,
        ?string $sort = null,
        array $categoryIds = [],
        array $brandIds = [],
        array $categorySlugs = [],
        array $brandSlugs = []
    ): LengthAwarePaginator {
        $query = NgnProduct::select(
            'ngn_products.name',
            'ngn_products.slug',
            'ngn_products.image_url',
            'ngn_brands.name as brand',
            'ngn_categories.name as category',
            'ngn_categories.slug as category_slug',
            'ngn_brands.slug as brand_slug',
            'ngn_products.normal_price',
            DB::raw('SUM(ngn_products.global_stock) as global_stock'),
            DB::raw('MAX(ngn_products.created_at) as max_created_at')
        )
            ->join('ngn_models', 'ngn_products.model_id', '=', 'ngn_models.id')
            ->join('ngn_brands', 'ngn_products.brand_id', '=', 'ngn_brands.id')
            ->join('ngn_categories', 'ngn_products.category_id', '=', 'ngn_categories.id')
            ->whereNull('ngn_products.parent_product_id')
            ->where('ngn_products.is_ecommerce', 1)
            ->whereNotNull('ngn_products.slug')
            ->where('ngn_products.slug', '!=', '')
            ->where('ngn_products.dead', 0)
            ->groupBy(
                'ngn_products.name',
                'ngn_products.slug',
                'ngn_products.image_url',
                'ngn_brands.name',
                'ngn_categories.name',
                'ngn_categories.slug',
                'ngn_brands.slug',
                'ngn_products.normal_price',
            );

        if (! empty($categoryIds)) {
            $query->whereIn('ngn_products.category_id', $categoryIds);
        }

        if (! empty($brandIds)) {
            $query->whereIn('ngn_products.brand_id', $brandIds);
        }

        $categorySlugNorm = $this->normaliseShopSlugs($categorySlugs);
        if ($categorySlugNorm !== []) {
            $resolvedCategoryIds = $this->ecommerceCategoryIdsForNormalisedSlugs($categorySlugNorm);
            if ($resolvedCategoryIds === []) {
                $query->whereRaw('0 = 1');
            } else {
                $query->whereIn('ngn_products.category_id', $resolvedCategoryIds);
            }
        }

        $brandSlugNorm = $this->normaliseShopSlugs($brandSlugs);
        if ($brandSlugNorm !== []) {
            $resolvedBrandIds = $this->ecommerceBrandIdsForNormalisedSlugs($brandSlugNorm);
            if ($resolvedBrandIds === []) {
                $query->whereRaw('0 = 1');
            } else {
                $query->whereIn('ngn_products.brand_id', $resolvedBrandIds);
            }
        }

        if ($search) {
            $terms = preg_split('/[\s,-]+/', trim($search), -1, PREG_SPLIT_NO_EMPTY);
            $query->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->where(function ($sub) use ($term) {
                        $sub->where('ngn_products.name', 'like', "%{$term}%")
                            ->orWhere('ngn_products.sku', 'like', "%{$term}%")
                            ->orWhere('ngn_products.description', 'like', "%{$term}%")
                            ->orWhere('ngn_products.variation', 'like', "%{$term}%");
                    });
                }
            });
        }

        match ($sort) {
            'price_low' => $query->orderBy('ngn_products.normal_price', 'asc'),
            'price_high' => $query->orderBy('ngn_products.normal_price', 'desc'),
            'name' => $query->orderBy('ngn_products.name', 'asc'),
            default => $query->orderBy('max_created_at', 'desc'),
        };

        return $query->paginate($perPage, ['*'], 'page', $page)
            ->through(function ($product) {
                $product->image_url = $this->catalog->publicAssetUrl($product->image_url);

                return $product;
            });
    }

    /**
     * Resolve a shop product by slug.
     *
     * Supports legacy rows sharing one slug (sizes in variation), colour suffix slugs,
     * per-size slugs ({base}-variant-xs), and parent_product_id children.
     */
    public function getProductBySlug(string $slug): ?array
    {
        $slug = trim($slug);
        if ($slug === '') {
            return null;
        }

        $query = DB::table('ngn_products')
            ->where('is_ecommerce', true)
            ->where('dead', false);

        $exactMatches = (clone $query)->where('slug', $slug)->orderBy('id')->get();

        $selectedVariantId = null;
        $variants = collect();
        $display = null;

        if ($exactMatches->count() > 1) {
            $display = $exactMatches->first();
            $variants = $exactMatches;
        } elseif ($exactMatches->count() === 1) {
            $hit = $exactMatches->first();
            $selectedVariantId = (int) $hit->id;

            $children = (clone $query)
                ->where('parent_product_id', $hit->id)
                ->orderBy('size_label')
                ->orderBy('id')
                ->get();

            if ($children->isNotEmpty()) {
                $display = $hit;
                $variants = $children;
            } elseif ($hit->parent_product_id) {
                $display = (clone $query)->where('id', $hit->parent_product_id)->first() ?? $hit;
                $variants = (clone $query)
                    ->where('parent_product_id', $display->id)
                    ->orderBy('size_label')
                    ->orderBy('id')
                    ->get();
            } elseif ($base = $this->sizeVariantBaseSlug($slug)) {
                $variants = (clone $query)
                    ->where('slug', 'like', $base.'-variant-%')
                    ->orderBy('slug')
                    ->orderBy('id')
                    ->get();
                $display = $variants->firstWhere('slug', $slug) ?? $hit;
                if ($variants->isEmpty()) {
                    $display = $hit;
                    $variants = collect([$hit]);
                }
            } else {
                $display = $hit;
                $variants = collect([$hit]);
            }
        } else {
            $family = (clone $query)
                ->where(function ($q) use ($slug) {
                    $q->where('slug', $slug)
                        ->orWhere('slug', 'like', $slug.'-%');
                })
                ->orderBy('slug')
                ->orderBy('id')
                ->get();

            if ($family->isEmpty()) {
                return null;
            }

            $sameSlug = $family->where('slug', $slug)->values();
            if ($sameSlug->count() > 1) {
                $display = $sameSlug->first();
                $variants = $sameSlug;
            } else {
                $display = $family->first();
                $variants = $family;
            }
        }

        if (! $display || $variants->isEmpty()) {
            return null;
        }

        $productIds = $variants->pluck('id')->prepend($display->id)->unique()->values()->all();

        $uniqueImages = DB::table('ngn_product_images')
            ->whereIn('product_id', $productIds)
            ->pluck('image_url')
            ->unique()
            ->values()
            ->all();

        $totalBalances = $this->calculateTotalBalances($variants->pluck('id')->all());

        $variantRows = $variants->map(function ($p) use ($totalBalances) {
            return [
                'id' => $p->id,
                'sku' => trim($p->sku),
                'name' => trim($p->name),
                'variation' => trim((string) $p->variation),
                'size_label' => trim((string) ($p->size_label ?? '')),
                'colour' => trim((string) ($p->colour ?? '')),
                'label' => $this->formatVariantLabel($p),
                'slug' => trim($p->slug),
                'normal_price' => (float) $p->normal_price,
                'total_balance' => $totalBalances[$p->id] ?? 0,
            ];
        })->values()->all();

        if ($selectedVariantId === null && count($variantRows) === 1) {
            $selectedVariantId = (int) $variantRows[0]['id'];
        }

        $resolvedImages = array_values(array_filter(array_map(
            fn (string $path) => $this->catalog->publicAssetUrl($path),
            $uniqueImages
        )));

        $mainImage = $this->catalog->publicAssetUrl($display->image_url);
        if ($mainImage && ! in_array($mainImage, $resolvedImages, true)) {
            array_unshift($resolvedImages, $mainImage);
        }

        $hasVariantOptions = count($variantRows) > 1
            || (count($variantRows) === 1 && (
                (int) ($variants->first()->parent_product_id ?? 0) === (int) $display->id
                || trim((string) ($variantRows[0]['size_label'] ?? '')) !== ''
                || trim((string) ($variantRows[0]['variation'] ?? '')) !== ''
            ));

        return [
            'name' => $display->name,
            'slug' => $display->slug,
            'canonical_slug' => $slug,
            'selected_variant_id' => $selectedVariantId,
            'image_url' => $mainImage,
            'video_url' => $this->resolveVideoUrl($display->video_url ?? null),
            'image_array' => $resolvedImages,
            'has_variant_options' => $hasVariantOptions,
            'normal_price' => (float) $display->normal_price,
            'global_stock' => $display->global_stock,
            'meta_title' => $display->meta_title,
            'meta_description' => $display->meta_description,
            'description' => strip_tags((string) $display->description),
            'extended_description' => strip_tags((string) $display->extended_description),
            'colour' => $display->colour,
            'counts' => count($variantRows),
            'variants' => $variantRows,
        ];
    }

    /**
     * Base slug for per-size URLs such as atom-2-bast-b7-variant-xs.
     */
    private function resolveVideoUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])
            || Str::contains($path, ['youtube.com', 'youtu.be', 'vimeo.com'])) {
            return $path;
        }

        return $this->catalog->publicAssetUrl($path);
    }

    private function sizeVariantBaseSlug(string $slug): ?string
    {
        if (preg_match('/^(.+)-variant-[a-z0-9]+$/i', $slug, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    private function formatVariantLabel(object $product): string
    {
        $labelParts = array_filter([
            $product->size_label ?? null,
            $product->variation ?? null,
            $product->colour ?? null,
        ]);

        return $labelParts !== [] ? implode(' · ', $labelParts) : trim((string) $product->sku);
    }

    /**
     * Get product by ID.
     */
    public function getProductById(int $id): ?NgnProduct
    {
        return NgnProduct::with('brand', 'category', 'model')
            ->where('id', $id)
            ->where('is_ecommerce', 1)
            ->where('dead', false)
            ->first();
    }

    /**
     * Get availability (stock balance) for a product.
     */
    public function getProductAvailability(int $id): array
    {
        $total = $this->calculateTotalBalances([$id])[$id] ?? 0;

        $branches = DB::table('ngn_stock_movements')
            ->where('product_id', $id)
            ->select(
                'branch_id',
                DB::raw('(SELECT name FROM branches WHERE id = branch_id) AS branch_name'),
                DB::raw('SUM(`in`) - SUM(`out`) AS branch_balance')
            )
            ->groupBy('branch_id')
            ->get();

        return [
            'total_balance' => $total,
            'branches' => $branches,
        ];
    }

    public static function clearNavigationCache(): void
    {
        cache()->forget('shop_categories');
        cache()->forget('shop_brands');
    }

    /**
     * @param  array<int, mixed>  $slugs
     * @return list<int>
     */
    public function resolveEcommerceCategoryIdsBySlugs(array $slugs): array
    {
        $norm = $this->normaliseShopSlugs($slugs);

        return $norm === [] ? [] : $this->ecommerceCategoryIdsForNormalisedSlugs($norm);
    }

    /**
     * @param  array<int, mixed>  $slugs
     * @return list<int>
     */
    public function resolveEcommerceBrandIdsBySlugs(array $slugs): array
    {
        $norm = $this->normaliseShopSlugs($slugs);

        return $norm === [] ? [] : $this->ecommerceBrandIdsForNormalisedSlugs($norm);
    }

    /**
     * @param  array<int, mixed>  $slugs
     * @return list<string>
     */
    private function normaliseShopSlugs(array $slugs): array
    {
        $out = [];
        foreach ($slugs as $raw) {
            $s = strtolower(trim((string) $raw));
            if ($s !== '') {
                $out[$s] = true;
            }
        }

        return array_keys($out);
    }

    /**
     * @param  list<string>  $normalisedSlugs
     * @return list<int>
     */
    private function ecommerceCategoryIdsForNormalisedSlugs(array $normalisedSlugs): array
    {
        return NgnCategory::query()
            ->where('is_ecommerce', true)
            ->where(function ($q) use ($normalisedSlugs) {
                foreach ($normalisedSlugs as $slug) {
                    $q->orWhereRaw('LOWER(TRIM(COALESCE(slug, ?))) = ?', ['', $slug]);
                }
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @param  list<string>  $normalisedSlugs
     * @return list<int>
     */
    private function ecommerceBrandIdsForNormalisedSlugs(array $normalisedSlugs): array
    {
        return NgnBrand::query()
            ->where('is_ecommerce', true)
            ->where(function ($q) use ($normalisedSlugs) {
                foreach ($normalisedSlugs as $slug) {
                    $q->orWhereRaw('LOWER(TRIM(COALESCE(slug, ?))) = ?', ['', $slug]);
                }
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Get all ecommerce brands.
     */
    public function getBrands(): Collection
    {
        return cache()->remember('shop_brands', 3600, function () {
            return NgnBrand::select('id', 'name', 'image_url', 'slug', 'description')
                ->where('is_ecommerce', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get all ecommerce categories.
     */
    public function getCategories(): Collection
    {
        return cache()->remember('shop_categories', 3600, function () {
            return NgnCategory::select('id', 'name', 'image_url', 'slug', 'description')
                ->where('is_ecommerce', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get blog posts with pagination.
     */
    public function getBlogPosts(int $perPage = 9, int $page = 1): LengthAwarePaginator
    {
        return BlogPost::with('category')
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get a single blog post by slug.
     */
    public function getBlogPost(string $slug): ?BlogPost
    {
        return BlogPost::with(['category', 'images'])->where('slug', $slug)->first();
    }

    /**
     * Calculate total stock balance for an array of product IDs.
     */
    public function calculateTotalBalances(array $productIds): array
    {
        $result = array_fill_keys($productIds, 0);

        if (empty($productIds)) {
            return $result;
        }

        $balances = DB::table('ngn_stock_movements')
            ->whereIn('product_id', $productIds)
            ->select(
                'product_id',
                'branch_id',
                DB::raw('SUM(`in`) - SUM(`out`) AS branch_balance')
            )
            ->groupBy('product_id', 'branch_id')
            ->get();

        foreach ($balances as $balance) {
            if (isset($result[$balance->product_id])) {
                $result[$balance->product_id] += (float) $balance->branch_balance;
            }
        }

        return $result;
    }

    /**
     * Get terms version content by slug/type.
     */
    public function getTerms(string $type): ?TermsVersion
    {
        return TermsVersion::where('type', $type)->latest()->first();
    }

    /**
     * Get countries.
     */
    public function getCountries(): Collection
    {
        return SystemCountry::orderBy('name')->get();
    }
}
