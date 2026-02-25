<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class ContractExtraItem extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = ['application_id', 'name', 'price', 'quantity'];

    // table
    protected $table = 'contract_extra_items';

    public function application()
    {
        return $this->belongsTo(FinanceApplication::class);
    }

    public function getTotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}
