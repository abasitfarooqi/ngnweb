<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OxfordCategory extends Model
{
    use HasFactory;

    public function oxfords()
    {
        return $this->hasManyThrough(Oxford::class, Oxford::class, 'category_id');
    }
}
