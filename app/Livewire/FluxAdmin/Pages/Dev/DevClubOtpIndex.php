<?php

namespace App\Livewire\FluxAdmin\Pages\Dev;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\OtpVerification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Club OTP viewer — Flux Admin')]
class DevClubOtpIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void { $this->authorizeModule('see-menu-admin'); }

    public function render()
    {
        $rows = OtpVerification::query()
            ->with(['clubMember:id,first_name,last_name,email,phone'])
            ->when($this->search, fn ($q, $v) => $q->whereHas('clubMember', fn ($q) => $q->where('first_name', 'like', "%{$v}%")->orWhere('last_name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%")->orWhere('phone', 'like', "%{$v}%")))
            ->when($this->filter('is_used') !== '', fn ($q) => $q->where('is_used', $this->filter('is_used') === '1'))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.dev.club-otp', ['rows' => $rows]);
    }
}
