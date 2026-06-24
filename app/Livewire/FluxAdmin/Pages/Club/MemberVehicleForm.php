<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ClubMember;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Club member vehicle — Flux Admin')]
class MemberVehicleForm extends Component
{
    use WithAuthorization;

    public ClubMember $clubMember;

    public array $form = [];

    public function mount(ClubMember $clubMember): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->clubMember = $clubMember;
        $this->form = $clubMember->only(['full_name', 'vrm', 'make', 'model', 'year']);
    }

    public function save(): void
    {
        $this->validate([
            'form.vrm' => ['nullable', 'string', 'max:20'],
            'form.make' => ['nullable', 'string', 'max:120'],
            'form.model' => ['nullable', 'string', 'max:120'],
            'form.year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
        ]);

        $this->clubMember->update(collect($this->form)->only(['vrm', 'make', 'model', 'year'])->all());
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Vehicle details updated.');
        $this->redirect(route('flux-admin.club-member-vehicles.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.club.member-vehicle-form');
    }
}
