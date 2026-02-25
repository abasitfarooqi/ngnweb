<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_member_id',
        'segment_type',
    ];

    public function clubMember()
    {
        return $this->belongsTo(ClubMember::class);
    }
}
