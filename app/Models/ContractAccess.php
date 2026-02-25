<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class ContractAccess extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'contract_access';

    protected $fillable = [
        'customer_id',
        'passcode',
        'expires_at',
        'application_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(FinanceApplication::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
