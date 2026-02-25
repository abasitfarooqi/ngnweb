<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class MotChecker extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;
    // Specify which attributes should be mass-assignable

    protected $table = 'mot_checker';

    protected $fillable = [
        'vehicle_registration',
        'mot_due_date',
        'email',
    ];
}
