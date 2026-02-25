<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Inventory
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property string $email
 * @property string $street_address
 * @property string|null $street_address_plus
 * @property string $zipcode
 * @property string $city
 * @property string|null $phone_number
 * @property int $priority
 * @property float|null $latitude
 * @property float|null $longitude
 * @property bool $is_default
 * @property int|null $country_id
 * @property SystemCountry|null $system_country
 * @property Collection|InventoryHistory[] $inventory_histories
 */
class Inventory extends Model
{
    protected $table = 'inventories';

    protected $casts = [
        'priority' => 'int',
        'latitude' => 'float',
        'longitude' => 'float',
        'is_default' => 'bool',
        'country_id' => 'int',
    ];

    protected $fillable = [
        'name',
        'code',
        'description',
        'email',
        'street_address',
        'street_address_plus',
        'zipcode',
        'city',
        'phone_number',
        'priority',
        'latitude',
        'longitude',
        'is_default',
        'country_id',
    ];

    public function system_country()
    {
        return $this->belongsTo(SystemCountry::class, 'country_id');
    }

    public function inventory_histories()
    {
        return $this->hasMany(InventoryHistory::class);
    }
}
