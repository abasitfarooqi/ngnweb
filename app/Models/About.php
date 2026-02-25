<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class About
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $short_title
 * @property string|null $short_description
 * @property string|null $long_description
 * @property string|null $about_image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class About extends Model
{
    protected $table = 'abouts';

    protected $fillable = [
        'title',
        'short_title',
        'short_description',
        'long_description',
        'about_image',
    ];
}
