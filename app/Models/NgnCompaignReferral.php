<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NgnCompaignReferral extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ngn_campaign_referrals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'ngn_campaign_id',
        'referrer_club_member_id',
        'referred_full_name',
        'referred_phone',
        'referred_reg_number',
        'referral_code',
        'validated',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'validated' => 'boolean',
    ];

    /**
     * Get the campaign that owns this referral
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(NgnCompaign::class, 'ngn_campaign_id');
    }

    /**
     * Get the club member who made this referral
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(ClubMember::class, 'referrer_club_member_id');
    }
}
