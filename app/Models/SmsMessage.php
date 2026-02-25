<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsMessage extends Model
{
    use HasFactory;

    protected $table = 'sms_messages';

    protected $fillable = [
        'sid',
        'account_sid',
        'api_version',
        'body',
        'date_created',
        'date_sent',
        'date_updated',
        'direction',
        'error_code',
        'error_message',
        'from',
        'to',
        'messaging_service_sid',
        'num_media',
        'num_segments',
        'price',
        'price_unit',
        'status',
        'uri',
        'subresource_uris',
    ];

    protected $casts = [
        'subresource_uris' => 'array',
        'date_created' => 'datetime',
        'date_sent' => 'datetime',
        'date_updated' => 'datetime',
    ];
}
