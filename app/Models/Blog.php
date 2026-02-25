<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Blog
 *
 * @property int $id
 * @property string|null $blog_category_id
 * @property string|null $blog_title
 * @property string|null $blog_image
 * @property string|null $blog_tags
 * @property string|null $blog_description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Blog extends Model
{
    protected $table = 'blogs';

    protected $fillable = [
        'blog_category_id',
        'blog_title',
        'blog_image',
        'blog_tags',
        'blog_description',
    ];
}
