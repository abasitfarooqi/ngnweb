<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderRefund
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $refund_reason
 * @property string|null $refund_amount
 * @property string $status
 * @property string $notes
 * @property int $order_id
 * @property int|null $user_id
 * @property Order $order
 * @property UsersOld|null $users_old
 */
class OrderRefund extends Model
{
    protected $table = 'order_refunds';

    protected $casts = [
        'order_id' => 'int',
        'user_id' => 'int',
    ];

    protected $fillable = [
        'refund_reason',
        'refund_amount',
        'status',
        'notes',
        'order_id',
        'user_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function users_old()
    {
        return $this->belongsTo(UsersOld::class, 'user_id');
    }
}
