<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class SurveyEmailCampaign extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'survey_email_campaigns';

    protected $fillable = [
        'ngn_survey_id',
        'fullname',
        'email',
        'phone',
        'send_email',
        'send_phone',
        'is_sent',
        'last_email_sent_datetime',
        'last_sms_sent_datetime',
        'last_whatsapp_sent_datetime',
        'is_email_sent',
        'is_sms_sent',
        'is_whatsapp_sent',
    ];

    /**
     * Get the survey associated with the email campaign.
     */
    public function survey()
    {
        return $this->belongsTo(NgnSurvey::class, 'ngn_survey_id');
    }
}
