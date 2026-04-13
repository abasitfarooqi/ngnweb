<?php

namespace App\Models;

use App\Services\ShopService;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class NgnBrand extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected static function booted(): void
    {
        static::saved(fn () => ShopService::clearNavigationCache());
        static::deleted(fn () => ShopService::clearNavigationCache());
    }

    protected $table = 'ngn_brands';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'image_url',
        'slug',
        'description',
        'is_ecommerce',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    public function products()
    {
        return $this->hasMany(NgnProduct::class, 'brand_id');
    }
}
