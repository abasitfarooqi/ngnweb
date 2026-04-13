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

class ShopService
{
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

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get a single product by slug (with all variants grouped under slug prefix).
     */
    public function getProductBySlug(string $slug): ?array
    {
        $products = DB::table('ngn_products')
            ->where('slug', 'like', $slug.'%')
            ->where('is_ecommerce', true)
            ->where('dead', false)
            ->get();

        if ($products->isEmpty()) {
            return null;
        }

        $productIds = $products->pluck('id')->toArray();

        $uniqueImages = DB::table('ngn_product_images')
            ->whereIn('product_id', $productIds)
            ->pluck('image_url')
            ->unique()
            ->values()
            ->all();

        $totalBalances = $this->calculateTotalBalances($productIds);

        $variants = $products->map(function ($p) use ($totalBalances) {
            return [
                'id' => $p->id,
                'sku' => trim($p->sku),
                'name' => trim($p->name),
                'variation' => trim($p->variation),
                'slug' => trim($p->slug),
                'total_balance' => $totalBalances[$p->id] ?? 0,
            ];
        })->values()->all();

        return [
            'name' => $products[0]->name,
            'slug' => $products[0]->slug,
            'image_url' => $products[0]->image_url,
            'image_array' => $uniqueImages,
            'normal_price' => $products[0]->normal_price,
            'global_stock' => $products[0]->global_stock,
            'meta_title' => $products[0]->meta_title,
            'meta_description' => $products[0]->meta_description,
            'description' => strip_tags($products[0]->description),
            'extended_description' => strip_tags($products[0]->extended_description),
            'colour' => $products[0]->colour,
            'counts' => count($variants),
            'variants' => $variants,
        ];
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
