<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductAttribute
 *
 * @property int $id
 * @property int $product_id
 * @property int $attribute_id
 * @property int $stock_id
 * @property Attribute $attribute
 * @property Product $product
 * @property Collection|AttributeValue[] $attribute_values
 */
class ProductAttribute extends Model
{
    protected $table = 'product_attributes';

    public $timestamps = false;

    protected $casts = [
        'product_id' => 'int',
        'attribute_id' => 'int',
        'stock_id' => 'int',
    ];

    protected $fillable = [
        'product_id',
        'attribute_id',
        'stock_id',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute_values()
    {
        return $this->belongsToMany(AttributeValue::class)
            ->withPivot('id', 'product_custom_value');
    }
}
