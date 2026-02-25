<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class NgnSurveyAnswer extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ngn_survey_answers';

    protected $fillable = [
        'response_id',
        'question_id',
        'option_id',
        'answer_text',
    ];

    public function response()
    {
        return $this->belongsTo(NgnSurveyResponse::class, 'response_id');
    }

    public function question()
    {
        return $this->belongsTo(NgnSurveyQuestion::class, 'question_id');
    }

    public function option()
    {
        return $this->belongsTo(NgnSurveyOption::class, 'option_id');
    }
}
