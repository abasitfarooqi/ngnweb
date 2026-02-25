<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UsersGeolocationHistory
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $ip_api
 * @property string|null $extreme_ip_lookup
 * @property int $user_id
 * @property int|null $order_id
 * @property Order|null $order
 * @property UsersOld $users_old
 */
class UsersGeolocationHistory extends Model
{
    use SoftDeletes;

    protected $table = 'users_geolocation_history';

    protected $casts = [
        'user_id' => 'int',
        'order_id' => 'int',
    ];

    protected $fillable = [
        'ip_api',
        'extreme_ip_lookup',
        'user_id',
        'order_id',
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
