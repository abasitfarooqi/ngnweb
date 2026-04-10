<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class SupportConversation extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'uuid',
        'customer_auth_id',
        'service_booking_id',
        'assigned_backpack_user_id',
        'title',
        'topic',
        'status',
        'last_message_at',
        'first_customer_message_at',
        'external_ai_session_id',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'first_customer_message_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $conversation): void {
            if (! $conversation->uuid) {
                $conversation->uuid = (string) Str::uuid();
            }
            if (! $conversation->status) {
                $conversation->status = 'open';
            }
        });
    }

    public function customerAuth(): BelongsTo
    {
        return $this->belongsTo(CustomerAuth::class, 'customer_auth_id');
    }

    public function serviceBooking(): BelongsTo
    {
        return $this->belongsTo(ServiceBooking::class, 'service_booking_id');
    }

    public function assignedBackpackUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_backpack_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class, 'conversation_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(SupportMessage::class, 'conversation_id')->latestOfMany();
    }

    public function getConversationTypeAttribute(): string
    {
        return $this->service_booking_id ? 'enquiry' : 'general';
    }
}
