<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class EmployeeSchedule extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = ['user_id', 'off_day'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getOffDayAttribute($value)
    {
        return date('d/m/Y', strtotime($value));
    }

    public function setOffDayAttribute($value)
    {
        $this->attributes['off_day'] = date('Y-m-d', strtotime($value));
    }

    public function getOffDayFormattedAttribute()
    {
        return date('d/m/Y', strtotime($this->off_day));
    }

    public function setOffDayFormattedAttribute($value)
    {
        $this->attributes['off_day'] = date('Y-m-d', strtotime($value));
    }

    public function getOffDayDMmmYyyyAttribute()
    {
        return Carbon::parse($this->attributes['off_day'])->format('d M Y');
    }

    public function setOffDayDMmmYyyyAttribute($value)
    {
        $this->attributes['off_day'] = Carbon::createFromFormat('d M Y', $value)->format('Y-m-d');
    }

    public function getDayOfWeekAttribute()
    {
        return Carbon::parse($this->attributes['off_day'])->format('l');
    }
}
