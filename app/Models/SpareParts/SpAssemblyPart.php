<?php

namespace App\Models\SpareParts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpAssemblyPart extends Model
{
    protected $table = 'sp_assembly_parts';

    protected $fillable = [
        'assembly_id',
        'part_id',
        'qty_used',
        'sort_order',
        'note_override',
        'price_override',
        'stock_override',
    ];

    protected $casts = [
        'qty_used' => 'integer',
        'sort_order' => 'integer',
        'price_override' => 'decimal:2',
    ];

    public function assembly(): BelongsTo
    {
        return $this->belongsTo(SpAssembly::class, 'assembly_id');
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(SpPart::class, 'part_id');
    }
}
