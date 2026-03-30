<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpMake extends Model
{
    use HasFactory;

    protected $table = 'sp_makes';

    protected $fillable = [
        'slug',
        'name',
        'source',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function models(): HasMany
    {
        return $this->hasMany(SpModel::class, 'make_id');
    }
}
