<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Portfolio
 *
 * @property int $id
 * @property string|null $portfolio_name
 * @property string|null $portfolio_title
 * @property string|null $portfolio_image
 * @property string|null $portfolio_description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Portfolio extends Model
{
    protected $table = 'portfolios';

    protected $fillable = [
        'portfolio_name',
        'portfolio_title',
        'portfolio_image',
        'portfolio_description',
    ];
}
