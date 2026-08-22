<?php

namespace App\Support;

use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Throwable;

final class FluxAdminRequiredColumn
{
    public static function matches(Throwable $e): bool
    {
        $message = $e->getMessage();

        return str_contains($message, "doesn't have a default value")
            || str_contains($message, 'cannot be null')
            || str_contains($message, 'NOT NULL constraint failed');
    }

    public static function column(Throwable $e): ?string
    {
        return self::columnFromMessage($e->getMessage());
    }

    public static function columnFromMessage(string $message): ?string
    {
        if (preg_match("/Field '([^']+)' doesn't have a default value/", $message, $match)) {
            return $match[1];
        }

        if (preg_match("/Column '([^']+)' cannot be null/", $message, $match)) {
            return $match[1];
        }

        if (preg_match('/NOT NULL constraint failed:\s+\w+\.(\w+)/i', $message, $match)) {
            return $match[1];
        }

        return null;
    }

    public static function message(?string $column): string
    {
        $label = $column ? str_replace('_', ' ', $column) : 'this field';

        return 'Please fill in '.$label.'. This field is required.';
    }

    public static function applyToComponent(object $component, Throwable $e): void
    {
        $column = self::column($e);
        $message = self::message($column);
        $key = FluxAdminUniqueViolation::errorKey($component, $column);

        if (method_exists($component, 'addError')) {
            $component->addError($key, $message);
        }

        if (method_exists($component, 'dispatch')) {
            $component->dispatch('flux-admin:toast', type: 'error', message: $message);
        }
    }

    public static function failValidation(object $component, Throwable $e): never
    {
        $column = self::column($e);

        throw ValidationException::withMessages([
            FluxAdminUniqueViolation::errorKey($component, $column) => [self::message($column)],
        ]);
    }
}
