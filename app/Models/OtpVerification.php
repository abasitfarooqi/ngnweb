<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    protected ?SmsMessage $matchedOtpSmsCache = null;
    protected bool $matchedOtpSmsResolved = false;

    public function clubMember()
    {
        return $this->belongsTo(ClubMember::class);
    }

    public function getClubMemberNameAttribute(): ?string
    {
        return $this->clubMember?->full_name;
    }

    public function getClubMemberPhoneAttribute(): ?string
    {
        return $this->clubMember?->phone;
    }

    public function getClubMemberEmailAttribute(): ?string
    {
        return $this->clubMember?->email;
    }

    public function getOtpPlainCodeAttribute(): ?string
    {
        $body = $this->otp_sms_body;
        if (! $body) {
            return null;
        }

        return preg_match('/is:\s*(\d{4})\b/i', $body, $matches) ? $matches[1] : null;
    }

    public function getOtpSmsStatusAttribute(): ?string
    {
        return $this->resolveMatchedOtpSms()?->status;
    }

    public function getOtpSmsToAttribute(): ?string
    {
        return $this->resolveMatchedOtpSms()?->to;
    }

    public function getOtpSmsBodyAttribute(): ?string
    {
        return $this->resolveMatchedOtpSms()?->body;
    }

    public function getOtpSmsCreatedAtAttribute(): ?Carbon
    {
        $sms = $this->resolveMatchedOtpSms();

        return $sms?->date_created ?? $sms?->created_at;
    }

    public function getOtpSmsErrorAttribute(): ?string
    {
        return $this->resolveMatchedOtpSms()?->error_message;
    }

    protected function resolveMatchedOtpSms(): ?SmsMessage
    {
        if ($this->matchedOtpSmsResolved) {
            return $this->matchedOtpSmsCache;
        }

        $this->matchedOtpSmsResolved = true;
        $phone = $this->normaliseUkPhoneNumber((string) ($this->clubMember?->phone ?? ''));
        if ($phone === '') {
            return $this->matchedOtpSmsCache = null;
        }

        $messagePrefix = 'Your OTP for redeeming credits at NGN Club is:%';
        $query = SmsMessage::query()
            ->where('to', $phone)
            ->where('body', 'like', $messagePrefix);

        if ($this->created_at) {
            $windowStart = $this->created_at->copy()->subMinutes(5);
            $windowEnd = $this->created_at->copy()->addMinutes(15);

            $windowed = (clone $query)
                ->where(function ($subQuery) use ($windowStart, $windowEnd) {
                    $subQuery->whereBetween('date_created', [$windowStart, $windowEnd])
                        ->orWhereBetween('created_at', [$windowStart, $windowEnd]);
                })
                ->orderByDesc('date_created')
                ->orderByDesc('created_at')
                ->first();

            if ($windowed) {
                return $this->matchedOtpSmsCache = $windowed;
            }
        }

        return $this->matchedOtpSmsCache = $query
            ->orderByDesc('date_created')
            ->orderByDesc('created_at')
            ->first();
    }

    protected function normaliseUkPhoneNumber(string $phoneNumber): string
    {
        $normalized = preg_replace('/[\s\-()\.]/', '', trim($phoneNumber));
        if (! $normalized) {
            return '';
        }

        if (str_starts_with($normalized, '07')) {
            return '+44'.substr($normalized, 1);
        }

        if (str_starts_with($normalized, '447')) {
            return '+'.$normalized;
        }

        if (str_starts_with($normalized, '+447')) {
            return $normalized;
        }

        return $normalized;
    }
}
