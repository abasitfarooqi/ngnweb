<?php

namespace App\Support;

use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;
use Throwable;

final class FluxAdminUniqueViolation
{
    /** @var array<string, string> */
    private const MESSAGES = [
        'email' => 'This email is already in use. Please use a different email.',
        'username' => 'This username is already in use.',
        'sku' => 'This SKU is already in use.',
        'slug' => 'This slug is already in use.',
        'name' => 'This name is already in use.',
        'companyname' => 'This company name is already in use.',
        'vin_number' => 'This VIN is already in use.',
        'invoice_number' => 'This invoice number is already in use.',
        'pos_invoice' => 'This POS invoice number is already in use.',
        'part_number' => 'This part number is already in use.',
        'motorbike_id' => 'This motorbike is already assigned.',
    ];

    public static function matches(Throwable $e): bool
    {
        if ($e instanceof UniqueConstraintViolationException) {
            return true;
        }

        if (! $e instanceof QueryException) {
            return false;
        }

        $message = $e->getMessage();

        return str_contains($message, 'Duplicate entry')
            || str_contains($message, 'UNIQUE constraint failed')
            || str_contains($message, 'duplicate key value violates unique constraint');
    }

    public static function column(Throwable $e): ?string
    {
        $sql = $e instanceof QueryException ? (string) $e->getSql() : '';

        return self::columnFromMessage($e->getMessage(), $sql);
    }

    public static function columnFromMessage(string $message, string $sql = ''): ?string
    {
        if (preg_match('/UNIQUE constraint failed:\s+(\w+)\.(\w+)/i', $message, $match)) {
            return $match[2];
        }

        if (preg_match('/Key \(([^)]+)\)=/i', $message, $match)) {
            $parts = preg_split('/\s*,\s*/', $match[1]) ?: [];

            return count($parts) === 1 ? trim((string) $parts[0], '"') : null;
        }

        if (! preg_match("/for key ['`](?:([\w]+)\.)?([\w]+)['`]/i", $message, $match)) {
            return null;
        }

        $table = $match[1] !== '' ? $match[1] : self::tableFromSql($sql);
        $index = preg_replace('/_unique$/', '', $match[2]) ?? $match[2];

        if ($table !== null && str_starts_with($index, $table.'_')) {
            $index = substr($index, strlen($table) + 1);
        }

        return $index !== '' ? $index : null;
    }

    public static function message(?string $column): string
    {
        if ($column !== null && isset(self::MESSAGES[$column])) {
            return self::MESSAGES[$column];
        }

        if ($column === null || str_contains($column, '_id_') || substr_count($column, '_') > 2) {
            return 'This value is already in use.';
        }

        $label = str_replace('_', ' ', $column);

        return 'This '.$label.' is already in use.';
    }

    public static function errorKey(object $component, ?string $column): string
    {
        $column ??= 'form';

        foreach (['form', 'formData'] as $bag) {
            if (property_exists($component, $bag) && is_array($component->{$bag}) && array_key_exists($column, $component->{$bag})) {
                return $bag.'.'.$column;
            }
        }

        if ($column !== 'form' && property_exists($component, $column)) {
            return $column;
        }

        if (property_exists($component, 'form')) {
            return 'form.'.$column;
        }

        if (property_exists($component, 'formData')) {
            return 'formData.'.$column;
        }

        return $column;
    }

    public static function applyToComponent(object $component, Throwable $e): void
    {
        $column = self::column($e);
        $message = self::message($column);

        if (method_exists($component, 'addError')) {
            $component->addError(self::errorKey($component, $column), $message);
        }

        if (method_exists($component, 'dispatch')) {
            $component->dispatch('flux-admin:toast', type: 'error', message: $message);
        }
    }

    public static function failValidation(object $component, Throwable $e): never
    {
        $column = self::column($e);

        throw ValidationException::withMessages([
            self::errorKey($component, $column) => [self::message($column)],
        ]);
    }

    private static function tableFromSql(string $sql): ?string
    {
        if (preg_match('/\b(?:insert into|update)\s+[`"]?(\w+)[`"]?/i', $sql, $match)) {
            return $match[1];
        }

        return null;
    }
}
