<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class NgnStockMovement extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ngn_stock_movements';

    protected $primaryKey = 'id';

    protected $fillable = [
        'branch_id',
        'transaction_date',
        'product_id',
        'in',
        'out',
        'transaction_type',
        'user_id',
        'ref_doc_no',
        'remarks',
    ];

    public function product()
    {
        return $this->belongsTo(NgnProduct::class, 'product_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
