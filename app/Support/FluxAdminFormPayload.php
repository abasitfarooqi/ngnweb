<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/** Strip form payloads to real DB columns before create/update (avoids Unknown column SQL errors). */
final class FluxAdminFormPayload
{
    public static function adminUserId(): ?int
    {
        $id = function_exists('backpack_user') ? backpack_user()?->id : null;

        return $id ?? auth()->id();
    }

    /**
     * @return array<string, mixed>
     */
    public static function formAttributesFromModel(Model $model): array
    {
        return self::onlyPersistable($model, $model->getAttributes());
    }

    /**
     * @param  class-string<Model>|Model  $model
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function onlyPersistable(Model|string $model, array $payload): array
    {
        $instance = is_string($model) ? new $model : $model;
        $table = $instance->getTable();

        static $columnCache = [];
        if (! isset($columnCache[$table])) {
            $columnCache[$table] = Schema::hasTable($table)
                ? array_flip(Schema::getColumnListing($table))
                : [];
        }

        $columns = $columnCache[$table];
        if ($columns === []) {
            return $payload;
        }

        $fillable = $instance->getFillable();
        if ($fillable !== []) {
            $payload = array_intersect_key($payload, array_flip($fillable));
        }

        return array_intersect_key(
            $payload,
            array_diff_key($columns, array_flip(['id', 'created_at', 'updated_at']))
        );
    }
}
