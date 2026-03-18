<?php

namespace App\Livewire\Portal;

use App\Models\Ecommerce\EcPaymentMethod;
use Livewire\Component;

class PaymentMethods extends Component
{
    public function render()
    {
        $methods = EcPaymentMethod::active()->get();

        return view('livewire.portal.payment-methods', compact('methods'))
            ->layout('components.layouts.portal', [
                'title' => 'Payment Methods | My Account',
            ]);
    }
}
