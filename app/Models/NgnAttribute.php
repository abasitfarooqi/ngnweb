<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NgnAttribute extends Model
{
    use HasFactory;

    protected $table = 'ngn_attributes';

    protected $primaryKey = ['product_id', 'attribute_key'];

    public $incrementing = false;

    protected $fillable = [
        'product_id',
        'attribute_key',
        'attribute_value',
        'slug',
        'stock_in_hand',
    ];

    public function product()
    {
        return $this->belongsTo(NgnProduct::class, 'product_id');
    }

    public function scopeWithStockInHand($query)
    {
        return $query->whereNotNull('stock_in_hand');
    }

    public function scopeWithoutStockInHand($query)
    {
        return $query->whereNull('stock_in_hand');
    }
}
