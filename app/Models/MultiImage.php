<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MultiImage
 *
 * @property int $id
 * @property string|null $multi_image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class MultiImage extends Model
{
    protected $table = 'multi_images';

    protected $fillable = [
        'multi_image',
    ];
}
