<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Channel
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name
 * @property string|null $slug
 * @property string|null $description
 * @property string|null $timezone
 * @property string|null $url
 * @property bool $is_default
 */
class Channel extends Model
{
    protected $table = 'channels';

    protected $casts = [
        'is_default' => 'bool',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'timezone',
        'url',
        'is_default',
    ];
}
