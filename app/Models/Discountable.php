<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Discountable
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $condition
 * @property int $total_use
 * @property string $discountable_type
 * @property int $discountable_id
 * @property int $discount_id
 * @property Discount $discount
 */
class Discountable extends Model
{
    protected $table = 'discountables';

    protected $casts = [
        'total_use' => 'int',
        'discountable_id' => 'int',
        'discount_id' => 'int',
    ];

    protected $fillable = [
        'condition',
        'total_use',
        'discountable_type',
        'discountable_id',
        'discount_id',
    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
