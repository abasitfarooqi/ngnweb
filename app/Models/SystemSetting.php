<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SystemSetting
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $key
 * @property string|null $display_name
 * @property string|null $value
 * @property bool $locked
 */
class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $casts = [
        'locked' => 'bool',
    ];

    protected $fillable = [
        'key',
        'display_name',
        'value',
        'locked',
    ];
}
