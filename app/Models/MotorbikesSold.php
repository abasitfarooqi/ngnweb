<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class MotorbikesSold extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'motorbikes_sold';

    protected $fillable = [
        'listing_id',
        'customer_name',
        'phone_number',
        'sold_price',
        'address',
        'note',
    ];

    public function motorbikeSale()
    {
        return $this->belongsTo(MotorbikesSale::class, 'listing_id');
    }
}
