<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Subscription
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $stripe_id
 * @property string $stripe_status
 * @property string|null $stripe_plan
 * @property int|null $quantity
 * @property Carbon|null $trial_ends_at
 * @property Carbon|null $ends_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $casts = [
        'user_id' => 'int',
        'quantity' => 'int',
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'stripe_id',
        'stripe_status',
        'stripe_plan',
        'quantity',
        'trial_ends_at',
        'ends_at',
    ];
}
