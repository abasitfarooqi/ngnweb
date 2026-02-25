<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class DsOrderItem extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ds_order_items';

    protected $fillable = [
        'ds_order_id',
        'pickup_lat',
        'pickup_lon',
        'dropoff_lat',
        'dropoff_lon',
        'pickup_address',
        'pickup_postcode',
        'dropoff_address',
        'dropoff_postcode',
        'vrm',
        'moveable',
        'documents',
        'keys',
        'note',
        'distance',
    ];

    protected $casts = [
        'ds_order_id' => 'integer',
        'pickup_lat' => 'float',
        'pickup_lon' => 'float',
        'dropoff_lat' => 'float',
        'dropoff_lon' => 'float',
        'pickup_address' => 'string',
        'pickup_postcode' => 'string',
        'dropoff_address' => 'string',
        'dropoff_postcode' => 'string',
        'vrm' => 'string',
        'moveable' => 'boolean',
        'documents' => 'boolean',
        'keys' => 'boolean',
        'note' => 'string',
        'distance' => 'float',
    ];

    public function dsOrder(): BelongsTo
    {
        return $this->belongsTo(DsOrder::class, 'ds_order_id', 'id');
    }
}
