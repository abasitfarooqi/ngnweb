<?php

namespace Database\Seeders;

use App\Models\NgnBrand;
use App\Models\NgnCategory;
use App\Models\NgnModel;
use App\Models\NgnProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class NgnProductsLatestSeeder extends Seeder
{
    use Importable;

    public function run()
    {
        HeadingRowFormatter::default('none'); // Set the default heading row format

        $filePath = database_path('seeders/op.xlsx');

        // Check if the file exists
        if (! File::exists($filePath)) {
            Log::error('File not found: '.$filePath);
            throw new \Exception('File not found: '.$filePath); // Throw an exception to stop seeder execution
        }

        // Import the data
        Excel::import(new NgnProductsLatestImport, $filePath);
    }
}

class NgnProductsLatestImport implements ToModel, WithHeadingRow
{
    use Importable;

    protected $categoryMappings = [
        '%JACKET%' => 'JACKET',
        '%FOOTWEAR%' => 'FOOTWEAR',
        '%GLOVES%' => 'GLOVES',
        '%PANTS%' => 'PANTS',
        '%CASUAL%' => 'CASUAL',
        '%LEGGINGS%' => 'LEGGINGS',
        '%JEANS%' => 'JEANS',
        '%RAINWEAR%' => 'RAINWEAR',
        '%SUIT%' => 'SUIT',
        '%HEADWEAR%' => 'HEADWEAR',
        '%LUGGAGE%' => 'LUGGAGE',
        '%SPARES%' => 'SPARES',
        '%HELMET%' => 'HELMET',
        '%ACCESSORIES%' => 'ACCESSORIES',
        '%LIGHTING%' => 'LIGHTING',
        '%WORKSHOP%' => 'WORKSHOP',
        '%MINT%' => 'MINT',
        '%HOTGRIPS%' => 'HOTGRIPS',
        '%LOCKS%' => 'LOCKS',
        '%TRANSPORT%' => 'TRANSPORT',
        '%CLOTHING%' => 'CLOTHING',
        '%INTERCOMS%' => 'INTERCOMS',
        '%CARE%' => 'CARE',
        '%LAYERS%' => 'LAYERS',
    ];

    public function model(array $row)
    {
        Log::info('Processing Row:', $row);

        try {

            // Handle Brand
            $brandName = trim($row['Brand']);
            if (empty($brandName)) {
                $brand = NgnBrand::find(1); // Use default Brand with id = 1
            } else {
                $brand = NgnBrand::firstOrCreate(['name' => $brandName]);
            }

            Log::info('Brand processed:', ['brand_id' => $brand->id, 'brand_name' => $brand->name]);

            // Handle Category
            $categoryName = trim($row['Category']);
            if (empty($categoryName)) {
                $category = NgnCategory::find(1); // Use default Category with id = 1
            } else {
                $category = $this->findOrCreateCategory($categoryName);
            }

            Log::info('Category processed:', ['category_id' => $category->id, 'category_name' => $category->name]);

            // Handle Model
            $modelName = trim($row['Model']);
            if (empty($modelName)) {
                $model = NgnModel::find(1); // Use default Model with id = 1
            } else {
                $model = NgnModel::firstOrCreate(['name' => $modelName]);
            }
            Log::info('Model processed:', ['model_id' => $model->id, 'model_name' => $model->name]);

            // Prepare Product Data
            $sku = trim($row['SKU']);
            $description = $row['Description'] ?? 'No Description Available';
            $extendedDescription = $row['Extended description'] ?? 'No Extended Description Available';
            $normalPrice = $this->parseFloat($row['RRP inc. VAT']);
            $posPrice = $this->parseFloat($row['RRP inc. VAT']);
            $posVat = $this->parseFloat($row['RRP less VAT']);
            $variation = $row['Variation'] ?? 'No Variation Specified';
            $color = $row['Colour'] ?? 'No Color Specified';
            $variantId = $row['VariantId'] ?? null;
            $productId = $row['ProductId'] ?? null;
            $ean = trim($row['EAN']);
            $stock = (int) ($row['Stock'] ?? 0);
            $vatable = strtolower(trim($row['Vatable'])) === 'yes';
            $dead = strtolower(trim($row['Dead'])) === 'yes';
            $imageUrl = trim($row['Image_URL']) ?? '';

            // Create or Update Product
            $product = NgnProduct::updateOrCreate(
                ['sku' => $sku],
                [
                    'name' => trim($row['Super Product Name']) ?: $description,
                    'description' => $description,
                    'extended_description' => $extendedDescription,
                    'image_url' => $imageUrl,
                    'variation' => $variation,
                    'brand_id' => $brand->id,
                    'category_id' => $category->id,
                    'model_id' => $model->id,
                    'colour' => $color,
                    'ean' => $ean,
                    'normal_price' => $normalPrice,
                    'pos_price' => $posPrice,
                    'pos_vat' => $posVat,
                    'global_stock' => DB::raw('global_stock'),
                    'vatable' => $vatable,
                    'is_oxford' => true,
                    'dead' => $dead,
                    'pos_variant_id' => $variantId,
                    'pos_product_id' => $productId,
                ]
            );

            Log::info('Product created/updated:', ['product' => $product]);

            return $product;

        } catch (\Exception $e) {
            Log::error('Error processing row: '.$e->getMessage(), ['row' => $row]);

            return null;
        }
    }

    protected function findOrCreateCategory($name)
    {
        // Attempt to find the category first
        $category = NgnCategory::where('name', $name)->first();

        if (! $category) {
            // If no match found, check the mapping
            foreach ($this->categoryMappings as $pattern => $mappedCategory) {
                if (stripos($name, $mappedCategory) !== false) {
                    return NgnCategory::firstOrCreate(['name' => $mappedCategory]);
                }
            }

            // If no mapping matched, create a new category
            $category = NgnCategory::firstOrCreate(['name' => $name]);
        }

        return $category;
    }

    protected function parseFloat($value)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?: 0;
    }
}
