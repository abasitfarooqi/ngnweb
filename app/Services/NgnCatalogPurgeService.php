<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class NgnCatalogPurgeService
{
    public const PURGE_PASSWORD = 'iamtheadmingo';

    public function purgeAll(): array
    {
        return DB::transaction(function () {
            $counts = [
                'stock_movements' => DB::table('ngn_stock_movements')->count(),
                'attributes' => DB::table('ngn_attributes')->count(),
                'images' => DB::table('ngn_product_images')->count(),
                'products' => DB::table('ngn_products')->count(),
                'models' => DB::table('ngn_models')->count(),
                'brands' => DB::table('ngn_brands')->count(),
                'categories' => DB::table('ngn_categories')->count(),
            ];

            DB::table('ngn_stock_movements')->delete();
            DB::table('ngn_attributes')->delete();
            DB::table('ngn_product_images')->delete();
            DB::table('ngn_products')->update(['parent_product_id' => null]);
            DB::table('ngn_products')->delete();
            DB::table('ngn_models')->delete();
            DB::table('ngn_brands')->delete();
            DB::table('ngn_categories')->delete();

            ShopService::clearNavigationCache();

            return $counts;
        });
    }
}
