<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class ApplicationItem extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'application_id',
        'motorbike_id',
        'user_id',
        'start_date',
        'due_date',
        'end_date',
        'is_posted',
        'weekly_instalment',
    ];

    public function application()
    {
        return $this->belongsTo(FinanceApplication::class, 'application_id');
    }

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
