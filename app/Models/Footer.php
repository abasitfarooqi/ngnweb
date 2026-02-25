<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Footer
 *
 * @property int $id
 * @property string|null $number
 * @property string|null $short_description
 * @property string|null $adress
 * @property string|null $email
 * @property string|null $facebook
 * @property string|null $twitter
 * @property string|null $copyright
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Footer extends Model
{
    protected $table = 'footers';

    protected $fillable = [
        'number',
        'short_description',
        'adress',
        'email',
        'facebook',
        'twitter',
        'copyright',
    ];
}
