<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class ClubMemberSpendingPayment extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'club_member_spending_payments';

    protected $fillable = [
        'club_member_id',
        'spending_id',
        'date',
        'received_total',
        'pos_invoice',
        'user_id',
        'branch_id',
        'note',
    ];

    protected $casts = [
        'date' => 'datetime',
        'received_total' => 'decimal:2',
    ];

    public function clubMember()
    {
        return $this->belongsTo(ClubMember::class);
    }

    public function spending()
    {
        return $this->belongsTo(ClubMemberSpending::class, 'spending_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
