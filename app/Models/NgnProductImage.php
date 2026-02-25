<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Backpack\CRUD\app\Models\Traits\SpatieTranslatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class NgnProductImage extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;
    // use HasTranslations;

    // public $translatable = ['image_url']; // Specify which fields are translatable

    protected $fillable = [
        'product_id',
        'sku',
        'image_url',
    ];

    public function product()
    {
        return $this->belongsTo(NgnProduct::class, 'product_id');
    }

    // public function translationEnabled()
    // {
    //     // Define functionality if applicable

    //     return true; // Example return
    // }

}
