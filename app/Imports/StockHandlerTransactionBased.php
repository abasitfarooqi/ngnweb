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
            $catfordStockIn = (int) ($row['catford_in'] ?? 0);
            $tootingStockIn = (int) ($row['tooting_in'] ?? 0);
            $suttonStockIn = (int) ($row['sutton_in'] ?? 0); // Added Sutton stock IN

            $catfordStockOut = (int) ($row['catford_out'] ?? 0);
            $tootingStockOut = (int) ($row['tooting_out'] ?? 0);
            $suttonStockOut = (int) ($row['sutton_out'] ?? 0); // Added Sutton stock OUT

            // Find the product by SKU
            $product = NgnProduct::where('sku', $sku)->first();

            if ($product) {
                // Fetch existing stock and log
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

                $globalStock = $existingCatfordStock + $existingTootingStock + $existingSuttonStock;
                Log::info("Product {$product->name} has Tooting: {$existingTootingStock}, Catford: {$existingCatfordStock}, Sutton: {$existingSuttonStock}, Global Stock: {$globalStock} existing in DB");

                // Calculate differences for IN
                $catfordDiffIn = $catfordStockIn - $existingCatfordStock;
                $tootingDiffIn = $tootingStockIn - $existingTootingStock;
                $suttonDiffIn = $suttonStockIn - $existingSuttonStock;

                // Calculate differences for OUT
                $catfordDiffOut = $catfordStockOut - $existingCatfordStock;
                $tootingDiffOut = $tootingStockOut - $existingTootingStock;
                $suttonDiffOut = $suttonStockOut - $existingSuttonStock;

                // Log differences before making any changes
                Log::info("Difference in DB and Excel for Catford: {$catfordDiffIn}, Tooting: {$tootingDiffIn}, Sutton: {$suttonDiffIn}, Global Stock: ".($catfordDiffIn + $tootingDiffIn + $suttonDiffIn));

                // Update IN transactions
                if ($catfordDiffIn > 0) {
                    $this->createStockMovement($product, $this->branchIds['catford'], $catfordDiffIn, 'IN');
                }

                if ($tootingDiffIn > 0) {
                    $this->createStockMovement($product, $this->branchIds['tooting'], $tootingDiffIn, 'IN');
                }

                if ($suttonDiffIn > 0) {
                    $this->createStockMovement($product, $this->branchIds['sutton'], $suttonDiffIn, 'IN');
                }

                // Update OUT transactions
                if ($catfordDiffOut > 0 && $existingCatfordStock >= $catfordDiffOut) {
                    $this->createStockMovement($product, $this->branchIds['catford'], $catfordDiffOut, 'OUT');
                }

                if ($tootingDiffOut > 0 && $existingTootingStock >= $tootingDiffOut) {
                    $this->createStockMovement($product, $this->branchIds['tooting'], $tootingDiffOut, 'OUT');
                }

                if ($suttonDiffOut > 0 && $existingSuttonStock >= $suttonDiffOut) {
                    $this->createStockMovement($product, $this->branchIds['sutton'], $suttonDiffOut, 'OUT');
                }

                // Update global stock after processing transactions
                $product->global_stock = $existingCatfordStock + $catfordDiffIn - $catfordDiffOut + $existingTootingStock + $tootingDiffIn - $tootingDiffOut + $existingSuttonStock + $suttonDiffIn - $suttonDiffOut;
                $product->save();

                Log::info("Product {$product->name} has Tooting: {$tootingStockIn}, Catford: {$catfordStockIn}, Sutton: {$suttonStockIn}, Global Stock: {$product->global_stock} now in DB");

            } else {
                Log::warning('Product not found for SKU:', ['sku' => $sku]);
            }

        } catch (\Exception $e) {
            Log::error('Error processing row: '.$e->getMessage(), ['row' => $row]);

            return null;
        }
    }

    protected function createStockMovement(NgnProduct $product, int $branchId, float $stock, string $type)
    {
        NgnStockMovement::create([
            'product_id' => $product->id,
            'branch_id' => $branchId,
            'in' => $type === 'IN' ? $stock : 0,
            'out' => $type === 'OUT' ? $stock : 0,
            'transaction_type' => $type === 'IN' ? 'Stock Received' : 'Stock Sold',
            'transaction_date' => now(),
            'user_id' => 93, // Adjust user ID as needed
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Log::info("Stock movement {$type} for product: {$product->name} at branch ID: {$branchId}");
    }
}
