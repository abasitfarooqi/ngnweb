<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_member_id',
        'feedback_text',
        'submitted_at',
    ];

    public function clubMember()
    {
        return $this->belongsTo(ClubMember::class);
    }
}
