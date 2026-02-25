<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentingOtherChargesTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'charges_id',
        'transaction_type_id',
        'payment_method_id',
        'amount',
        'user_id',
        'notes',
    ];

    public function charges()
    {
        return $this->belongsTo(RentingOtherCharge::class, 'charges_id');
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
