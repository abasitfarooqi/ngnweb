<?php

namespace App\Livewire\Portal\Recovery;

use App\Models\MotorbikeDeliveryOrderEnquiries;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;

class Show extends Component
{
    public MotorbikeDeliveryOrderEnquiries $request;

    public function mount(int $requestId): void
    {
        $record = MotorbikeDeliveryOrderEnquiries::query()
            ->with(['branch'])
            ->findOrFail($requestId);

        $customerAuth = auth('customer')->user();
        $email = trim((string) ($customerAuth?->email ?? ''));
        $phone = trim((string) ($customerAuth?->customer?->phone ?? ''));
        $isOwner = ($email !== '' && strcasecmp((string) $record->email, $email) === 0)
            || ($phone !== '' && trim((string) $record->phone) === $phone);

        if (! $isOwner) {
            throw (new ModelNotFoundException())->setModel(MotorbikeDeliveryOrderEnquiries::class);
        }

        $this->request = $record;
    }

    public function render()
    {
        return view('livewire.portal.recovery.show')
            ->layout('components.layouts.portal', ['title' => 'Recovery Request | My Account']);
    }
}
