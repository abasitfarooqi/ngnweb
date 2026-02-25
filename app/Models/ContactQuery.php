<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class ContactQuery extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'phone',
        'message',
        'is_dealt',
        'dealt_by_user_id',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'dealt_by_user_id');
    }
}
