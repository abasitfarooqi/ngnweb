<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsVersion extends Model
{
    use HasFactory;

    protected $table = 'terms_versions'; // Ensure this matches your database table name

    protected $fillable = [
        'version',
        'content',
        'published_at',
        'is_active',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('version', 'desc');
    }

    public function getIsCurrentAttribute()
    {
        return $this->is_active && $this->published_at->isPast();
    }
}
