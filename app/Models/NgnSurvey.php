<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class NgnSurvey extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'ngn_surveys';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'is_active',
    ];

    public function questions()
    {
        return $this->hasMany(NgnSurveyQuestion::class, 'survey_id');
    }

    public function responses()
    {
        return $this->hasMany(NgnSurveyResponse::class, 'survey_id');
    }

    public function getQuestionsAttribute()
    {
        return $this->questions()->with('options')->get();
    }

    public function setQuestionsAttribute($value)
    {
        $this->save();
        $this->questions()->delete();

        collect($value)->each(function ($questionData) {
            $question = $this->questions()->create($questionData);
            if (array_key_exists('options', $questionData)) {
                $question->options()->createMany($questionData['options']);
            }
        });
    }

    public static function boot()
    {
        parent::boot();
        static::saving(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }
}
