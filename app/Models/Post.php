<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Post
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $description
 * @property string $body
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property UsersOld $users_old
 */
class Post extends Model
{
    protected $table = 'posts';

    protected $casts = [
        'user_id' => 'int',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'body',
    ];

    public function users_old()
    {
        return $this->belongsTo(UsersOld::class, 'user_id');
    }
}
