<?php

namespace App\Livewire\Portal;

use App\Models\Ecommerce\EcPaymentMethod;
use Livewire\Component;

class PaymentMethods extends Component
{
    public function render()
    {
        $methods = EcPaymentMethod::active()
            ->where(function ($query): void {
                $query->whereIn('slug', ['paypal', 'cash', 'cash-on-branch', 'cash_on_branch'])
                    ->orWhereRaw('LOWER(title) LIKE ?', ['%paypal%'])
                    ->orWhereRaw('LOWER(title) LIKE ?', ['%cash%']);
            })
            ->get();

        return view('livewire.portal.payment-methods', compact('methods'))
            ->layout('components.layouts.portal', [
                'title' => 'Payment Methods | My Account',
            ]);
    }
}
