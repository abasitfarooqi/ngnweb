<?php

namespace App\Imports;

use App\Models\NgnCategory;
use App\Models\NgnProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class NgnPOSProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        Log::info('Processing Row:', $row);

        try {
            // Prepare SKU
            $sku = trim($row['sku'] ?? ''); // Ensure this matches the column name in the file

            // Use product_id if SKU is missing or duplicate
            if (empty($sku) || NgnProduct::where('sku', $sku)->exists()) {
                Log::warning('Using product_id as SKU due to missing/duplicate SKU.', ['row' => $row]);
                $sku = trim($row['product_id'] ?? ''); // Use product_id as fallback
            }

            // If product_id is also missing, skip the row
            if (empty($sku)) {
                Log::error('Both SKU and product_id are missing.', ['row' => $row]);

                return null; // Skip this row if both SKU and product_id are missing
            }

            // Default brand, category, and model IDs
            $defaultBrandId = DB::table('ngn_brands')->where('name', 'No-Brand-Specified')->value('id');
            $defaultCategoryId = DB::table('ngn_categories')->where('name', 'No-Category-Specified')->value('id');
            $defaultModelId = DB::table('ngn_models')->where('name', 'No-Model-Specified')->value('id');

            // Handle Category (use default if null)
            $categoryName = ucfirst(strtolower(trim($row['category'] ?? '')));
            $categoryId = $categoryName ?
                NgnCategory::firstOrCreate(['name' => $categoryName])->id :
                $defaultCategoryId;

            // Handle Brand and Model (use default)
            $brandId = $defaultBrandId;
            $modelId = $defaultModelId;

            // Prepare Product Data
            $productId = trim($row['product_id'] ?? '');
            $description = $row['name'] ?? 'Not specified';
            $extendedDescription = $row['extended_description'] ?? 'Not specified';
            $imageUrl = $row['image_url'] ?? 'Not specified';
            $colour = 'Not specified';

            $normalPrice = $this->parseFloat($row['price'] ?? 0);
            $posPrice = $this->parseFloat($row['price'] ?? 0);
            $posVat = $this->parseFloat($row['vat'] ?? 0);
            $variation = $this->combineOptions(
                $row['option1_name'] ?? null,
                $row['option1_value'] ?? null,
                $row['option2_name'] ?? null,
                $row['option2_value'] ?? null,
                $row['option3_name'] ?? null,
                $row['option3_value'] ?? null
            );
            $ean = trim($row['barcode'] ?? '');
            $stock = (int) ($row['in_stock'] ?? 0);
            $vatable = $this->isVatable($row['vat'] ?? 0.00);
            $dead = strtolower(trim($row['dead'] ?? '')) === 'yes';

            // Handle pos_variant_id and pos_product_id
            $pos_variant_id = trim($row['variant_id'] ?? '');
            $pos_product_id = $productId ?: '';

            // Check for stock, set to 0 if less than 1
            $posStock = ($stock <= 0 || $stock < 1) ? 0 : $stock;

            // Check for pos_price, set to 0 if less than 1
            $posPrice = ($posPrice <= 0 || $posPrice < 1) ? 0 : $posPrice;

            // Create or Update Product
            $product = NgnProduct::updateOrCreate(
                ['sku' => $sku], // Find product by SKU
                [
                    'name' => trim($row['name'] ?? ''),
                    'description' => $description,
                    'extended_description' => $extendedDescription,
                    'image_url' => $imageUrl,
                    'variation' => $variation ?: '',
                    'colour' => $colour,
                    'brand_id' => $brandId,
                    'category_id' => $categoryId,
                    'model_id' => $modelId,
                    'ean' => $ean,
                    'pos_variant_id' => $pos_variant_id,
                    'pos_product_id' => $pos_product_id,
                    'normal_price' => $normalPrice,
                    'pos_price' => $posPrice,
                    'pos_vat' => $posVat,
                    'global_stock' => $posStock,
                    'vatable' => $vatable,
                    'is_oxford' => false,
                    'dead' => $dead,
                ]
            );

            Log::info('Product created/updated:', ['sku' => $sku]);

            return $product;

        } catch (\Exception $e) {
            Log::error('Error processing row: '.$e->getMessage(), ['row' => $row]);

            return null;
        }
    }

    // Helper function to combine options
    private function combineOptions($option1Name, $option1Value, $option2Name, $option2Value, $option3Name, $option3Value)
    {
        $options = [];
        if ($option1Name && $option1Value) {
            $options[] = "{$option1Name}: {$option1Value}";
        }
        if ($option2Name && $option2Value) {
            $options[] = "{$option2Name}: {$option2Value}";
        }
        if ($option3Name && $option3Value) {
            $options[] = "{$option3Name}: {$option3Value}";
        }

        return ! empty($options) ? implode(', ', $options) : '';
    }

    // Helper function to determine VAT status
    private function isVatable($vat)
    {
        return $vat > 0;
    }

    // Parse float value from string
    protected function parseFloat($value)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?: 0;
    }
}
