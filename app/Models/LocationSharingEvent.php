<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationSharingEvent extends Model
{
    protected $fillable = ['customer_id', 'trackable_type', 'trackable_id', 'tracking_source_id', 'action', 'policy_version', 'disclosure_version', 'recorded_at'];
    protected $casts = ['recorded_at' => 'datetime'];
}
