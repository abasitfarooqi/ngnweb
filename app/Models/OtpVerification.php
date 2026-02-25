<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    use HasFactory;

    protected $table = 'otp_verifications';

    protected $fillable = [
        'club_member_id',
        'otp_code',
        'expires_at',
        'is_used',
    ];

    public function clubMember()
    {
        return $this->belongsTo(ClubMember::class);
    }
}
