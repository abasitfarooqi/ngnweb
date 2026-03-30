<?php

namespace App\Models\SpareParts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpAssembly extends Model
{
    protected $table = 'sp_assemblies';

    protected $fillable = [
        'fitment_id',
        'external_id',
        'slug',
        'name',
        'image_url',
        'diagram_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function fitment(): BelongsTo
    {
        return $this->belongsTo(SpFitment::class, 'fitment_id');
    }

    public function assemblyParts(): HasMany
    {
        return $this->hasMany(SpAssemblyPart::class, 'assembly_id');
    }
}
