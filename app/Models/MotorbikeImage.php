<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MotorbikeImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['motorbike_id', 'image_path', 'alt_text'];

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class);
    }
}
