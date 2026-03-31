<?php

namespace App\Livewire\Portal\Recovery;

use App\Models\MotorbikeDeliveryOrderEnquiries;
use Livewire\Component;
use Livewire\WithPagination;

class MyRequests extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

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

    private function phoneSqlExpression(string $column = 'phone'): string
    {
        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM({$column}), ' ', ''), '+', ''), '-', ''), '(', ''), ')', '')";
    }

    public function render()
    {
        $customerAuth = auth('customer')->user();
        $email = $this->normaliseEmail($customerAuth?->email);
        $phone = $this->normaliseUkPhone($customerAuth?->customer?->phone);

        $query = MotorbikeDeliveryOrderEnquiries::query()
            ->with(['branch'])
            // Email must match on both sides; if it doesn't, do not show.
            ->whereRaw('LOWER(TRIM(email)) = ?', [$email]);

        if ($email === '' || $phone === '') {
            $query->whereRaw('1 = 0');
        } else {
            $sqlPhone = $this->phoneSqlExpression('phone');
            $phoneNoZero = ltrim($phone, '0');
            $phone44 = '44'.$phoneNoZero;
            $phonePlus44 = '+44'.$phoneNoZero;

            // Accept UK equivalent phone forms: 07..., 447..., +447...
            $query->where(function ($phoneQuery) use ($sqlPhone, $phone, $phone44, $phonePlus44) {
                $phoneQuery
                    ->whereRaw("{$sqlPhone} = ?", [$phone])
                    ->orWhereRaw("{$sqlPhone} = ?", [$phone44])
                    ->orWhereRaw("{$sqlPhone} = ?", [$phonePlus44]);
            });
        }

        $requests = $query->latest('id')->paginate(12);

        return view('livewire.portal.recovery.my-requests', compact('requests'))
            ->layout('components.layouts.portal', ['title' => 'My Recovery Requests | My Account']);
    }
}
