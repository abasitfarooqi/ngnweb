<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentingReferralLog extends Model
{
    public $timestamps = false;

    protected $table = 'renting_referral_logs';

    protected $fillable = [
        'referral_id',
        'action',
        'old_data',
        'new_data',
        'changed_by',
        'created_at',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
    ];

    public function referral(): BelongsTo
    {
        return $this->belongsTo(RentingReferral::class, 'referral_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
