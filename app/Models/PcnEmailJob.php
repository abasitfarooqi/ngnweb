<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PcnEmailJob extends Model
{
    use HasFactory;

    protected $table = 'pcn_email_jobs';

    protected $id = 'id';

    protected $casts = [
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
        'template_name' => 'string',
    ];

    protected $fillable = [
        'customer_id',
        'motorbike_id',
        'case_id',
        'user_id',
        'is_sent',
        'sent_at',
        'template_code',
        'force_stop',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class, 'motorbike_id');
    }

    public function pcnCase()
    {
        return $this->belongsTo(PcnCase::class, 'case_id');
    }
}
