<?php

namespace App\Livewire\Portal\Recovery;

use App\Models\MotorbikeDeliveryOrderEnquiries;
use Livewire\Component;
use Livewire\WithPagination;

class MyRequests extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public function render()
    {
        $customerAuth = auth('customer')->user();
        $email = trim((string) ($customerAuth?->email ?? ''));
        $phone = trim((string) ($customerAuth?->customer?->phone ?? ''));

        $requests = MotorbikeDeliveryOrderEnquiries::query()
            ->with(['branch'])
            ->where(function ($query) use ($email, $phone) {
                if ($email !== '') {
                    $query->where('email', $email);
                }
                if ($phone !== '') {
                    $query->orWhere('phone', $phone);
                }
            })
            ->latest('id')
            ->paginate(12);

        return view('livewire.portal.recovery.my-requests', compact('requests'))
            ->layout('components.layouts.portal', ['title' => 'My Recovery Requests | My Account']);
    }
}
