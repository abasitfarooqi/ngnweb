<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingAlert extends Model
{
    protected $fillable = ['vehicle_id', 'trackable_type', 'trackable_id', 'tracking_source_id', 'alert_type', 'severity', 'title', 'message', 'metadata', 'triggered_at', 'resolved_at', 'acknowledged_at', 'acknowledged_by'];
    protected $casts = ['metadata' => 'array', 'triggered_at' => 'datetime', 'resolved_at' => 'datetime', 'acknowledged_at' => 'datetime'];
}
