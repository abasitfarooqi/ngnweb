<?php

namespace App\Models;

use App\Support\MotorbikeMediaStorage;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Motorcycle extends Model
{
    use CrudTrait, HasFactory, Searchable;

    protected $guarded = [];

    protected $fillable = [
        'availability',
        'sale_new_price',
        'make',
        'model',
        'year',
        'colour',
        'category',
        'description',
        'engine',
        'file_name',
        'file_path',
        'image_two',
        'image_three',
        'image_four',
        'video_path',
        'type',
    ];

    protected $dates = [
        'payment_due_date',
        'next_payment_date',
        'rental_start_date',
        'payment_date',
        'tax_due_date',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *  Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'registration' => $this->registration,
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'availability' => $this->availability,
            'rental_price' => $this->rental_price,
            'engine' => $this->engine,
        ];
    }

    protected static function booted()
    {
        static::saving(function (self $motorcycle): void {
            if (! $motorcycle->isDirty('file_path') || ! $motorcycle->file_path) {
                return;
            }

            $motorcycle->file_path = MotorbikeMediaStorage::promoteLocalToSpaces((string) $motorcycle->file_path);
        });
    }
}
