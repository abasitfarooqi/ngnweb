<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class PurchaseRequest extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'purchase_requests';

    protected $fillable = [
        'date',
        'note',
        'created_by',
        'is_posted',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class, 'pr_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
