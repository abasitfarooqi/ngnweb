<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeleteRequestOtp extends Model
{
    use HasFactory;

    protected $table = 'delete_request_otps';

    protected $fillable = [
        'purchase_id',
        'otp_code',
        'authorised_by',
        'expires_at',
        'is_used',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function purchase()
    {
        return $this->belongsTo(ClubMemberPurchase::class, 'purchase_id');
    }
}
