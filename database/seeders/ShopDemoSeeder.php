<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Brands
        $brands = [
            ['name' => 'Oxford', 'slug' => 'oxford', 'is_ecommerce' => true, 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Abus', 'slug' => 'abus', 'is_ecommerce' => true, 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Shark', 'slug' => 'shark', 'is_ecommerce' => true, 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Bell', 'slug' => 'bell', 'is_ecommerce' => true, 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Alpinestars', 'slug' => 'alpinestars', 'is_ecommerce' => true, 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Akrapovic', 'slug' => 'akrapovic', 'is_ecommerce' => true, 'is_active' => true, 'sort_order' => 6],
        ];

        foreach ($brands as $brand) {
            DB::table('ngn_brands')->updateOrInsert(['slug' => $brand['slug']], array_merge($brand, [
                'created_at' => now(), 'updated_at' => now(),
            ]));
        }

        // Categories
        $categories = [
            ['name' => 'Helmets', 'slug' => 'helmets', 'is_ecommerce' => true, 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Accessories', 'slug' => 'accessories', 'is_ecommerce' => true, 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Spare Parts', 'slug' => 'spare-parts', 'is_ecommerce' => true, 'is_active' => true, 'sort_order' => 3],
            ['name' => 'GPS Trackers', 'slug' => 'gps-trackers', 'is_ecommerce' => true, 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Locks & Security', 'slug' => 'locks-security', 'is_ecommerce' => true, 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Clothing & Gear', 'slug' => 'clothing-gear', 'is_ecommerce' => true, 'is_active' => true, 'sort_order' => 6],
        ];

        foreach ($categories as $cat) {
            DB::table('ngn_categories')->updateOrInsert(['slug' => $cat['slug']], array_merge($cat, [
                'created_at' => now(), 'updated_at' => now(),
            ]));
        }

        $brandIds = DB::table('ngn_brands')->pluck('id', 'slug');
        $categoryIds = DB::table('ngn_categories')->pluck('id', 'slug');

        $products = [
            // Helmets
            ['name' => 'Shark Race-R Pro GP Full Face Helmet', 'slug' => 'shark-race-r-pro-gp', 'brand_slug' => 'shark', 'cat_slug' => 'helmets', 'price' => 449.99, 'stock' => 5, 'description' => 'The Race-R Pro GP is Shark\'s top-of-the-range helmet, used by MotoGP riders. Carbon fibre shell, Pinlock 120 included.'],
            ['name' => 'Bell Qualifier Full Face Helmet', 'slug' => 'bell-qualifier-full-face', 'brand_slug' => 'bell', 'cat_slug' => 'helmets', 'price' => 149.99, 'stock' => 12, 'description' => 'Lightweight polycarbonate shell with an integrated speaker pockets and excellent ventilation.'],
            ['name' => 'Shark Skwal i3 Bluetooth Helmet', 'slug' => 'shark-skwal-i3', 'brand_slug' => 'shark', 'cat_slug' => 'helmets', 'price' => 329.99, 'stock' => 3, 'description' => 'Built-in LED lights and integrated Bluetooth for calls and music.'],

            // Accessories
            ['name' => 'Oxford Clamp Lock Chain', 'slug' => 'oxford-clamp-lock-chain', 'brand_slug' => 'oxford', 'cat_slug' => 'accessories', 'price' => 59.99, 'stock' => 20, 'description' => 'Heavy duty 1.5m chain with triple power clamp lock. 12mm links.'],
            ['name' => 'Oxford Aqua T-50 Tail Pack', 'slug' => 'oxford-aqua-t50', 'brand_slug' => 'oxford', 'cat_slug' => 'accessories', 'price' => 34.99, 'stock' => 15, 'description' => 'Waterproof 50L tail pack with reflective panels and shoulder strap.'],
            ['name' => 'Oxford Hot Grips Advanced Heating', 'slug' => 'oxford-hot-grips', 'brand_slug' => 'oxford', 'cat_slug' => 'accessories', 'price' => 79.99, 'stock' => 8, 'description' => 'Oxford 5-setting heated grips, universal fit. Includes full wiring harness.'],

            // GPS Trackers
            ['name' => 'Monimoto 7 GPS Motorcycle Tracker', 'slug' => 'monimoto-7-tracker', 'brand_slug' => 'oxford', 'cat_slug' => 'gps-trackers', 'price' => 119.99, 'stock' => 7, 'description' => 'Hidden GPS tracker with SIM card. Motion-triggered alerts direct to your phone.'],
            ['name' => 'Rewire Datatool S5 Tracker', 'slug' => 'datatool-s5', 'brand_slug' => 'oxford', 'cat_slug' => 'gps-trackers', 'price' => 189.99, 'stock' => 4, 'description' => 'Thatcham Category 6 approved. Live tracking with 12-month subscription included.'],

            // Locks
            ['name' => 'Abus Granit Detecto XPlus 8077 Disc Lock', 'slug' => 'abus-granit-disc-lock', 'brand_slug' => 'abus', 'cat_slug' => 'locks-security', 'price' => 89.99, 'stock' => 9, 'description' => 'Alarm disc lock with 100dB alarm. Hardened steel, 12mm bolt.'],
            ['name' => 'Oxford Boss Alarm Disc Lock', 'slug' => 'oxford-boss-alarm-lock', 'brand_slug' => 'oxford', 'cat_slug' => 'locks-security', 'price' => 49.99, 'stock' => 14, 'description' => 'Compact alarm disc lock with 100dB siren. Includes reminder cable.'],

            // Spare Parts
            ['name' => 'NGN Original Brake Pads — Front', 'slug' => 'ngn-brake-pads-front', 'brand_slug' => 'oxford', 'cat_slug' => 'spare-parts', 'price' => 24.99, 'stock' => 30, 'description' => 'OEM spec front brake pads suitable for most 125cc–600cc motorcycles.'],
            ['name' => 'NGK Iridium Spark Plug', 'slug' => 'ngk-iridium-spark-plug', 'brand_slug' => 'oxford', 'cat_slug' => 'spare-parts', 'price' => 14.99, 'stock' => 50, 'description' => 'Long-life iridium spark plug for improved performance and fuel economy.'],
            ['name' => 'Michelin Pilot Street 120/70 Tyre', 'slug' => 'michelin-pilot-street-120-70', 'brand_slug' => 'oxford', 'cat_slug' => 'spare-parts', 'price' => 89.99, 'stock' => 6, 'description' => 'High-performance street tyre for 125cc–300cc motorcycles. 120/70-17 front.'],

            // Clothing
            ['name' => 'Alpinestars Tech-Air Race Airbag System', 'slug' => 'alpinestars-tech-air', 'brand_slug' => 'alpinestars', 'cat_slug' => 'clothing-gear', 'price' => 699.99, 'stock' => 2, 'description' => 'Autonomous airbag system compatible with select Alpinestars jackets.'],
            ['name' => 'Oxford Spartan Short WP Textile Jacket', 'slug' => 'oxford-spartan-jacket', 'brand_slug' => 'oxford', 'cat_slug' => 'clothing-gear', 'price' => 199.99, 'stock' => 7, 'description' => 'Waterproof motorcycle jacket with CE Level 2 armour at shoulders and elbows.'],
        ];

        // Ensure a default model exists
        $defaultModelId = DB::table('ngn_models')->insertGetId([
            'name' => 'Universal',
            'slug' => 'universal',
            'is_ecommerce' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($products as $product) {
            $brandId = $brandIds[$product['brand_slug']] ?? null;
            $catId = $categoryIds[$product['cat_slug']] ?? null;

            DB::table('ngn_products')->updateOrInsert(['slug' => $product['slug']], [
                'name' => $product['name'],
                'slug' => $product['slug'],
                'brand_id' => $brandId,
                'category_id' => $catId,
                'model_id' => $defaultModelId,
                'normal_price' => $product['price'],
                'global_stock' => $product['stock'],
                'description' => $product['description'],
                'is_ecommerce' => true,
                'dead' => false,
                'sku' => strtoupper(Str::random(8)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Shop demo data seeded: '.count($brands).' brands, '.count($categories).' categories, '.count($products).' products.');
    }
}
