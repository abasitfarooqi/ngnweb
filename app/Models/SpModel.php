<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpModel extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $table = 'sp_models';

    protected $fillable = [
        'make_id',
        'slug',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function make(): BelongsTo
    {
        return $this->belongsTo(SpMake::class, 'make_id');
    }

    public function fitments(): HasMany
    {
        return $this->hasMany(SpFitment::class, 'model_id');
    }
}
