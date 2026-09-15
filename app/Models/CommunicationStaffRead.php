<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationStaffRead extends Model
{
    protected $fillable = ['communication_id', 'user_id', 'opened_at', 'dealt_at'];
    protected $casts = ['opened_at' => 'datetime', 'dealt_at' => 'datetime'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
