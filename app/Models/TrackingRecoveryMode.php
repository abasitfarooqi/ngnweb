<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingRecoveryMode extends Model
{
    protected $fillable = ['vehicle_id', 'trackable_type', 'trackable_id', 'tracking_source_id', 'activated_by', 'activated_at', 'ended_at', 'reason', 'notes', 'status'];
    protected $casts = ['activated_at' => 'datetime', 'ended_at' => 'datetime'];
}
