<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Calander extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'calendar';

    protected $fillable = [
        'title',
        'start',
        'end',
        'background_color',
        'text_color',
    ];
}
