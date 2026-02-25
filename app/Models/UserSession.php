<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_member_id',
        'login_time',
        'logout_time',
        'session_duration',
        'pages_visited',
    ];

    protected $casts = [
        'pages_visited' => 'array',
    ];

    public function clubMember()
    {
        return $this->belongsTo(ClubMember::class);
    }
}
