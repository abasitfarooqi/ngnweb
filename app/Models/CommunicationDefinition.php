<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CommunicationDefinition extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'classification',
        'category',
        'priority',
        'source_class',
        'source_trigger',
        'email_class',
        'template_view',
        'recipient_summary',
        'supported_channels',
        'variables',
        'metadata',
        'existing_email_default',
        'active',
    ];

    protected $casts = [
        'supported_channels' => 'array',
        'variables' => 'array',
        'metadata' => 'array',
        'existing_email_default' => 'boolean',
        'active' => 'boolean',
    ];

    public function policy(): HasOne
    {
        return $this->hasOne(CommunicationPolicy::class, 'communication_definition_id');
    }
}
