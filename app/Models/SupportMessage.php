<?php

namespace App\Models;

use App\Events\SupportMessageSent;
use App\Notifications\SupportConversationStartedNotification;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SupportMessage extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_customer_auth_id',
        'sender_user_id',
        'body',
        'meta',
        'read_at_customer',
        'read_at_staff',
    ];

    protected $casts = [
        'meta' => 'array',
        'read_at_customer' => 'datetime',
        'read_at_staff' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (self $message): void {
            $message->loadMissing('conversation');

            $conversation = $message->conversation;
            if ($conversation) {
                $conversation->last_message_at = now();
                if ($message->sender_type === 'customer' && $conversation->first_customer_message_at === null) {
                    $conversation->first_customer_message_at = now();
                }
                if ($message->sender_type === 'staff' && ! $conversation->assigned_backpack_user_id && $message->sender_user_id) {
                    $conversation->assigned_backpack_user_id = $message->sender_user_id;
                }
                if ($message->sender_type === 'customer') {
                    $conversation->status = 'waiting_for_staff';
                }
                if (
                    $message->sender_type === 'staff'
                    && ! in_array((string) $conversation->status, ['resolved', 'closed'], true)
                ) {
                    $conversation->status = 'waiting_for_customer';
                }
                $conversation->save();
            }

            event(new SupportMessageSent($message->fresh(['conversation', 'senderCustomerAuth', 'senderUser', 'attachments'])));

            if ($message->sender_type === 'customer' && $conversation && Schema::hasTable('notifications')) {
                $customerMsgCount = self::query()
                    ->where('conversation_id', $conversation->id)
                    ->where('sender_type', 'customer')
                    ->count();

                if ($customerMsgCount === 1) {
                    try {
                        User::query()
                            ->where('is_admin', true)
                            ->each(function (User $user) use ($conversation): void {
                                $user->notify(new SupportConversationStartedNotification($conversation));
                            });
                    } catch (Throwable) {
                        // Never block chat delivery if notification storage/mailer fails.
                    }
                }
            }
        });
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(SupportConversation::class, 'conversation_id');
    }

    public function senderCustomerAuth(): BelongsTo
    {
        return $this->belongsTo(CustomerAuth::class, 'sender_customer_auth_id');
    }

    public function senderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SupportAttachment::class, 'message_id');
    }
}
