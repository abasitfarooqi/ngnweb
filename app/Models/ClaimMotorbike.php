<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class ClaimMotorbike extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'claim_motorbikes';

    protected $fillable = [
        'motorbike_id',
        'branch_id',
        'notes',
        'case_date',
        'is_received',
        'received_date',
        'is_returned',
        'returned_date',
        'notes',
        'user_id',
        'fullname',
        'email',
        'phone',
    ];

    protected $rules = [
        'fullname' => 'required|string',
        'email' => 'required|string',
        'phone' => 'required|string',
        'user_id' => 'required|exists:users,id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
