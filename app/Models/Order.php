<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Order
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string $number
 * @property int|null $price_amount
 * @property string $status
 * @property string $currency
 * @property int|null $shipping_total
 * @property string|null $shipping_method
 * @property string|null $notes
 * @property int|null $parent_order_id
 * @property int|null $payment_method_id
 * @property int|null $shipping_address_id
 * @property int $user_id
 * @property Order|null $order
 * @property PaymentMethod|null $payment_method
 * @property UserAddress|null $user_address
 * @property UsersOld $users_old
 * @property Collection|OrderItem[] $order_items
 * @property Collection|OrderRefund[] $order_refunds
 * @property Collection|OrderShipping[] $order_shippings
 * @property Collection|Order[] $orders
 * @property Collection|UsersGeolocationHistory[] $users_geolocation_histories
 */
class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';

    protected $casts = [
        'price_amount' => 'int',
        'shipping_total' => 'int',
        'parent_order_id' => 'int',
        'payment_method_id' => 'int',
        'shipping_address_id' => 'int',
        'user_id' => 'int',
    ];

    protected $fillable = [
        'number',
        'price_amount',
        'status',
        'currency',
        'shipping_total',
        'shipping_method',
        'notes',
        'parent_order_id',
        'payment_method_id',
        'shipping_address_id',
        'user_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'parent_order_id');
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function user_address()
    {
        return $this->belongsTo(UserAddress::class, 'shipping_address_id');
    }

    public function users_old()
    {
        return $this->belongsTo(UsersOld::class, 'user_id');
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function order_refunds()
    {
        return $this->hasMany(OrderRefund::class);
    }

    public function order_shippings()
    {
        return $this->hasMany(OrderShipping::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'parent_order_id');
    }

    public function users_geolocation_histories()
    {
        return $this->hasMany(UsersGeolocationHistory::class);
    }
}
