<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    use CrudTrait, HasFactory;

    protected $table = 'blog_tags';

    protected $fillable = ['name', 'slug'];
}
