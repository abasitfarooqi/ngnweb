<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class ClubMember extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'club_members';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'is_active',
        'tc_agreed',
        'passkey',
        'email_sent',
        'make',
        'model',
        'year',
        'vrm',
        'is_partner',
        'ngn_partner_id',
        'user_id',
    ];

    public function partner()
    {
        return $this->belongsTo(NgnPartner::class, 'ngn_partner_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function purchases()
    {
        return $this->hasMany(ClubMemberPurchase::class);
    }

    public function getDetailAttribute()
    {
        return $this->full_name.' | '.$this->phone.' | '.$this->email;
    }

    public function redemptions()
    {
        return $this->hasMany(ClubMemberRedeem::class);
    }

    public function spendings()
    {
        return $this->hasMany(ClubMemberSpending::class);
    }

    public function sessions()
    {
        return $this->hasMany(UserSession::class);
    }

    public function segment()
    {
        return $this->hasOne(UserSegment::class);
    }

    public function feedback()
    {
        return $this->hasMany(UserFeedback::class);
    }

    public function getTotalPurchasesAttribute()
    {
        return $this->purchases->sum('total');
    }

    public function getTotalDiscountsAttribute()
    {
        return $this->purchases->sum('discount');
    }

    public function getTotalRedeemedAttribute()
    {
        return $this->redemptions->sum('redeem_total');
    }

    public function getAvailableRedeemableBalanceAttribute()
    {
        return round(
            $this->purchases()
                ->get()
                ->sum(function ($purchase) {
                    $discount = (float) ($purchase->discount ?? 0);
                    $redeemed = (float) ($purchase->redeem_amount ?? 0);
                    return max($discount - $redeemed, 0);
                }),
            2
        );
    }

    public function getRemainingRedeemableAttribute()
    {
        return $this->getTotalDiscountsAttribute() - $this->getTotalRedeemedAttribute();
    }

    public function getTotalSpendingAttribute()
    {
        return $this->spendings()->sum('total');
    }

    public function getTotalUnpaidSpendingAttribute()
    {
        return $this->spendings()->get()->sum(function ($spending) {
            return round($spending->total - ($spending->paid_amount ?? 0), 2);
        });
    }
}
