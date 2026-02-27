<?php

namespace Database\Seeders;

use App\Models\NgnCategory;
use App\Models\NgnProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class NgnPOSProductsLatestSeeder extends Seeder
{
    public function run()
    {
        HeadingRowFormatter::default('none'); // Set the default heading row format

        $filePath = database_path('seeders/pos.xlsx');

        // Check if the file exists
        if (! File::exists($filePath)) {
            Log::error('File not found: '.$filePath);
            throw new \Exception('File not found: '.$filePath); // Stop execution if file is missing
        }

        // Import the data
        Excel::import(new NgnPOSProductsLatestImport, $filePath);
    }
}

class NgnPOSProductsLatestImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        Log::info('Processing Row:', $row);

        try {
            // Default brand, category, and model IDs
            $defaultBrandId = DB::table('ngn_brands')->where('name', 'No-Brand-Specified')->value('id');
            $defaultCategoryId = DB::table('ngn_categories')->where('name', 'No-Category-Specified')->value('id');
            $defaultModelId = DB::table('ngn_models')->where('name', 'No-Model-Specified')->value('id');

            // Handle Category (use default if null)
            $categoryName = trim($row['Category'] ?? '');
            $categoryId = $categoryName ?
                NgnCategory::firstOrCreate(['name' => $categoryName])->id :
                $defaultCategoryId;

            // Handle Brand and Model (use default)
            $brandId = $defaultBrandId;
            $modelId = $defaultModelId;

            // Prepare Product Data
            $sku = trim($row['SKU']);
            $productId = trim($row['Product id']);
            $description = $row['Name'] ?? 'Not specified';
            $extendedDescription = $row['Extended description'] ?? 'Not specified';
            $imageUrl = $row['Image URL'] ?? 'Not specified';
            $colour = 'Not specified';

            $normalPrice = $this->parseFloat($row['Price']);
            $posPrice = $this->parseFloat($row['Price']); // Assuming POS price is the same as normal price
            $posVat = $this->parseFloat($row['VAT']);
            $variation = $this->combineOptions(
                $row['Option1 Name'] ?? null,
                $row['Option1 Value'] ?? null,
                $row['Option2 Name'] ?? null,
                $row['Option2 Value'] ?? null,
                $row['Option3 Name'] ?? null,
                $row['Option3 Value'] ?? null
            );
            $ean = trim($row['Barcode'] ?? '');
            $stock = (int) ($row['In Stock'] ?? 0.00);
            $vatable = $this->isVatable($row['VAT'] ?? 0.00);
            $dead = strtolower(trim($row['Dead'] ?? '')) === 'yes';

            // Handle pos_variant_id and pos_product_id
            $pos_variant_id = trim($row['Variant id'] ?? '');
            $pos_product_id = $productId ?: '';

            // Determine SKU logic

            // if (empty($sku)) {
            //     // If SKU is null, use Product ID
            //     $sku = $productId;
            // } else {
            //     // Check for duplicate SKU and concatenate with Product ID if needed
            //     $existingProduct = NgnProduct::where('sku', $sku)->first();
            //     if ($existingProduct) {
            //         // If SKU exists, concatenate SKU with Product ID
            //         $sku = $sku . $productId;
            //     } else {
            //         // Even if SKU is not empty and doesn't exist, concatenate SKU with Product ID
            //         $sku = $sku . $productId;
            //     }
            // }

            // Create or Update Product
            $product = NgnProduct::updateOrCreate(
                ['sku' => $sku],
                [
                    'name' => trim($row['Name']) ?: '',
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
                    'global_stock' => $stock,
                    'vatable' => $vatable,
                    'is_oxford' => false,  // Adjust if applicable
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

    // // Set chunk size
    // public function chunkSize(): int
    // {
    //     return 1000; // Process 1000 rows at a time
    // }

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
