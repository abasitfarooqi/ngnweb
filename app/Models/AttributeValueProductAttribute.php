<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AttributeValueProductAttribute
 *
 * @property int $id
 * @property int|null $attribute_value_id
 * @property int $product_attribute_id
 * @property string|null $product_custom_value
 * @property AttributeValue|null $attribute_value
 * @property ProductAttribute $product_attribute
 */
class AttributeValueProductAttribute extends Model
{
    protected $table = 'attribute_value_product_attribute';

    public $timestamps = false;

    protected $casts = [
        'attribute_value_id' => 'int',
        'product_attribute_id' => 'int',
    ];

    protected $fillable = [
        'attribute_value_id',
        'product_attribute_id',
        'product_custom_value',
    ];

    public function attribute_value()
    {
        return $this->belongsTo(AttributeValue::class);
    }

    public function product_attribute()
    {
        return $this->belongsTo(ProductAttribute::class);
    }
}
