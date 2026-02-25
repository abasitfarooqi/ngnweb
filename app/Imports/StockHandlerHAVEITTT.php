<?php

namespace App\Imports;

use App\Models\NgnProduct;
use App\Models\NgnStockMovement;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockHandler implements ToModel, WithHeadingRow
{
    protected $branchIds = [
        'catford' => 1,
        'tooting' => 2,
        'sutton' => 3, // Added Sutton branch
    ];

    public function model(array $row)
    {
        Log::info('Processing Row:', $row);

        try {
            $sku = trim($row['sku'] ?? '');
            $catfordStockExcel = (int) ($row['catford'] ?? 0);
            $tootingStockExcel = (int) ($row['tooting'] ?? 0);
            $suttonStockExcel = (int) ($row['sutton'] ?? 0); // Added Sutton stock

            // Find the product by SKU
            $product = NgnProduct::where('sku', $sku)->first();

            if ($product) {
                // Check if any stock movements exist for this product
                $catfordTransactionsExist = NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['catford'])->exists();

                $tootingTransactionsExist = NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['tooting'])->exists();

                $suttonTransactionsExist = NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['sutton'])->exists(); // Check Sutton

                // If no transactions exist, create initial stock movements based on Excel data
                if (! $catfordTransactionsExist && $catfordStockExcel > 0) {
                    $this->createStockMovement($product, $this->branchIds['catford'], $catfordStockExcel, 'IN', 'Initial Stock');
                }

                if (! $tootingTransactionsExist && $tootingStockExcel > 0) {
                    $this->createStockMovement($product, $this->branchIds['tooting'], $tootingStockExcel, 'IN', 'Initial Stock');
                }

                if (! $suttonTransactionsExist && $suttonStockExcel > 0) {
                    $this->createStockMovement($product, $this->branchIds['sutton'], $suttonStockExcel, 'IN', 'Initial Stock');
                }

                // Get existing stock levels for each branch
                $existingCatfordStock = NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['catford'])
                    ->sum('in') - NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['catford'])
                    ->sum('out');

                $existingTootingStock = NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['tooting'])
                    ->sum('in') - NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['tooting'])
                    ->sum('out');

                $existingSuttonStock = NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['sutton'])
                    ->sum('in') - NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['sutton'])
                    ->sum('out');

                // Calculate differences for stock movements
                $catfordDiff = $catfordStockExcel - $existingCatfordStock;
                $tootingDiff = $tootingStockExcel - $existingTootingStock;
                $suttonDiff = $suttonStockExcel - $existingSuttonStock;

                // Catford: If positive, adjust by adding stock (IN); if negative, reduce by sale (OUT)
                if ($catfordDiff > 0) {
                    $this->createStockMovement($product, $this->branchIds['catford'], $catfordDiff, 'IN', 'Stock Adjustment');
                } elseif ($catfordDiff < 0) {
                    $this->createStockMovement($product, $this->branchIds['catford'], abs($catfordDiff), 'OUT', 'Shop Sale');
                }

                // Tooting: If positive, adjust by adding stock (IN); if negative, reduce by sale (OUT)
                if ($tootingDiff > 0) {
                    $this->createStockMovement($product, $this->branchIds['tooting'], $tootingDiff, 'IN', 'Stock Adjustment');
                } elseif ($tootingDiff < 0) {
                    $this->createStockMovement($product, $this->branchIds['tooting'], abs($tootingDiff), 'OUT', 'Shop Sale');
                }

                // Sutton: If positive, adjust by adding stock (IN); if negative, reduce by sale (OUT)
                if ($suttonDiff > 0) {
                    $this->createStockMovement($product, $this->branchIds['sutton'], $suttonDiff, 'IN', 'Stock Adjustment');
                } elseif ($suttonDiff < 0) {
                    $this->createStockMovement($product, $this->branchIds['sutton'], abs($suttonDiff), 'OUT', 'Shop Sale');
                }

                // Update the global stock for the product
                $product->global_stock = $existingCatfordStock + $catfordDiff + $existingTootingStock + $tootingDiff + $existingSuttonStock + $suttonDiff;
                $product->save();

                Log::info("Product {$product->name} - Updated Tooting: {$tootingStockExcel}, Catford: {$catfordStockExcel}, Sutton: {$suttonStockExcel}, Global Stock: {$product->global_stock}");
            } else {
                Log::warning('Product not found for SKU:', ['sku' => $sku]);
            }

        } catch (\Exception $e) {
            Log::error('Error processing row: '.$e->getMessage(), ['row' => $row]);
        }
    }

    protected function createStockMovement(NgnProduct $product, int $branchId, float $stock, string $type, string $transactionType)
    {
        NgnStockMovement::create([
            'product_id' => $product->id,
            'branch_id' => $branchId,
            'in' => $type === 'IN' ? $stock : 0,
            'out' => $type === 'OUT' ? $stock : 0,
            'transaction_type' => $transactionType,
            'transaction_date' => now(),
            'user_id' => 93, // Adjust user ID as needed
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Log::info("Stock movement {$type} for product: {$product->name} at branch ID: {$branchId} with transaction type: {$transactionType}");
    }
}
