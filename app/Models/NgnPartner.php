<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class NgnPartner extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ngn_partners';

    protected $fillable = [
        'companyname',
        'company_logo',
        'company_address',
        'company_number',
        'first_name',
        'last_name',
        'phone',
        'mobile',
        'email',
        'website',
        'fleet_size',
        'operating_since',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'fleet_size' => 'integer',
    ];

    public function clubMembers()
    {
        return $this->hasMany(ClubMember::class, 'ngn_partner_id');
    }
}
