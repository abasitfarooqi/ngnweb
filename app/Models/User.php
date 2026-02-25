<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
// carbor
use Laravel\Scout\Searchable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Billable, HasApiTokens, HasFactory, Notifiable, Searchable;
    use CrudTrait;
    use HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'is_client',
        'employee_id',
        'role_id',
        'is_admin',
        'rating',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users');
    }

    public function purchaseRequestItems()
    {
        return $this->hasMany(PurchaseRequestItem::class, 'created_by');
    }

    // User Active Now
    public function UserOnline()
    {
        return Cache::has('user-is-online'.$this->id);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'user_id', 'id');
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class, 'user_id', 'id');
    }

    public function ipRestrictions()
    {
        return $this->hasMany(IpRestriction::class, 'user_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'user_id', 'id');
    }

    public function toSearchableArray()
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
        ];
    }

    public function getDetailAttribute()
    {
        return $this->first_name.' | '.$this->last_name.' | '.$this->email.' | '.$this->id;
    }

    public function getDealtByUserNameAttribute()
    {
        return $this->first_name.' '.$this->last_name.' | '.$this->email.' | '.$this->id;
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name.' '.$this->last_name);
    }
}
