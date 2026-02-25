<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class ClubMemberRedeem extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'club_member_redeem';

    protected $fillable = [
        'club_member_id',
        'date',
        'redeem_total',
        'pos_invoice',
        'note',
        'user_id',
        'branch_id',
    ];

    public function clubMember()
    {
        return $this->belongsTo(ClubMember::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clubMemberRedeem()
    {
        return $this->hasMany(ClubMemberRedeem::class);
    }

    public function clubMemberPurchase()
    {
        return $this->belongsTo(ClubMemberPurchase::class);
    }
}
