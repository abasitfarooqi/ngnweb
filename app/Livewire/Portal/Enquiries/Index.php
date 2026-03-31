<?php

namespace App\Livewire\Portal\Enquiries;

use App\Models\ServiceBooking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $customerAuth = Auth::guard('customer')->user();

        if (! $customerAuth) {
            abort(403);
        }

        $customerId = $customerAuth->customer_id;

        $enquiries = ServiceBooking::query()
            ->where(function ($query) use ($customerAuth, $customerId): void {
                if ($customerId) {
                    $query->orWhere('customer_id', $customerId);
                }
                if ($customerAuth->id) {
                    $query->orWhere('customer_auth_id', $customerAuth->id);
                }
            })
            ->latest()
            ->paginate(12);

        return view('livewire.portal.enquiries.index', compact('enquiries'))
            ->layout('components.layouts.portal', [
                'title' => 'My Enquiries | My Account',
            ]);
    }
}
