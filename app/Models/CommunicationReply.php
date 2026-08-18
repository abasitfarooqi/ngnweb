<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationReply extends Model
{
    protected $fillable = [
        'communication_id',
        'author_type',
        'author_id',
        'body',
    ];

    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }

    public function authorLabel(): string
    {
        if ($this->author_type === 'staff') {
            $user = User::query()->find($this->author_id);

            return $user?->full_name ?: $user?->name ?: 'Staff';
        }

        $customer = CustomerAuth::query()->find($this->author_id);

        return $customer?->customer?->first_name
            ? trim($customer->customer->first_name.' '.($customer->customer->last_name ?? ''))
            : ($customer?->email ?: 'Customer');
    }
}
