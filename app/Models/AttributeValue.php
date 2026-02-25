<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AttributeValue
 *
 * @property int $id
 * @property string $value
 * @property string $key
 * @property int|null $position
 * @property int $attribute_id
 * @property Attribute $attribute
 * @property Collection|ProductAttribute[] $product_attributes
 */
class AttributeValue extends Model
{
    protected $table = 'attribute_values';

    public $timestamps = false;

    protected $casts = [
        'position' => 'int',
        'attribute_id' => 'int',
    ];

    protected $fillable = [
        'value',
        'key',
        'position',
        'attribute_id',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function product_attributes()
    {
        return $this->belongsToMany(ProductAttribute::class)
            ->withPivot('id', 'product_custom_value');
    }
}
