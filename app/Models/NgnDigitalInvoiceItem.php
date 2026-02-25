<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class NgnDigitalInvoiceItem extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ngn_digital_invoice_items';

    protected $primaryKey = 'id';

    protected $fillable = [
        'invoice_id',
        'item_name',
        'sku',
        'quantity',
        'price',
        'discount',
        'tax',
        'total',
        'notes',
    ];

    /**
     * Boot method to auto-calculate line total
     */

    /**
     * Belongs to parent invoice
     */
    public function invoice()
    {
        return $this->belongsTo(NgnDigitalInvoice::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $quantity = $item->quantity ?? 0;
            $price = $item->price ?? 0;
            $discount = $item->discount ?? 0;
            $tax = $item->tax ?? 0;

            $item->total = round(($quantity * $price) - $discount + $tax, 2);
        });
    }
}
