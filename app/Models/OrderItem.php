<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderItem
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $name
 * @property string|null $sku
 * @property string $product_type
 * @property int $product_id
 * @property int $quantity
 * @property int $unit_price_amount
 * @property int|null $order_id
 * @property Order|null $order
 */
class OrderItem extends Model
{
    protected $table = 'order_items';

    protected $casts = [
        'product_id' => 'int',
        'quantity' => 'int',
        'unit_price_amount' => 'int',
        'order_id' => 'int',
    ];

    protected $fillable = [
        'name',
        'sku',
        'product_type',
        'product_id',
        'quantity',
        'unit_price_amount',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
