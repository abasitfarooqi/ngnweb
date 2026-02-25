<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class MotorbikeCatB extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = ['motorbike_id', 'notes', 'dop', 'branch_id'];

    protected $table = 'motorbikes_cat_b';

    protected $casts = [
        'dop' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class, 'motorbike_id');
    }
}
