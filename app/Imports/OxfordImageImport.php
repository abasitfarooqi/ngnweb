<?php

namespace App\Imports;

use App\Models\NgnProduct;
use App\Models\NgnProductImage;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OxfordImageImport implements ToCollection, WithHeadingRow
{
    private $firstChunkOnly;

    private $hasProcessedFirstChunk = false;

    public function __construct(bool $firstChunkOnly = false)
    {
        $this->firstChunkOnly = $firstChunkOnly;
    }

    public function collection(Collection $rows)
    {
        if ($this->firstChunkOnly && $this->hasProcessedFirstChunk) {
            return;
        }

        $client = new Client(['timeout' => 30]);

        foreach ($rows as $rowNumber => $row) {
            $sku = trim($row['sku'] ?? '');
            $image_urls = trim($row['images'] ?? '');

            if (empty($sku)) {
                Log::error('SKU is missing.', ['row' => $rowNumber]);
                $this->output("Row {$rowNumber}: SKU is missing.\n");

                continue;
            }

            $product = NgnProduct::where('sku', $sku)->first();

            if (! $product) {
                Log::error('Product not found for SKU: '.$sku, ['row' => $rowNumber]);
                $this->output("Row {$rowNumber}: No product found for SKU: {$sku}\n");

                continue;
            }

            // Split image URLs by semicolon and trim each URL
            $imageUrlsArray = array_filter(array_map('trim', explode(';', $image_urls)));

            foreach ($imageUrlsArray as $imageUrl) {
                if (! empty($imageUrl)) {
                    try {
                        // Validate the image URL
                        if (! filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                            Log::error('Invalid image URL.', ['url' => $imageUrl, 'sku' => $sku, 'row' => $rowNumber]);
                            $this->output("Row {$rowNumber}: Invalid image URL: {$imageUrl}\n");

                            continue;
                        }

                        // Save the entire URL in the database
                        $imageRecord = NgnProductImage::create([
                            'product_id' => $product->id,
                            'sku' => $sku,
                            'image_url' => $imageUrl, // Save the URL directly
                        ]);

                        if ($imageRecord) {
                            Log::info('Image record created for SKU: '.$sku, ['record_id' => $imageRecord->id]);
                            $this->output("Row {$rowNumber}: Saved image URL for SKU: {$sku}\n");
                        } else {
                            Log::error('Failed to create image record for SKU: '.$sku, ['row' => $rowNumber]);
                            $this->output("Row {$rowNumber}: Failed to create image record for SKU: {$sku}\n");
                        }

                    } catch (\Exception $e) {
                        Log::error('Error processing image.', [
                            'sku' => $sku,
                            'url' => $imageUrl,
                            'error' => $e->getMessage(),
                            'row' => $rowNumber,
                        ]);
                        $this->output("Row {$rowNumber}: Error processing image for SKU: {$sku} - {$e->getMessage()}\n");

                        continue;
                    }
                }
            }
        }

        if ($this->firstChunkOnly) {
            $this->hasProcessedFirstChunk = true;
        }
    }

    protected function output(string $message)
    {
        echo $message;
    }
}
