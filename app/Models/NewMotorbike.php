<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class NewMotorbike extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'purchase_date',
        'VRM',
        'make',
        'model',
        'colour',
        'engine',
        'year',
        'VIM',
        'branch_id',
        'user_id',
        'status',
        'is_vrm',
        'is_migrated',
        'migrated_at',
    ];

    protected $primaryKey = 'id';

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
