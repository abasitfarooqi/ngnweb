<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SupportAttachment extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'message_id',
        'disk',
        'path',
        'original_name',
        'mime',
        'size',
        'uploaded_by_customer_auth_id',
        'uploaded_by_user_id',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(SupportMessage::class, 'message_id');
    }

    public function uploadedByCustomerAuth(): BelongsTo
    {
        return $this->belongsTo(CustomerAuth::class, 'uploaded_by_customer_auth_id');
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk ?: 'public')->url($this->path);
    }
}
