<?php

namespace App\Models;

use App\Http\Controllers\Admin\PurchaseUsedVehicleCrudController;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class PurchaseUsedVehicle extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'purchase_used_vehicles';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $fillable = [
        'purchase_date',
        'full_name',
        'address',
        'postcode',
        'phone_number',
        'email',
        'make',
        'year',
        'colour',
        'fuel_type',
        'model',
        'reg_no',
        'current_mileage',
        'vin',
        'engine_number',
        'price',
        'deposit',
        'outstanding',
        'total_to_pay',
        'account_name',
        'sort_code',
        'account_number',
        'user_id',
    ];

    protected $dates = [
        'purchase_date',
    ];

    protected $casts = [
        'purchase_date' => 'datetime:Y-m-d',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            \App::make(PurchaseUsedVehicleCrudController::class)->postCreate($model);
        });
    }
}
