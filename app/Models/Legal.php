<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Legal
 *
 * @property int $id
 * @property string $title
 * @property string|null $slug
 * @property string|null $content
 * @property bool $is_enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Legal extends Model
{
    protected $table = 'legals';

    protected $casts = [
        'is_enabled' => 'bool',
    ];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_enabled',
    ];
}
