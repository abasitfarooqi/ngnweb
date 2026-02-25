<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class RentingPricing extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'motorbike_id',
        'user_id',
        'iscurrent',
        'weekly_price',
        'update_date',
        'minimum_deposit',
    ];

    protected $casts = [
        'iscurrent' => 'boolean',
        'update_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class);
    }

    public static function motorbikeNotPriced()
    {
        return Motorbike::whereNotIn('id', function ($query) {
            $query->select('motorbike_id')->from(with(new self)->getTable());
        })->get(['id', 'make', 'model', 'year', 'engine', 'color', 'fuel_type', 'reg_no']);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function motorbikeRegistrations()
    {
        return $this->hasMany(MotorbikeRegistration::class);
    }

    public function scopeCurrent($query)
    {
        return $query->where('iscurrent', true)->orderBy('motorbike_id', 'desc');
    }

    public function scopeOld($query)
    {
        return $query->where('iscurrent', false)->orderBy('update_date', 'desc');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->iscurrent = $model->iscurrent ?? false;
        });

        // static::creating(function ($model) {
        //     $model->iscurrent = $model->iscurrent ?? false;
        //     $existing = self::where('motorbike_id', $model->motorbike_id)
        //         ->where('iscurrent', $model->iscurrent)
        //         ->first();

        //     if ($existing) {
        //         throw new \Exception('The combination of motorbike_id and iscurrent must be unique.');
        //     }
        // });
    }
}
