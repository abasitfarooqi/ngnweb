<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\EncryptCast;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class JudopayMitPaymentSession extends Model
{
    use HasFactory;

    protected $table = 'judopay_mit_payment_sessions';

    protected $fillable = [
        'subscription_id',
        'user_id',
        'judopay_payment_reference',
        'amount',
        'order_reference',
        'description',
        'judopay_related_receipt_id',
        'card_token_used',
        'judopay_receipt_id',
        'judopay_response',
        'status',
        'status_score',
        'scheduled_for',
        'payment_completed_at',
        'attempt_no',
        'failure_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'scheduled_for' => 'datetime',
        'payment_completed_at' => 'datetime',
        'judopay_response' => 'array',
        'attempt_no' => 'integer',
        'card_token_used' => EncryptCast::class,
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(JudopaySubscription::class, 'subscription_id');
    }

    public function paymentSessionOutcomes(): MorphMany
    {
        return $this->morphMany(JudopayPaymentSessionOutcome::class, 'session');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
