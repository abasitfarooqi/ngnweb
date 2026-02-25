<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class NgnSurveyQuestion extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ngn_survey_questions';

    protected $fillable = [
        'survey_id',
        'question_text',
        'question_type',
        'is_required',
        'order',
    ];

    public function survey()
    {
        return $this->belongsTo(NgnSurvey::class, 'survey_id');
    }

    public function options()
    {
        return $this->hasMany(NgnSurveyOption::class, 'question_id');
    }

    public function answers()
    {
        return $this->hasMany(NgnSurveyAnswer::class, 'question_id');
    }
}
