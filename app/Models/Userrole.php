<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Userrole
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property string|null $display_name
 * @property string|null $description
 * @property bool $can_be_removed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Collection|ModelHasRole[] $model_has_roles
 * @property Collection|RoleHasPermission[] $role_has_permissions
 */
class Userrole extends Model
{
    protected $table = 'userroles';

    protected $casts = [
        'can_be_removed' => 'bool',
    ];

    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
        'can_be_removed',
    ];

    public function model_has_roles()
    {
        return $this->hasMany(ModelHasRole::class, 'role_id');
    }

    public function role_has_permissions()
    {
        return $this->hasMany(RoleHasPermission::class, 'role_id');
    }
}
