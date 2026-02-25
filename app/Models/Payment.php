<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Payment
 *
 * @property int $id
 * @property int|null $payment_id
 * @property int|null $user_id
 * @property int|null $motorcycle_id
 * @property string|null $registration
 * @property string|null $payment_type
 * @property float|null $rental_deposit
 * @property float|null $rental_price
 * @property string|null $description
 * @property float|null $received
 * @property float|null $outstanding
 * @property string|null $notes
 * @property Carbon|null $payment_due_date
 * @property int|null $payment_due_count
 * @property Carbon|null $payment_next_date
 * @property Carbon|null $payment_date
 * @property string|null $paid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $auth_user
 * @property string|null $deleted_by
 * @property string|null $deleted_at
 */
class Payment extends Model
{
    use SoftDeletes;

    protected $table = 'payments';

    protected $casts = [
        'payment_id' => 'int',
        'user_id' => 'int',
        'motorcycle_id' => 'int',
        'rental_deposit' => 'float',
        'rental_price' => 'float',
        'received' => 'float',
        'outstanding' => 'float',
        'payment_due_date' => 'datetime',
        'payment_due_count' => 'int',
        'payment_next_date' => 'datetime',
        'payment_date' => 'datetime',
    ];

    protected $fillable = [
        'payment_id',
        'user_id',
        'motorcycle_id',
        'registration',
        'payment_type',
        'rental_deposit',
        'rental_price',
        'description',
        'received',
        'outstanding',
        'notes',
        'payment_due_date',
        'payment_due_count',
        'payment_next_date',
        'payment_date',
        'paid',
        'auth_user',
        'deleted_by',
    ];
}
