<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Oxford extends Model
{
    protected $fillable = [
        'sku',
        'description',
        'ean',
        'price',
        'vat_price',
        'stock',
        'vatable',
        'obsolete',
        'dead',
        'brand',
        'extended_description',
        'variation',
        'super_product_name',
        'colour',
        'image_url',
        'category_id',
        'category',
        'model',
    ];

    public function getBuyableIdentifier($options = null)
    {
        return $this->id;
    }

    public function getBuyableDescription($options = null)
    {
        return $this->name;
    }

    public function getBuyablePrice($options = null)
    {
        return $this->price;
    }

    public function getBuyableWeight($options = null)
    {
        return $this->weight;
    }
}
