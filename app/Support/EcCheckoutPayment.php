<?php

namespace App\Support;

use App\Models\Ecommerce\EcPaymentMethod;
use Illuminate\Database\Eloquent\Builder;

/** Shop checkout payment method helpers (PayPal vs pay in store / cash). */
final class EcCheckoutPayment
{
    /** @return Builder<EcPaymentMethod> */
    public static function checkoutMethodsQuery(): Builder
    {
        return EcPaymentMethod::active()
            ->where(function ($query): void {
                $query->whereIn('slug', [
                    'paypal',
                    'pay-on-store',
                    'pay_on_store',
                    'in-store-payment',
                    'in_store_payment',
                    'cash',
                    'cash-on-branch',
                    'cash_on_branch',
                ])
                    ->orWhereRaw('LOWER(title) LIKE ?', ['%paypal%'])
                    ->orWhereRaw('LOWER(title) LIKE ?', ['%pay on store%'])
                    ->orWhereRaw('LOWER(title) LIKE ?', ['%in store%'])
                    ->orWhereRaw('LOWER(title) LIKE ?', ['%cash%']);
            })
            ->orderBy('id');
    }

    public static function isPayPal(EcPaymentMethod $method): bool
    {
        $slug = strtolower(trim((string) $method->slug));
        $title = strtolower(trim((string) $method->title));

        return str_contains($slug, 'paypal') || str_contains($title, 'paypal');
    }

    public static function isOffline(EcPaymentMethod $method): bool
    {
        if (self::isPayPal($method)) {
            return false;
        }

        $slug = strtolower(trim((string) $method->slug));
        $title = strtolower(trim((string) $method->title));

        return in_array($slug, [
            'pay-on-store',
            'pay_on_store',
            'in-store-payment',
            'in_store_payment',
            'cash',
            'cash-on-branch',
            'cash_on_branch',
        ], true)
            || str_contains($title, 'pay on store')
            || str_contains($title, 'in store')
            || str_contains($title, 'cash');
    }
}
