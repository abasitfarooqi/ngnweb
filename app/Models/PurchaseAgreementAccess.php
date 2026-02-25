<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class PurchaseAgreementAccess extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'purchase_agreement_accesses';

    protected $guarded = ['id'];

    protected $fillable = [
        'passcode',
        'expires_at',
        'purchase_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected $dates = [
        'expires_at',
    ];

    public function seller()
    {
        return $this->belongsTo(PurchaseUsedVehicle::class, 'purchase_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
