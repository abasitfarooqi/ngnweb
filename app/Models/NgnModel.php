<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class NgnModel extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ngn_models';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'image_url',
    ];

    public function products()
    {
        return $this->hasMany(NgnProduct::class, 'model_id');
    }
}
