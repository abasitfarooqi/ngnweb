<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BikeModel extends Model
{
    use HasFactory;

    protected $table = 'bike_models';

    protected $fillable = ['brand_name_id', 'model'];

    public function brandName()
    {
        return $this->belongsTo(Make::class, 'brand_name_id');
    }
}
