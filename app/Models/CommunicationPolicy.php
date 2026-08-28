<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationPolicy extends Model
{
    protected $fillable = [
        'communication_definition_id',
        'email_enabled',
        'internal_inbox_enabled',
        'staff_copy_enabled',
        'web_push_enabled',
        'mobile_push_enabled',
        'reply_allowed',
        'enquiry_allowed',
        'mandatory',
        'priority',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'internal_inbox_enabled' => 'boolean',
        'staff_copy_enabled' => 'boolean',
        'web_push_enabled' => 'boolean',
        'mobile_push_enabled' => 'boolean',
        'reply_allowed' => 'boolean',
        'enquiry_allowed' => 'boolean',
        'mandatory' => 'boolean',
    ];

    public function definition(): BelongsTo
    {
        return $this->belongsTo(CommunicationDefinition::class, 'communication_definition_id');
    }
}
