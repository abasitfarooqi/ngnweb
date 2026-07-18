<?php

namespace App\Support;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerAuth;
use App\Models\DsOrder;
use App\Models\Motorbike;
use App\Models\NgnDigitalInvoice;
use App\Models\User;

final class FluxAdminEntityLabel
{
    public static function joinParts(array $parts, string $fallback): string
    {
        $filtered = array_values(array_filter($parts, fn ($p) => filled($p)));

        return $filtered !== [] ? implode(' · ', $filtered) : $fallback;
    }

    public static function customer(?Customer $customer): string
    {
        if (! $customer) {
            return '—';
        }

        $name = trim(($customer->first_name ?? '').' '.($customer->last_name ?? ''));

        return self::joinParts([$name ?: null, $customer->phone ?? null, $customer->email ?? null], 'Customer #'.$customer->id);
    }

    public static function customerAuth(?CustomerAuth $auth): string
    {
        if (! $auth) {
            return '—';
        }

        if ($auth->relationLoaded('customer') || $auth->customer_id) {
            $profile = $auth->customer;
            if ($profile) {
                $label = self::customer($profile);
                if ($label !== '—') {
                    return $label;
                }
            }
        }

        return self::joinParts([$auth->email ?? null], 'Account #'.$auth->id);
    }

    public static function motorbike(?Motorbike $motorbike): string
    {
        if (! $motorbike) {
            return '—';
        }

        $makeModel = trim(($motorbike->make ?? '').' '.($motorbike->model ?? ''));

        return self::joinParts([
            $motorbike->reg_no ?? null,
            $makeModel ?: null,
            $motorbike->year ?? null,
            $motorbike->vin_number ?? null,
        ], 'Motorbike #'.$motorbike->id);
    }

    public static function branch(?Branch $branch): string
    {
        if (! $branch) {
            return '—';
        }

        return filled($branch->name) ? (string) $branch->name : 'Branch #'.$branch->id;
    }

    public static function customerAddress(?CustomerAddress $address): string
    {
        if (! $address) {
            return '—';
        }

        $name = trim(($address->first_name ?? '').' '.($address->last_name ?? ''));
        $street = trim(($address->street_address ?? '').' '.($address->street_address_plus ?? ''));

        return self::joinParts([
            $name ?: null,
            $street ?: null,
            $address->postcode ?? null,
            $address->city ?? null,
        ], 'Address #'.$address->id);
    }

    public static function dsOrder(?DsOrder $order): string
    {
        if (! $order) {
            return '—';
        }

        return self::joinParts([
            '#'.$order->id,
            $order->full_name ?? null,
            $order->phone ?? null,
            $order->postcode ?? null,
        ], 'DS order #'.$order->id);
    }

    public static function digitalInvoice(?NgnDigitalInvoice $invoice): string
    {
        if (! $invoice) {
            return '—';
        }

        return self::joinParts([
            $invoice->invoice_number ?: '#'.$invoice->id,
            $invoice->customer_name ?? null,
            $invoice->registration_number ?? null,
        ], 'Invoice #'.$invoice->id);
    }

    public static function user(?User $user): string
    {
        if (! $user) {
            return '—';
        }

        return self::joinParts([$user->name ?? null, $user->email ?? null], 'User #'.$user->id);
    }
}
