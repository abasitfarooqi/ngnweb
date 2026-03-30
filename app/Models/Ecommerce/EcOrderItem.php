<?php

namespace App\Models\Ecommerce;

use App\Models\NgnProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcOrderItem extends Model
{
    protected $table = 'ec_order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'item_type',
        'product_name',
        'sku',
        'part_number',
        'sp_part_id',
        'sp_assembly_id',
        'quantity',
        'unit_price',
        'total_price',
        'discount',
        'tax',
        'line_total',
        'source_meta',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'sp_part_id' => 'integer',
        'sp_assembly_id' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'line_total' => 'decimal:2',
        'source_meta' => 'array',
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(EcOrder::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(NgnProduct::class);
    }
}
