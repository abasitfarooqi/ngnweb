<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class OxfordProducts extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'oxford_products';

    protected $fillable = [
        'sku',
        'description',
        'ean',
        'rrp_less_vat',
        'rrp_inc_vat',
        'stock',
        'catford_stock',
        'estimated_delivery',
        'image_file_name',
        'vatable',
        'obsolete',
        'dead',
        'category',
        'supplier',
        'supplier_code',
        'cost_price',
        'brand',
        'extended_description',
        'variation',
        'date_added',
        'super_product_name',
        'colour',
        'image_url',
        'model',
    ];

    protected $casts = [
        'date_added' => 'datetime',
        'vatable' => 'boolean',
        'obsolete' => 'boolean',
    ];
}
