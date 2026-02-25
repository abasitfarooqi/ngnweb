<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
    
class JudopayMitQueue extends Model
{
    use HasFactory;
    use CrudTrait;

    protected $table = 'judopay_mit_queues';

    protected $fillable = [
        'ngn_mit_queue_id',
        'judopay_payment_reference',
        'mit_fire_date',
        'retry',
        'fired',
        'authorized_by',
        'cleared',
        'cleared_at',
    ];

    protected $casts = [
        'mit_fire_date' => 'datetime',
        'retry' => 'integer',
        'fired' => 'boolean',
        'cleared' => 'boolean',
        'cleared_at' => 'datetime',
    ];

    public function ngnMitQueue(): BelongsTo
    {
        return $this->belongsTo(NgnMitQueue::class, 'ngn_mit_queue_id');
    }

    public function authorizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function canFire(): bool
    {
        return !$this->fired && $this->mit_fire_date <= now();
    }

    public function shouldRetry(): bool
    {
        return $this->retry < config('judopay.mit.max_retry_attempts', 2);
    }

    public function markAsFired(): void
    {
        $this->update(['fired' => true]);
    }

    public function incrementRetry(): void
    {
        $this->increment('retry');
    }

    public function resetRetry(): void
    {
        $this->update(['retry' => 0]);
    }

    public function canBeStopped(): bool
    {
        return !$this->fired && $this->mit_fire_date > now();
    }
}
