<?php

namespace App\Livewire\Portal\Enquiries;

use App\Models\ServiceBooking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $activeFilter = 'all';

    public function setFilter(string $filter): void
    {
        $this->activeFilter = $filter;
        $this->resetPage();
    }

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
            ->when($this->activeFilter !== 'all', function ($query): void {
                $mapped = match ($this->activeFilter) {
                    'mot' => ['mot'],
                    'rentals' => ['rental'],
                    'finance' => ['finance'],
                    'shop' => ['shop', 'new_bike', 'used_bike'],
                    'recovery' => ['recovery_delivery'],
                    'ebike' => ['e_bike'],
                    default => [],
                };

                if (! empty($mapped)) {
                    $query->whereIn('enquiry_type', $mapped);
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
