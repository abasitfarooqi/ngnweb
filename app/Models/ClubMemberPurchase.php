<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ClubMemberPurchase extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'club_member_purchases';

    protected $fillable = [
        'date',
        'club_member_id',
        'percent',
        'total',
        'discount',
        'is_redeemed',
        'redeem_amount',
        'user_id',
        'pos_invoice',
        'branch_id',
    ];

    // Add a unique constraint to the 'pos_invoice' field
    protected $unique = [
        'pos_invoice',
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

   
    protected static function booted()
    {
        static::deleting(function ($purchase) {
            DB::table('backup_club_member_purchases')->insert([
                'original_id'    => $purchase->id,
                'date'           => $purchase->date,
                'club_member_id' => $purchase->club_member_id,
                'percent'        => $purchase->percent,
                'total'          => $purchase->total,
                'discount'       => $purchase->discount,
                'is_redeemed'    => $purchase->is_redeemed,
                'redeem_amount'  => $purchase->redeem_amount,
                'pos_invoice'    => $purchase->pos_invoice,
                'branch_id'      => $purchase->branch_id,

                // Backpack logged-in user
                'user_id'        => Auth::id(),

                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        });
    }

}
