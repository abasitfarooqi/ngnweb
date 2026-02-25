<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Sale
 *
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property string|null $brand_name
 * @property string|null $generic_name
 * @property string|null $category
 * @property float|null $orginal_price
 * @property float|null $sell_price
 * @property int|null $quantity
 * @property float|null $profit
 * @property float|null $total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Product $product
 * @property UsersOld $users_old
 */
class Sale extends Model
{
    protected $table = 'sales';

    protected $casts = [
        'user_id' => 'int',
        'product_id' => 'int',
        'orginal_price' => 'float',
        'sell_price' => 'float',
        'quantity' => 'int',
        'profit' => 'float',
        'total' => 'float',
    ];

    protected $fillable = [
        'user_id',
        'product_id',
        'brand_name',
        'generic_name',
        'category',
        'orginal_price',
        'sell_price',
        'quantity',
        'profit',
        'total',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function users_old()
    {
        return $this->belongsTo(UsersOld::class, 'user_id');
    }
}
