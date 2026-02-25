<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class NgnSurveyOption extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ngn_survey_options';

    protected $fillable = [
        'question_id',
        'option_text',
    ];

    public function question()
    {
        return $this->belongsTo(NgnSurveyQuestion::class, 'question_id');
    }
}
