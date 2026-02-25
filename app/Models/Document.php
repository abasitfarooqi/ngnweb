<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Document
 *
 * @property int $id
 * @property string|null $driving_licence_number
 * @property string|null $file_name
 * @property string $path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $user_id
 * @property int|null $motorcycle_id
 */
class Document extends Model
{
    protected $table = 'documents';

    protected $casts = [
        'user_id' => 'int',
        'motorcycle_id' => 'int',
    ];

    protected $fillable = [
        'driving_licence_number',
        'file_name',
        'path',
        'user_id',
        'motorcycle_id',
    ];
}
