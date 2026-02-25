<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Filerental extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_path',
        'user_id',
    ];

    /**
     * Get the rental agreement this payment belongs to
     */
    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(motorcycle::class, 'id', 'motorcycle_id');
    }
}
