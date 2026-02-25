<?php

namespace App\Models;

use App\Services\FtpSyncService;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class MotorbikesSale extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'motorbikes_sale';

    protected $primaryKey = 'id';

    protected $fillable = [
        'condition',
        'motorbike_id',
        'is_sold',
        'buyer_name',
        'buyer_phone',
        'buyer_email',
        'buyer_address',
        'mileage',
        'price',
        'engine',
        'suspension',
        'brakes',
        'belt',
        'electrical',
        'tires',
        'note',
        'v5_available',
        'accessories',
        'user_id',
    ];

    public $timestamps = true;

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class, 'motorbike_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function saleLogs()
    {
        return $this->hasMany(MotorbikeSaleLog::class, 'motorbikes_sale_id');
    }

    public function latestSaleLog()
    {
        return $this->hasOne(MotorbikeSaleLog::class, 'motorbikes_sale_id')->latestOfMany();
    }

    public function motorbikeImage()
    {
        return $this->hasMany(MotorbikeImage::class, 'motorbike_id');
    }

    public function getMotorbikeImage()
    {
        return $this->motorbikeImage()->first();
    }

    public function getMotorbike()
    {
        return $this->motorbike()->first();
    }

    public function getMotorbikeId()
    {
        return $this->motorbike_id;
    }

    public function getCondition()
    {
        return $this->condition;
    }

    public function getMileage()
    {
        return $this->mileage;
    }

    public function getPrice()
    {
        return $this->price;
    }

    protected static function booted()
    {
        static::saved(function ($model) {
            $fields = ['image_one', 'image_two', 'image_three', 'image_four'];

            foreach ($fields as $field) {
                $newFilePath = $model->{$field};
                $originalFilePath = $model->getOriginal($field);

                // Only sync if the field has changed AND is not null
                if ($newFilePath && $newFilePath !== $originalFilePath) {
                    if (Storage::disk('used_motorbikes')->exists($newFilePath)) {
                        $fullLocalPath = Storage::disk('used_motorbikes')->path($newFilePath);

                        Log::info("[📦 Backpack Sync] Field changed '$field'. Local full path: $fullLocalPath");

                        $success = app(FtpSyncService::class)->uploadFile($fullLocalPath);

                        Log::info("[📦 Backpack Sync] Upload result for '$field': ".($success ? '✅ success' : '❌ failure'));
                    } else {
                        Log::warning("[📦 Backpack Sync] File for '$field' does not exist: $newFilePath");
                    }
                }
            }

            // 🔹 Track is_sold and buyer info (only when is_sold changed or when sold and buyer info changed)
            $buyerChanged = $model->wasChanged('buyer_name') || $model->wasChanged('buyer_phone') || $model->wasChanged('buyer_email') || $model->wasChanged('buyer_address');
            if ($model->wasChanged('is_sold') || ($model->is_sold && $buyerChanged)) {
                $user = backpack_user();

                \App\Models\MotorbikeSaleLog::updateOrCreate(
                    [
                        'motorbikes_sale_id' => $model->id,
                    ],
                    [
                        'motorbike_id' => $model->motorbike_id,
                        'user_id' => $user?->id,
                        'username' => $user?->name ?? 'System',
                        'reg_no' => $model->motorbike?->reg_no,
                        'is_sold' => $model->is_sold,
                        'buyer_name' => $model->is_sold ? $model->buyer_name : null,
                        'buyer_phone' => $model->is_sold ? $model->buyer_phone : null,
                        'buyer_email' => $model->is_sold ? $model->buyer_email : null,
                        'buyer_address' => $model->is_sold ? $model->buyer_address : null,
                    ]
                );

                Log::info("[📝 Sale Log] is_sold/buyer changed for Motorbike ID {$model->motorbike_id}, User: {$user?->name}, is_sold: {$model->is_sold}");
            }

        });
    }
}
