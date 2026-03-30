<?php

namespace App\Models\SpareParts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpPart extends Model
{
    protected $table = 'sp_parts';

    protected $fillable = [
        'part_number',
        'name',
        'note',
        'stock_status',
        'price_gbp_inc_vat',
        'meta',
        'last_synced_at',
        'is_active',
    ];

    protected $casts = [
        'price_gbp_inc_vat' => 'decimal:2',
        'meta' => 'array',
        'last_synced_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function assemblyParts(): HasMany
    {
        return $this->hasMany(SpAssemblyPart::class, 'part_id');
    }
}
