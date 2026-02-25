<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InventoryHistory
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $stockable_type
 * @property int $stockable_id
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property int $quantity
 * @property int $old_quantity
 * @property string|null $event
 * @property string|null $description
 * @property int $inventory_id
 * @property int $user_id
 * @property Inventory $inventory
 * @property UsersOld $users_old
 */
class InventoryHistory extends Model
{
    protected $table = 'inventory_histories';

    protected $casts = [
        'stockable_id' => 'int',
        'reference_id' => 'int',
        'quantity' => 'int',
        'old_quantity' => 'int',
        'inventory_id' => 'int',
        'user_id' => 'int',
    ];

    protected $fillable = [
        'stockable_type',
        'stockable_id',
        'reference_type',
        'reference_id',
        'quantity',
        'old_quantity',
        'event',
        'description',
        'inventory_id',
        'user_id',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function users_old()
    {
        return $this->belongsTo(UsersOld::class, 'user_id');
    }
}
