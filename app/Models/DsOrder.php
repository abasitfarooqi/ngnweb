<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class DsOrder extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ds_orders';

    protected $fillable = [
        'pick_up_datetime',
        'full_name',
        'phone',
        'address',
        'postcode',
        'note',
        'proceed',
    ];

    protected $casts = [
        'pick_up_datetime' => 'datetime',
        'full_name' => 'string',
        'phone' => 'string',
        'address' => 'string',
        'postcode' => 'string',
        'note' => 'string',
        'proceed' => 'boolean',
    ];

    public function dsOrderItems(): HasMany
    {
        return $this->hasMany(DsOrderItem::class, 'ds_order_id', 'id');
    }
}
