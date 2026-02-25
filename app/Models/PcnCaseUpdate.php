<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class PcnCaseUpdate extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'pcn_case_updates';

    protected $fillable = [
        'case_id',
        'user_id',
        'update_date',
        'is_appealed',
        'is_paid_by_owner',
        'is_paid_by_keeper',
        'is_transferred',
        'additional_fee',
        'picture_url',
        'is_cancled',
        'note',
    ];

    public function pcnCase()
    {
        return $this->belongsTo(PcnCase::class, 'case_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function requestTolButton($crud = false)
    {
        $url = backpack_url('pcn-tol-request/create?update_id='.$this->id);

        return '<a class="btn btn-sm btn-link" href="'.$url.'"><i class="la la-file"></i> REQUEST TOL</a>';
    }

    public function getUpdateTypeAttribute($value)
    {
        return ucfirst($value);
    }

    public function getDisplayForTolAttribute()
    {
        if ($this->pcnCase) {
            return "{$this->pcnCase->pcn_number} | Update #{$this->id}";
        }

        return '';
    }

    public function tolRequests()
    {
        return $this->hasMany(PcnTolRequest::class, 'update_id');
    }

    // public function getAdditionalFeeAttribute($value)
    // {
    //     return '£' . number_format($value, 2);
    // }
}
