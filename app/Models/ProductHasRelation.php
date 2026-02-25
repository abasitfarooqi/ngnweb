<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductHasRelation
 *
 * @property int|null $product_id
 * @property string $productable_type
 * @property int $productable_id
 * @property int $stock_id
 * @property Product|null $product
 */
class ProductHasRelation extends Model
{
    protected $table = 'product_has_relations';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'product_id' => 'int',
        'productable_id' => 'int',
        'stock_id' => 'int',
    ];

    protected $fillable = [
        'product_id',
        'productable_type',
        'productable_id',
        'stock_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
