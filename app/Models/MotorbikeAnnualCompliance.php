<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class MotorbikeAnnualCompliance extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'motorbike_annual_compliance';

    protected $fillable = [
        'motorbike_id',
        'year',
        'mot_status',
        'road_tax_status',
        'insurance_status',
        'tax_due_date',
        'insurance_due_date',
        'mot_due_date',
    ];

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class);
    }

    // get motorbike_id using motorbike_annual_compliance_id
    public static function getMotorbikeId($motorbike_annual_compliance_id)
    {
        return MotorbikeAnnualCompliance::where('id', $motorbike_annual_compliance_id)->first()->motorbike_id;
    }

    public function getMotStatusAttribute()
    {
        $motDueDate = Carbon::parse($this->mot_due_date);
        $daysLeft = Carbon::now()->diffInDays($motDueDate, false);

        if ($daysLeft <= 10) {
            return '10 days left in MOT expires from now';
        } elseif ($daysLeft <= 30) {
            return '30 days left in MOT expires from now';
        } else {
            return 'MOT is valid';
        }
    }

    public function getTaxStatusAttribute()
    {
        $taxDueDate = Carbon::parse($this->tax_due_date);
        $daysLeft = Carbon::now()->diffInDays($taxDueDate, false);

        if ($daysLeft <= 10) {
            return '10 days left in tax expires from now';
        } elseif ($daysLeft <= 30) {
            return '30 days left in tax expires from now';
        } else {
            return 'Tax is valid';
        }
    }
}
