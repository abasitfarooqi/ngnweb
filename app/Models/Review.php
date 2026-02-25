<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Review
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $is_recommended
 * @property int $rating
 * @property string|null $title
 * @property string|null $content
 * @property bool $approved
 * @property string $reviewrateable_type
 * @property int $reviewrateable_id
 * @property string $author_type
 * @property int $author_id
 */
class Review extends Model
{
    protected $table = 'reviews';

    protected $casts = [
        'is_recommended' => 'bool',
        'rating' => 'int',
        'approved' => 'bool',
        'reviewrateable_id' => 'int',
        'author_id' => 'int',
    ];

    protected $fillable = [
        'is_recommended',
        'rating',
        'title',
        'content',
        'approved',
        'reviewrateable_type',
        'reviewrateable_id',
        'author_type',
        'author_id',
    ];
}
