<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentingTransaction extends Model
{
    use HasFactory;

    protected $table = 'renting_transactions';

    protected $fillable = [
        'transaction_date',
        'booking_id',
        'invoice_id',
        'transaction_type_id',
        'payment_method_id',
        'amount',
        'user_id',
        'notes',
    ];

    public function booking()
    {
        return $this->belongsTo(RentingBooking::class, 'booking_id');
    }

    public function invoice()
    {
        return $this->belongsTo(BookingInvoice::class, 'invoice_id');
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
