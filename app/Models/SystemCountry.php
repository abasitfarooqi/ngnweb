<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SystemCountry
 *
 * @property int $id
 * @property string $name
 * @property string $name_official
 * @property string $cca2
 * @property string $cca3
 * @property string $flag
 * @property float $latitude
 * @property float $longitude
 * @property string $currencies
 * @property Collection|Inventory[] $inventories
 * @property Collection|UserAddress[] $user_addresses
 */
class SystemCountry extends Model
{
    protected $table = 'system_countries';

    public $timestamps = false;

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    protected $fillable = [
        'name',
        'name_official',
        'cca2',
        'cca3',
        'flag',
        'latitude',
        'longitude',
        'currencies',
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'country_id');
    }

    public function user_addresses()
    {
        return $this->hasMany(UserAddress::class, 'country_id');
    }
}
