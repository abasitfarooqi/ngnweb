<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class NgnSurveyResponse extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ngn_survey_responses';

    protected $fillable = [
        'survey_id',
        'customer_id',
        'club_member_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'is_contact_opt_in',
    ];

    public function survey()
    {
        return $this->belongsTo(NgnSurvey::class, 'survey_id');
    }

    public function answers()
    {
        return $this->hasMany(NgnSurveyAnswer::class, 'response_id');
    }
}
