<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class ClubMemberSpending extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'club_member_spendings';

    protected $fillable = [
        'date',
        'club_member_id',
        'total',
        'paid_amount',
        'is_paid',
        'user_id',
        'pos_invoice',
        'branch_id',
    ];

    protected $casts = [
        'date' => 'datetime',
        'is_paid' => 'boolean',
        'paid_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function clubMember()
    {
        return $this->belongsTo(ClubMember::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(ClubMemberSpendingPayment::class, 'spending_id', 'id');
    }

    public function getUnpaidAmountAttribute()
    {
        return round($this->total - ($this->paid_amount ?? 0), 2);
    }

    public function getTotalAmountAttribute()
    {
        return $this->total;
    }
}
