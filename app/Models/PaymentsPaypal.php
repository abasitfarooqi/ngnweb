<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentsPaypal extends Model
{
    use HasFactory;

    protected $table = 'payments_paypal';

    protected $fillable = [
        'customer_id',
        'order_id',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payer_email',
        'payer_name',
        'payer_id',
        'paypal_fee',
        'net_amount',
        'payment_response',
        'response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function webhookEvents(): HasMany
    {
        return $this->hasMany(PaypalWebhookEvent::class, 'payment_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerAuth::class, 'customer_id', 'id');
    }
}
