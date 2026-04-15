<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\RentingTransaction;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class TransactionsTab extends Component
{
    public int $bookingId;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        $transactions = RentingTransaction::with(['transactionType', 'paymentMethod', 'user'])
            ->where('booking_id', $this->bookingId)
            ->orderByDesc('transaction_date')
            ->get();

        return view('flux-admin.partials.rentals.transactions-tab', [
            'transactions' => $transactions,
        ]);
    }
}
