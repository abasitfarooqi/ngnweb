<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NgnMotNotifier extends Model
{
    protected $table = 'ngn_mot_notifier';

    protected $fillable = [
        'motorbike_id',
        'motorbike_reg',
        'mot_due_date',
        'tax_due_date',
        'insurance_due_date',
        'mot_status',
        'customer_name',
        'customer_contact',
        'customer_email',

        'mot_notify_email',
        'mot_notify_phone',

        'mot_is_email_sent',
        'mot_is_phone_sent',

        'mot_is_whatsapp_sent',

        'mot_is_notified_30',
        'mot_is_notified_10',

        'mot_email_sent_30',
        'mot_email_sent_10',

        'mot_email_sent_expired',

        'mot_last_email_notification_date',
        'mot_last_phone_notification_date',
        'mot_last_whatsapp_notification_date',

        'notes',
    ];

    protected $dates = [
        'mot_due_date',
        'tax_due_date',
        'insurance_due_date',
        'mot_last_email_notification_date',
        'mot_last_phone_notification_date',
        'mot_last_whatsapp_notification_date',
    ];

    // Accessors
    public function getMotDueDateFormattedAttribute()
    {
        return $this->mot_due_date ? $this->mot_due_date->format('d/m/Y') : 'N/A';
    }

    public function getMotLastWhatsappNotificationDateFormattedAttribute()
    {
        return $this->mot_last_whatsapp_notification_date ? $this->mot_last_whatsapp_notification_date->format('d/m/Y') : 'N/A';
    }

    public function getTaxDueDateFormattedAttribute()
    {
        return $this->tax_due_date ? $this->tax_due_date->format('d/m/Y') : 'N/A';
    }

    public function getInsuranceDueDateFormattedAttribute()
    {
        return $this->insurance_due_date ? $this->insurance_due_date->format('d/m/Y') : 'N/A';
    }

    public function motorbike()
    {
        return $this->belongsTo(\App\Models\Motorbike::class, 'motorbike_id');
    }

    public function customer()
    {
        return \App\Models\Customer::where('email', $this->customer_email)->first();
    }
}
