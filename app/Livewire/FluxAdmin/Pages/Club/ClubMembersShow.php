<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Models\ClubMember;
use App\Models\NgnPartner;
use App\Support\ClubMemberStaffAccess;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class ClubMembersShow extends Component
{
    public ClubMember $clubMember;

    public string $activeTab = 'spendings';

    public array $vehicleForm = [];

    public function mount(ClubMember $clubMember): void
    {
        if (! ClubMemberStaffAccess::canAccessPortal()) {
            throw new AuthorizationException('You do not have permission to access this section.');
        }

        $this->clubMember = $clubMember->load('customer', 'partner', 'user', 'purchases', 'redemptions', 'spendings');
        $this->vehicleForm = [
            'vrm' => (string) ($clubMember->vrm ?? ''),
            'make' => (string) ($clubMember->make ?? ''),
            'model' => (string) ($clubMember->model ?? ''),
            'year' => (string) ($clubMember->year ?? ''),
            'is_partner' => (bool) $clubMember->is_partner,
            'ngn_partner_id' => $clubMember->ngn_partner_id,
        ];
    }

    public function getTitle(): string
    {
        $name = trim((string) $this->clubMember->full_name);

        return ($name !== '' && $name !== '-' ? $name : 'Club member #'.$this->clubMember->id).' — Flux Admin';
    }

    public function saveVehicle(): void
    {
        if ($this->vehicleForm['year'] === '') {
            $this->vehicleForm['year'] = null;
        }

        $this->validate([
            'vehicleForm.vrm' => ['nullable', 'string', 'max:20'],
            'vehicleForm.make' => ['nullable', 'string', 'max:100'],
            'vehicleForm.model' => ['nullable', 'string', 'max:100'],
            'vehicleForm.year' => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'vehicleForm.is_partner' => ['boolean'],
            'vehicleForm.ngn_partner_id' => ['nullable', 'integer', 'exists:ngn_partners,id'],
        ]);

        $this->clubMember->update([
            'vrm' => $this->vehicleForm['vrm'] ?: null,
            'make' => $this->vehicleForm['make'] ?: null,
            'model' => $this->vehicleForm['model'] ?: null,
            'year' => $this->vehicleForm['year'] ?: null,
            'is_partner' => (bool) ($this->vehicleForm['is_partner'] ?? false),
            'ngn_partner_id' => $this->vehicleForm['ngn_partner_id'] ?: null,
            'user_id' => backpack_user()?->id ?? auth()->id(),
        ]);

        $this->clubMember->refresh()->load('partner');
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Vehicle and partner details updated.');
    }

    public function render()
    {
        return view('flux-admin.pages.club.members-show', [
            'partners' => NgnPartner::orderBy('companyname')->get(['id', 'companyname']),
        ]);
    }
}
