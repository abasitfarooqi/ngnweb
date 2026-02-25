<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusFlag extends Model
{
    use HasFactory;

    protected $fillable = ['short_name', 'long_name', 'color', 'icon'];

    public static function getColor($shortName)
    {
        $statusFlag = self::where('short_name', $shortName)->first();

        return $statusFlag ? $statusFlag->color : null;
    }
}
