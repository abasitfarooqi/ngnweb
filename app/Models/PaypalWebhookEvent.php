<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaypalWebhookEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'event_type',
        'resource',
        'payload',
        'transmission_id',
        'transmission_time',
        'transmission_sig',
        'auth_algo',
        'cert_url',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(PaymentsPaypal::class, 'payment_id');
    }
}
