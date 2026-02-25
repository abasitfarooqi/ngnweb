<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property string|null $group_name
 * @property string|null $display_name
 * @property string|null $description
 * @property bool $can_be_removed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Collection|ModelHasPermission[] $model_has_permissions
 * @property Collection|RoleHasPermission[] $role_has_permissions
 */
class Permission extends Model
{
    protected $table = 'permissions';

    protected $casts = [
        'can_be_removed' => 'bool',
    ];

    protected $fillable = [
        'name',
        'guard_name',
        'group_name',
        'display_name',
        'description',
        'can_be_removed',
    ];

    public function model_has_permissions()
    {
        return $this->hasMany(ModelHasPermission::class);
    }

    public function role_has_permissions()
    {
        return $this->hasMany(RoleHasPermission::class);
    }
}
