<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductType
 *
 * @property int $id
 * @property string $types
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ProductType extends Model
{
    protected $table = 'product_types';

    protected $fillable = [
        'types',
    ];
}
