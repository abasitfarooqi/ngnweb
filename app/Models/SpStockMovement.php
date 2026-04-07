<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpStockMovement extends Model
{
    use CrudTrait;

    protected $table = 'sp_stock_movements';

    protected $fillable = [
        'sp_part_id',
        'branch_id',
        'transaction_date',
        'in',
        'out',
        'transaction_type',
        'user_id',
        'ref_doc_no',
        'remarks',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'in' => 'decimal:2',
        'out' => 'decimal:2',
    ];

    public function part(): BelongsTo
    {
        return $this->belongsTo(SpPart::class, 'sp_part_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
