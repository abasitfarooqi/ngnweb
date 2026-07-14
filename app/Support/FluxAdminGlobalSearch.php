<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FluxAdminGlobalSearch
{
    protected const TEXT_TYPES = ['varchar', 'char', 'text', 'tinytext', 'mediumtext', 'longtext'];

    protected const SKIP_COLUMNS = ['password', 'remember_token', 'deleted_at', 'created_at', 'updated_at'];

    /** @return array{results: array<int, array<string, mixed>>, total: int, resources_searched: int} */
    public static function search(string $query, int $perResource = 5, int $maxResults = 100): array
    {
        $term = trim($query);
        if (mb_strlen($term) < 2) {
            return ['results' => [], 'total' => 0, 'resources_searched' => 0];
        }

        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';
        $results = [];
        $searched = 0;

        foreach (FluxAdminSearchRegistry::resources() as $resource) {
            if (! Route::has($resource['index'])) {
                continue;
            }

            if (count($results) >= $maxResults) {
                break;
            }

            $modelClass = $resource['model'];
            if (! class_exists($modelClass) || ! is_subclass_of($modelClass, Model::class)) {
                continue;
            }

            /** @var Model $prototype */
            $prototype = new $modelClass;
            $columns = self::searchableColumns($prototype);

            if ($columns === []) {
                continue;
            }

            $searched++;
            $remaining = min($perResource, $maxResults - count($results));

            $rows = $modelClass::query()
                ->where(function ($builder) use ($columns, $like) {
                    foreach ($columns as $column) {
                        $builder->orWhere($column, 'like', $like);
                    }
                })
                ->limit($remaining)
                ->get();

            foreach ($rows as $row) {
                $results[] = self::formatHit($resource, $row, $term, $columns);
            }
        }

        return [
            'results' => $results,
            'total' => count($results),
            'resources_searched' => $searched,
        ];
    }

    /** @return list<string> */
    protected static function searchableColumns(Model $model): array
    {
        $table = $model->getTable();

        return Cache::remember("flux-admin.search.columns.{$table}", now()->addDay(), function () use ($model, $table) {
            if (! Schema::hasTable($table)) {
                return [];
            }

            $connection = $model->getConnection();
            $database = $connection->getDatabaseName();
            $rows = $connection->select(
                'SELECT COLUMN_NAME, DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?',
                [$database, $table]
            );

            return collect($rows)
                ->filter(fn ($row) => in_array(strtolower($row->DATA_TYPE), self::TEXT_TYPES, true))
                ->pluck('COLUMN_NAME')
                ->filter(fn ($name) => preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name) === 1)
                ->reject(fn ($name) => in_array($name, self::SKIP_COLUMNS, true))
                ->reject(fn ($name) => Str::endsWith($name, '_token'))
                ->values()
                ->all();
        });
    }

    /** @param array<string, mixed> $resource */
    /** @param list<string> $columns */
    protected static function formatHit(array $resource, Model $row, string $term, array $columns): array
    {
        $title = self::recordTitle($row);
        $snippet = self::matchSnippet($row, $term, $columns);

        return [
            'label' => $resource['label'],
            'title' => $title,
            'snippet' => $snippet,
            'id' => $row->getKey(),
            'index_url' => route($resource['index'], ['q' => $term]),
            'show_url' => self::resourceUrl($resource['show'] ?? null, $row, (string) ($resource['param'] ?? 'id')),
            'edit_url' => self::resourceUrl($resource['edit'] ?? null, $row, (string) ($resource['param'] ?? 'id')),
        ];
    }

    protected static function resourceUrl(?string $name, Model $row, string $param): ?string
    {
        if ($name === null || $name === '' || ! Route::has($name)) {
            return null;
        }

        $route = Route::getRoutes()->getByName($name);
        $parameterNames = $route?->parameterNames() ?? [];

        if ($parameterNames === []) {
            return route($name);
        }

        $key = in_array($param, $parameterNames, true) ? $param : $parameterNames[0];

        try {
            return route($name, [$key => $row->getKey()]);
        } catch (\Throwable) {
            return null;
        }
    }

    protected static function recordTitle(Model $row): string
    {
        foreach (['name', 'title', 'label', 'email', 'reg_no', 'registration', 'pcn_number', 'sku', 'ip_address', 'slug', 'first_name', 'full_name', 'topic', 'subject'] as $field) {
            $value = data_get($row, $field);
            if (is_string($value) && trim($value) !== '') {
                if ($field === 'first_name' && filled($row->last_name ?? null)) {
                    return trim($row->first_name.' '.$row->last_name);
                }

                return trim($value);
            }
        }

        return '#'.$row->getKey();
    }

    /** @param list<string> $columns */
    protected static function matchSnippet(Model $row, string $term, array $columns): ?string
    {
        $needle = Str::lower($term);

        foreach ($columns as $column) {
            $value = $row->getAttribute($column);
            if (! is_scalar($value) || (string) $value === '') {
                continue;
            }
            if (Str::contains(Str::lower((string) $value), $needle)) {
                $text = Str::limit((string) $value, 80);

                return $column.': '.$text;
            }
        }

        return null;
    }
}
