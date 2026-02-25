<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotorbikeSaleLog extends Model
{
    protected $fillable = [
        'motorbike_id',
        'motorbikes_sale_id',
        'user_id',
        'username',
        'reg_no',
        'is_sold',
        'buyer_name',
        'buyer_phone',
        'buyer_email',
        'buyer_address',
    ];

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class, 'motorbike_id');
    }

    public function sale()
    {
        return $this->belongsTo(MotorbikesSale::class, 'motorbikes_sale_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
