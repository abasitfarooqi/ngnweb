<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class CompanyVehicle extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'company_vehicles';

    protected $fillable = [
        'custodian',
        'motorbike_id',
    ];

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class);
    }
}
