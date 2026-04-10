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

        $enquiries = ServiceBooking::query()
            ->forPortalCustomer($customerAuth)
            ->with('conversation')
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
                    if ($this->activeFilter === 'rentals') {
                        $query->whereRentalEnquiry();
                    } else {
                        $query->whereIn('enquiry_type', $mapped);
                    }
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
