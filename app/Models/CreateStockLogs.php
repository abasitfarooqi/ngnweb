<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class CreateStockLogs extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'description',
        'qty',
        'color',
        'picture',
        'branch_id',
        'user_id',
        'sku',
    ];

    protected $table = 'stock_logs';
}
