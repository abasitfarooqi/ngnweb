<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentingWeeklyUpdateLog extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'renting_weekly_update_logs';

    protected $fillable = [
        'renting_weekly_update_id',
        'action',
        'old_data',
        'new_data',
        'changed_by',
        'created_at',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
    ];

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function save(array $options = [])
    {
        if ($this->exists) {
            throw new \RuntimeException('Weekly rental update audit logs cannot be changed.');
        }

        return parent::save($options);
    }

    public function delete()
    {
        throw new \RuntimeException('Weekly rental update audit logs cannot be deleted.');
    }
}
