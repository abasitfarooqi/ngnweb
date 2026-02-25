<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class PcnTolRequest extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'pcn_tol_requests';

    protected $guarded = ['id'];

    protected $fillable = [
        'update_id',
        'request_date',
        'status',
        'letter_sent_at',
        'note',
        'user_id',
        'pcn_case_id',
        'full_path',
    ];

    public function pcnCaseUpdate()
    {
        return $this->belongsTo(PcnCaseUpdate::class, 'update_id');
    }

    public function pcnCase()
    {
        return $this->belongsTo(PcnCase::class, 'pcn_case_id');
    }

    public function getPcnUpdateDisplayAttribute()
    {
        if ($this->pcnCaseUpdate && $this->pcnCaseUpdate->pcnCase) {
            $pcnNumber = $this->pcnCaseUpdate->pcnCase->pcn_number;
            $updateId = $this->pcnCaseUpdate->id;
            $customer = optional($this->pcnCaseUpdate->pcnCase->customer)->full_name;

            return "$pcnNumber | Update #$updateId | $customer";
        }

        return '';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function generateTolPdfButton($crud = false)
    {
        $url = backpack_url('pcn-tol-request/'.$this->id.'/generate-tol-pdf');

        return '<a class="btn btn-sm btn-link" href="'.$url.'"><i class="la la-file-pdf"></i> Generate PDF</a>';
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // If update_id is present, always sync pcn_case_id
            if ($model->update_id) {
                $update = PcnCaseUpdate::find($model->update_id);
                if ($update) {
                    $model->pcn_case_id = $update->case_id;
                }
            }
        });
    }
}
