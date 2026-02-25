<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class RentalPayment extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    protected $guarded = [];

    protected $dates = [
        'payment_due_date',
        'payment_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class, 'id', 'rental_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function note(): HasMany
    {
        return $this->hasMany(Note::class, 'payment_id', 'id');
    }

    public function paymenttransaction(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'payment_transaction_id', 'id');
    }

    /**
     *  Get the indexable data array for the model.
     */
    public function toSearchableArray()
    {
        return [
            'registration' => $this->registration,
            'payment_type' => $this->type,
            'rental_price' => $this->amount,
        ];
    }
}
