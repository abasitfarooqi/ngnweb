<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Carrier
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name
 * @property string|null $slug
 * @property string|null $logo
 * @property string|null $link_url
 * @property string|null $description
 * @property int $shipping_amount
 * @property bool $is_enabled
 * @property Collection|OrderShipping[] $order_shippings
 */
class Carrier extends Model
{
    protected $table = 'carriers';

    protected $casts = [
        'shipping_amount' => 'int',
        'is_enabled' => 'bool',
    ];

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'link_url',
        'description',
        'shipping_amount',
        'is_enabled',
    ];

    public function order_shippings()
    {
        return $this->hasMany(OrderShipping::class);
    }
}
