<?php

namespace App\Exports;

use App\Models\NgnProduct;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NgnPOSProductsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all products with their stock movements
     * and prepare the data for the export.
     */
    public function collection()
    {
        // Fetch all products from NgnProduct with necessary relationships
        return NgnProduct::with(['category', 'stockMovements'])->get();
    }

    /**
     * Define the headings for the Excel file
     */
    public function headings(): array
    {
        return [
            'ID',               // Product Name
            'Name',               // Product Name
            'Custom unit',        // Custom Unit (if applicable)
            'VAT',                // VAT
            'Option1 Name',       // Option1 Name
            'Option1 Value',      // Option1 Value
            'Option2 Name',       // Option2 Name
            'Option2 Value',      // Option2 Value
            'Option3 Name',       // Option3 Name
            'Option3 Value',      // Option3 Value
            'SKU',                // SKU
            'Price',              // Normal Price
            'Cost price',         // Cost Price
            'Barcode',            // EAN (Barcode)
            'In stock',           // Global Stock
            'Category',           // Category Name
            'Variant id',         // POS Variant ID
            'Product id',         // POS Product ID
            'Net Stock Catford',  // Net Stock at Catford (New)
            'Net Stock Tooting',  // Net Stock at Tooting (New)
        ];
    }

    /**
     * Map the product data to match the structure of the Excel columns
     */
    public function map($product): array
    {
        // Assuming variations are stored as a comma-separated string
        $variation = $this->splitVariation($product->variation);

        // Calculate net stock for branches
        $catfordStock = $product->stockMovements->where('branch_id', 1)->sum('in') - $product->stockMovements->where('branch_id', 1)->sum('out');
        $tootingStock = $product->stockMovements->where('branch_id', 2)->sum('in') - $product->stockMovements->where('branch_id', 2)->sum('out');

        return [
            $product->id,                             // DB ID
            $product->name,                             // Product Name
            $product->custom_unit ?? '',                // Custom Unit (or a default value)
            $product->pos_vat,                          // VAT
            $variation[0]['name'] ?? '',                // Option1 Name
            $variation[0]['value'] ?? '',               // Option1 Value
            $variation[1]['name'] ?? '',                // Option2 Name
            $variation[1]['value'] ?? '',               // Option2 Value
            $variation[2]['name'] ?? '',                // Option3 Name
            $variation[2]['value'] ?? '',               // Option3 Value
            $product->sku,                              // SKU
            $product->normal_price,                     // Normal Price
            $product->cost_price ?? 0,                  // Cost Price (if available)
            $product->ean,                              // Barcode (EAN)
            $product->global_stock,                     // In Stock (Global Stock)
            $product->category && $product->category->name !== 'No-Category-Specified' ? $product->category->name : null, // Category Name
            $product->pos_variant_id,                   // POS Variant ID
            $product->pos_product_id,                   // POS Product ID
            $catfordStock,                              // Net Stock Catford (New)
            $tootingStock,                              // Net Stock Tooting (New)
        ];
    }

    /**
     * Helper function to split the variation string into an array of option name/value pairs
     */
    private function splitVariation($variation)
    {
        // If the variation string is formatted like "Option1: Value1, Option2: Value2", parse it.
        $options = explode(',', $variation);
        $result = [];

        foreach ($options as $option) {
            $parts = explode(':', $option);
            $result[] = [
                'name' => trim($parts[0] ?? ''),
                'value' => trim($parts[1] ?? ''),
            ];
        }

        return $result;
    }
}
