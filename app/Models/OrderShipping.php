<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderShipping
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon $shipped_at
 * @property Carbon $received_at
 * @property Carbon $returned_at
 * @property string|null $tracking_number
 * @property string|null $tracking_number_url
 * @property string|null $voucher
 * @property int $order_id
 * @property int|null $carrier_id
 * @property Carrier|null $carrier
 * @property Order $order
 */
class OrderShipping extends Model
{
    protected $table = 'order_shippings';

    protected $casts = [
        'shipped_at' => 'datetime',
        'received_at' => 'datetime',
        'returned_at' => 'datetime',
        'order_id' => 'int',
        'carrier_id' => 'int',
    ];

    protected $fillable = [
        'shipped_at',
        'received_at',
        'returned_at',
        'tracking_number',
        'tracking_number_url',
        'voucher',
        'order_id',
        'carrier_id',
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
