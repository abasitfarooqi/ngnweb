<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SystemCurrency
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $symbol
 * @property string $format
 * @property string|null $exchange_rate
 */
class SystemCurrency extends Model
{
    protected $table = 'system_currencies';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'format',
        'exchange_rate',
    ];
}
