<?php

namespace App\Livewire\Portal\Recovery;

use App\Models\MotorbikeDeliveryOrderEnquiries;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;

class Show extends Component
{
    public MotorbikeDeliveryOrderEnquiries $request;

    private function normaliseEmail(?string $email): string
    {
        return mb_strtolower(trim((string) $email));
    }

    private function normaliseUkPhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';
        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '44')) {
            $digits = '0'.substr($digits, 2);
        }

        if (! str_starts_with($digits, '0') && strlen($digits) >= 10) {
            $digits = '0'.$digits;
        }

        return $digits;
    }

    public function mount(int $requestId): void
    {
        $record = MotorbikeDeliveryOrderEnquiries::query()
            ->with(['branch'])
            ->findOrFail($requestId);

        $customerAuth = auth('customer')->user();
        $email = $this->normaliseEmail($customerAuth?->email);
        $phone = $this->normaliseUkPhone($customerAuth?->customer?->phone);
        $recordEmail = $this->normaliseEmail((string) $record->email);
        $recordPhone = $this->normaliseUkPhone((string) $record->phone);

        // Strict access: email must match exactly + phone must match after UK normalisation.
        $isOwner = $email !== '' && $phone !== '' && $recordEmail === $email && $recordPhone === $phone;

        if (! $isOwner) {
            throw (new ModelNotFoundException)->setModel(MotorbikeDeliveryOrderEnquiries::class);
        }

        $this->request = $record;
    }

    public function render()
    {
        return view('livewire.portal.recovery.show')
            ->layout('components.layouts.portal', ['title' => 'Recovery Request | My Account']);
    }
}
