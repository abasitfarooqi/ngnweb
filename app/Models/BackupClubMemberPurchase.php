<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupClubMemberPurchase extends Model
{
    protected $table = 'backup_club_member_purchases';

    protected $fillable = [
        'original_id',
        'date',
        'club_member_id',
        'percent',
        'total',
        'discount',
        'is_redeemed',
        'redeem_amount',
        'pos_invoice',
        'branch_id',
        'user_id',
    ];
}
