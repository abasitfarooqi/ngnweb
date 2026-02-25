<?php

namespace App\Imports;

use App\Models\NgnProduct;
use App\Models\NgnStockMovement;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockAdderUpdater implements ToModel, WithHeadingRow
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
            $catfordStock = (int) ($row['catford'] ?? 0);
            $tootingStock = (int) ($row['tooting'] ?? 0);
            $suttonStock = (int) ($row['sutton'] ?? 0); // Added Sutton stock
            $totalStock = $catfordStock + $tootingStock + $suttonStock;

            // Find the product by SKU
            $product = NgnProduct::where('sku', $sku)->first();

            if ($product) {
                // Update global stock
                $product->global_stock = $totalStock;
                $product->save();

                // Handle stock movements for each branch
                if ($catfordStock > 0) {
                    $this->updateOrCreateStockMovement($product, $this->branchIds['catford'], $catfordStock);
                }

                if ($tootingStock > 0) {
                    $this->updateOrCreateStockMovement($product, $this->branchIds['tooting'], $tootingStock);
                }

                if ($suttonStock > 0) {
                    $this->updateOrCreateStockMovement($product, $this->branchIds['sutton'], $suttonStock);
                }

                Log::info('Stock movement processed for product:', ['sku' => $sku]);
            } else {
                Log::warning('Product not found for SKU:', ['sku' => $sku]);
            }

        } catch (\Exception $e) {
            Log::error('Error processing row: '.$e->getMessage(), ['row' => $row]);

            return null;
        }
    }

    protected function updateOrCreateStockMovement(NgnProduct $product, int $branchId, float $stock)
    {
        $existingStockMovement = NgnStockMovement::where('product_id', $product->id)
            ->where('branch_id', $branchId)
            ->first();

        if ($existingStockMovement) {
            // Update existing stock movement
            $existingStockMovement->update([
                'in' => $existingStockMovement->in + $stock,
                'updated_at' => now(),
            ]);
            Log::info("Stock movement updated for product: {$product->name} at branch ID: {$branchId}");
        } else {
            // Create new stock movement
            NgnStockMovement::create([
                'product_id' => $product->id,
                'branch_id' => $branchId,
                'in' => $stock,
                'out' => 0,
                'transaction_type' => 'Opening Stock',
                'transaction_date' => now(),
                'user_id' => 93, // Adjust user ID as needed
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Log::info("Stock movement created for product: {$product->name} at branch ID: {$branchId}");
        }
    }
}
