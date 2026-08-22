<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use ReflectionClass;
use ReflectionNamedType;
use Throwable;

final class FluxAdminSchemaRules
{
    /** @var array<string, list<string>> */
    private static array $requiredColumns = [];

    /** @var array<string, class-string<Model>> */
    private const MODEL_ALIASES = [
        'ServiceVideoForm' => \App\Models\RentingServiceVideo::class,
        'EbikeForm' => \App\Models\Motorbike::class,
        'CatBForm' => \App\Models\Motorbike::class,
        'NewMotorbikeForm' => \App\Models\NewMotorbike::class,
        'MotBookingForm' => \App\Models\MOTBooking::class,
        'BookingInvoiceForm' => \App\Models\BookingInvoice::class,
        'CompanyVehicleForm' => \App\Models\CompanyVehicle::class,
    ];

    /**
     * @return list<string>
     */
    public static function requiredColumns(string $table): array
    {
        if (isset(self::$requiredColumns[$table])) {
            return self::$requiredColumns[$table];
        }

        if (! Schema::hasTable($table)) {
            return self::$requiredColumns[$table] = [];
        }

        $skip = ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'email_verified_at'];

        $columns = [];
        foreach (Schema::getColumns($table) as $column) {
            $name = (string) ($column['name'] ?? '');
            if ($name === '' || in_array($name, $skip, true)) {
                continue;
            }

            if (! empty($column['auto_increment'])) {
                continue;
            }

            if (! empty($column['nullable'])) {
                continue;
            }

            if (array_key_exists('default', $column) && $column['default'] !== null) {
                continue;
            }

            $columns[] = $name;
        }

        return self::$requiredColumns[$table] = $columns;
    }

    /**
     * @param  array<string, mixed>  $bag
     * @return array<string, list<string>>
     */
    public static function rulesForBag(string $modelClass, array $bag, string $prefix): array
    {
        if (! is_subclass_of($modelClass, Model::class)) {
            return [];
        }

        $table = (new $modelClass)->getTable();
        $rules = [];

        foreach (self::requiredColumns($table) as $column) {
            if (! array_key_exists($column, $bag)) {
                continue;
            }

            $rules[$prefix.'.'.$column] = ['required'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public static function messages(array $rules): array
    {
        $messages = [];

        foreach (array_keys($rules) as $key) {
            $column = (string) str($key)->afterLast('.');
            $messages[$key.'.required'] = FluxAdminRequiredColumn::message($column);
        }

        return $messages;
    }

    public static function modelClassFor(object $component): ?string
    {
        if (method_exists($component, 'formModel')) {
            $class = $component->formModel();
            if (is_string($class) && is_subclass_of($class, Model::class)) {
                return $class;
            }
        }

        $short = class_basename($component);
        if (isset(self::MODEL_ALIASES[$short])) {
            return self::MODEL_ALIASES[$short];
        }

        try {
            $reflection = new ReflectionClass($component);
            foreach ($reflection->getProperties() as $property) {
                if ($property->getDeclaringClass()->getName() === Component::class) {
                    continue;
                }

                $type = $property->getType();
                if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
                    continue;
                }

                $class = $type->getName();
                if (is_subclass_of($class, Model::class)) {
                    return $class;
                }
            }
        } catch (Throwable) {
            // Fall through to the class-name guess.
        }

        $guess = 'App\\Models\\'.preg_replace('/Form$/', '', $short);

        return is_string($guess) && is_subclass_of($guess, Model::class) ? $guess : null;
    }
}
