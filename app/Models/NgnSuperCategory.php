<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class NgnSuperCategory extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ngn_super_categories';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'is_active',
        'is_ecommerce',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function categories()
    {
        return $this->hasMany(NgnCategory::class);
    }

    public function products()
    {
        return $this->hasManyThrough(NgnProduct::class,
            NgnCategory::class,
            'ngn_super_category_id',
            'ngn_category_id',
            'id');
    }
}
