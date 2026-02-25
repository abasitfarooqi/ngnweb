<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class NgnMitQueue extends Model
{
    use HasFactory;
    use CrudTrait;

    protected $table = 'ngn_mit_queues';

    protected $fillable = [
        'subscribable_id',
        'invoice_number',
        'invoice_date',
        'mit_fire_date',
        'mit_attempt',
        'status',
        'cleared',
        'cleared_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'mit_fire_date' => 'datetime',
        'cleared_at' => 'datetime',
        'cleared' => 'boolean',
    ];

    public function subscribable(): BelongsTo
    {
        return $this->belongsTo(JudopaySubscription::class, 'subscribable_id');
    }

    public function clearedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cleared_by');
    }

    public function judopayMitQueues(): HasMany
    {
        return $this->hasMany(JudopayMitQueue::class);
    }

    public function canFire(): bool
    {
        return !$this->cleared && 
               $this->mit_attempt !== 'manual' && 
               $this->mit_fire_date <= now();
    }

    public function hasReachedMaxAttempts(): bool
    {
        return $this->mit_attempt === 'second';
    }

    public function markAsCleared(int $userId): void
    {
        $this->update([
            'cleared' => true,
            'cleared_at' => now(),
            'cleared_by' => $userId,
        ]);
    }

    public function incrementAttempt(): void
    {
        $attempts = ['not attempt', 'first', 'second', 'manual'];
        $currentIndex = array_search($this->mit_attempt, $attempts);
        
        if ($currentIndex !== false && $currentIndex < count($attempts) - 1) {
            $this->update(['mit_attempt' => $attempts[$currentIndex + 1]]);
        }
    }
}
