<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TrackingLocation extends Model
{
    protected $fillable = ['tracking_source_id', 'trackable_type', 'trackable_id', 'vehicle_id', 'customer_id', 'latitude', 'longitude', 'accuracy_metres', 'speed', 'heading', 'altitude', 'recorded_at', 'received_at', 'test_data'];
    protected $casts = ['latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'recorded_at' => 'datetime', 'received_at' => 'datetime', 'test_data' => 'boolean'];
    public function source(): BelongsTo { return $this->belongsTo(TrackingSource::class, 'tracking_source_id'); }
    public function trackable(): MorphTo { return $this->morphTo(); }
    public function vehicle(): BelongsTo { return $this->belongsTo(Motorbike::class, 'vehicle_id'); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
}
