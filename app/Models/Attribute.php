<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Attribute
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name
 * @property string|null $slug
 * @property string|null $description
 * @property string $type
 * @property bool $is_enabled
 * @property bool $is_searchable
 * @property bool $is_filterable
 * @property Collection|AttributeValue[] $attribute_values
 * @property Collection|Product[] $products
 */
class Attribute extends Model
{
    protected $table = 'attributes';

    protected $casts = [
        'is_enabled' => 'bool',
        'is_searchable' => 'bool',
        'is_filterable' => 'bool',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'is_enabled',
        'is_searchable',
        'is_filterable',
    ];

    public function attribute_values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attributes')
            ->withPivot('id', 'stock_id');
    }
}
