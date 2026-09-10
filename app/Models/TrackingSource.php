<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrackingSource extends Model
{
    protected $fillable = ['vehicle_id', 'customer_id', 'source_type', 'device_identifier', 'provider', 'status', 'sharing_enabled', 'last_seen_at', 'metadata'];
    protected $casts = ['sharing_enabled' => 'boolean', 'last_seen_at' => 'datetime', 'metadata' => 'array'];
    public function vehicle(): BelongsTo { return $this->belongsTo(Motorbike::class, 'vehicle_id'); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function locations(): HasMany { return $this->hasMany(TrackingLocation::class); }
}
