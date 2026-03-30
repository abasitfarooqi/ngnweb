<?php

namespace App\Models\SpareParts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpFitment extends Model
{
    protected $table = 'sp_fitments';

    protected $fillable = [
        'model_id',
        'year',
        'country_slug',
        'country_name',
        'colour_slug',
        'colour_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(SpModel::class, 'model_id');
    }

    public function assemblies(): HasMany
    {
        return $this->hasMany(SpAssembly::class, 'fitment_id');
    }
}
