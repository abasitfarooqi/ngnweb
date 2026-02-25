<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UsersOld
 *
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $gender
 * @property string|null $phone_number
 * @property Carbon|null $birth_date
 * @property string $avatar_type
 * @property string|null $avatar_location
 * @property string|null $timezone
 * @property bool $opt_in
 * @property Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string $username
 * @property bool $is_admin
 * @property bool|null $is_client
 * @property string|null $nationality
 * @property string|null $driving_licence
 * @property string|null $street_address
 * @property string|null $street_address_plus
 * @property string|null $city
 * @property string|null $post_code
 * @property Collection|InventoryHistory[] $inventory_histories
 * @property Collection|OrderRefund[] $order_refunds
 * @property Collection|Order[] $orders
 * @property Collection|Post[] $posts
 * @property Collection|Sale[] $sales
 * @property Collection|UserAddress[] $user_addresses
 * @property Collection|UsersGeolocationHistory[] $users_geolocation_histories
 */
class UsersOld extends Model
{
    use SoftDeletes;

    protected $table = 'users-old';

    protected $casts = [
        'birth_date' => 'datetime',
        'opt_in' => 'bool',
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'is_admin' => 'bool',
        'is_client' => 'bool',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'remember_token',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'phone_number',
        'birth_date',
        'avatar_type',
        'avatar_location',
        'timezone',
        'opt_in',
        'last_login_at',
        'last_login_ip',
        'email',
        'email_verified_at',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
        'username',
        'is_admin',
        'is_client',
        'nationality',
        'driving_licence',
        'street_address',
        'street_address_plus',
        'city',
        'post_code',
    ];

    public function inventory_histories()
    {
        return $this->hasMany(InventoryHistory::class, 'user_id');
    }

    public function order_refunds()
    {
        return $this->hasMany(OrderRefund::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_id');
    }

    public function user_addresses()
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }

    public function users_geolocation_histories()
    {
        return $this->hasMany(UsersGeolocationHistory::class, 'user_id');
    }
}
