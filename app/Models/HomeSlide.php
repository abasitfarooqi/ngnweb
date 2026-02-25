<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HomeSlide
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $short_title
 * @property string|null $home_slide
 * @property string|null $video_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class HomeSlide extends Model
{
    protected $table = 'home_slides';

    protected $fillable = [
        'title',
        'short_title',
        'home_slide',
        'video_url',
    ];
}
