<?php

namespace App\Imports;

use App\Models\NgnProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class NgnOXProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        Log::info('Processing Row:', $row);

        try {

            $sku = trim($row['sku'] ?? '');

            if (empty($sku) || NgnProduct::where('sku', $sku)->exists()) {
                Log::warning('Using product_id as SKU due to missing/duplicate SKU.', ['row' => $row]);
                $sku = trim($row['id'] ?? '');
            }

            if (empty($sku)) {
                Log::error('Both SKU and ID are missing.', ['row' => $row]);

                return null;
            }

            // Fetch default IDs
            $defaultBrandId = $this->getDefaultId('ngn_brands', 'Others');
            $defaultCategoryId = $this->getDefaultId('ngn_categories', 'Others');
            $defaultModelId = $this->getDefaultId('ngn_models', 'Others');

            // // Check for variation
            // if (!empty(trim($row['variation'] ?? ''))) {
            //     Log::info('Processing variation for product.', ['sku' => $sku, 'variation' => $row['variation']]);
            //     // Handle variation-specific logic if needed, e.g., adjusting SKU
            //     $sku .= '-' . strtoupper(trim($row['variation']));
            // }

            // Prepare Product Data
            $productData = $this->mapRowToProductData($row, $sku, $defaultBrandId, $defaultModelId);

            // Check if product exists
            $product = NgnProduct::where('sku', $sku)->first();

            if ($product) {
                return $this->updateOrCreateProduct($product, $productData);
            }

            // Create a new product if it doesn't exist
            return $this->createNewProduct($productData);

        } catch (\Exception $e) {
            Log::error('Error processing row: '.$e->getMessage(), ['row' => $row]);

            return null;
        }
    }

    private function getDefaultId(string $table, string $defaultName)
    {
        return DB::table($table)->where('name', $defaultName)->value('id');
    }

    private function mapRowToProductData(array $row, string $sku, int $defaultBrandId, int $defaultModelId)
    {
        $name = trim($row['name'] ?? '');
        // $name = preg_replace('/[^A-Za-z0-9\s"]/', '', trim($row['name'] ?? ''));
        $description = preg_replace('/&#\d+;|•/', '', trim($row['description'] ?? 'Not specified'));

        $extended_description = preg_replace('/(?:&#\d+;|•|<[^>]*>)/', '', trim($row['extended_description'] ?? 'Not specified'));

        $img = $row['image_url'] ?? 'https://neguinhomotors.co.uk/assets/img/no-image.png';

        $colour = trim($row['colour'] ?? 'Not specified');

        $variation = trim($row['variation'] ?? 'Not specified');

        $brand = trim($row['brand'] ?? 'Not specified');
        $category = trim($row['category'] ?? 'Not specified');
        $model = trim($row['model'] ?? 'Not specified');

        $ean = trim($row['ean'] ?? '');

        $normal_price = $this->parseFloat($row['rrp_less_vat'] ?? 0);
        $pos_price = $this->parseFloat($row['rrp_less_vat'] ?? 0);

        // If the name is blank, use the description as the name
        if (empty($name)) {
            $name = $description;
        }

        // Generate slug from name
        $slug = Str::slug($name);

        // Prepare meta title and description
        $metaTitle = $name;
        $metaDescription = 'Buy '.strtolower($name).' | Explore our range of Jackets, helmets, gears, shoes, and accessories at Neguinho Motors Ltd.';

        return [
            'sku' => $sku,
            'name' => $name,
            'description' => $description,
            'extended_description' => $extended_description,
            'image_url' => $img,
            'colour' => $colour,
            'variation' => $variation,
            'brand_id' => $this->getBrandId($brand, $defaultBrandId),
            'category_id' => $this->getCategoryId($category),
            'model_id' => $this->getModelId($model, $defaultModelId),
            'ean' => $ean,
            'normal_price' => $normal_price,
            'pos_price' => $pos_price,
            'global_stock' => 0,
            'vatable' => $this->isVatable($row['vatable'] ?? 0.00),
            'dead' => $this->isDead($row['dead'] ?? 'no'),
            'slug' => $slug,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'is_oxford' => true,
        ];
    }

    private function updateOrCreateProduct(NgnProduct $product, array $productData)
    {
        if (! $product->is_oxford) {
            Log::info('Updating existing product as is_oxford is false:', ['sku' => $product->sku]);

            $product->update(array_merge($productData, ['is_oxford' => true]));

            return $product;
        }

        // Create a new SKU with -OXDPRO suffix if the product is already Oxford
        return $this->createNewOxfordProduct($product->sku, $productData);
    }

    private function createNewOxfordProduct(string $sku, array $productData)
    {
        $newSku = $sku;
        Log::info('Creating new product with modified SKU:', ['new_sku' => $newSku]);

        return NgnProduct::create(array_merge($productData, ['sku' => $newSku, 'is_oxford' => true]));
    }

    private function createNewProduct(array $productData)
    {
        Log::info('Creating new product:', ['sku' => $productData['sku']]);

        return NgnProduct::create(array_merge($productData, ['is_oxford' => true]));
    }

    private function isDead($dead)
    {
        return strtolower(trim($dead)) === 'yes';
    }

    private function getBrandId(string $brandName, int $defaultBrandId)
    {
        return DB::table('ngn_brands')->where('name', $brandName)->value('id') ?: $defaultBrandId;
    }

    private function getCategoryId(string $categoryName)
    {
        return DB::table('ngn_categories')->where('name', $categoryName)->value('id') ?: $this->getDefaultId('ngn_categories', 'Others');
    }

    private function getModelId(string $modelName, int $defaultModelId)
    {
        return DB::table('ngn_models')->where('name', $modelName)->value('id') ?: $defaultModelId;
    }

    private function isVatable($vat)
    {
        return $vat > 0;
    }

    protected function parseFloat($value)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?: 0;
    }
}
