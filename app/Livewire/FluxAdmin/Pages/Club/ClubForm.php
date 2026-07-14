<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ClubMember;
use App\Models\NgnPartner;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Club Member — Flux Admin')]
class ClubForm extends Component
{
    use WithAuthorization;

    public ?int $clubMemberId = null;

    public array $form = [];

    public function mount(?ClubMember $clubMember = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-club');

        if ($clubMember !== null) {
            $this->clubMemberId = $clubMember->id;
            $attrs = $clubMember->getAttributes();
            $this->form = [
                'full_name' => $attrs['full_name'] ?? '',
                'email' => $attrs['email'] ?? '',
                'phone' => $attrs['phone'] ?? '',
                'vrm' => $attrs['vrm'] ?? '',
                'make' => $attrs['make'] ?? '',
                'model' => $attrs['model'] ?? '',
                'year' => $attrs['year'] ?? '',
                'passkey' => $attrs['passkey'] ?? '',
                'is_active' => (bool) ($attrs['is_active'] ?? true),
                'is_partner' => (bool) ($attrs['is_partner'] ?? false),
                'ngn_partner_id' => $attrs['ngn_partner_id'] ?? null,
                'email_sent' => (bool) ($attrs['email_sent'] ?? false),
                'tc_agreed' => (bool) ($attrs['tc_agreed'] ?? false),
            ];
        } else {
            $this->form = [
                'full_name' => '',
                'email' => '',
                'phone' => '',
                'vrm' => '',
                'make' => '',
                'model' => '',
                'year' => '',
                'passkey' => '',
                'is_active' => true,
                'is_partner' => false,
                'ngn_partner_id' => null,
                'email_sent' => false,
                'tc_agreed' => false,
            ];
        }
    }

    public function save(): void
    {
        $emailRule = Rule::unique('club_members', 'email');
        if ($this->clubMemberId) {
            $emailRule = $emailRule->ignore($this->clubMemberId);
        }

        $this->validate([
            'form.full_name' => ['required', 'string', 'max:200'],
            'form.email' => ['required', 'email', 'max:200', $emailRule],
            'form.phone' => ['required', 'string', 'max:15'],
            'form.vrm' => ['nullable', 'string', 'max:20'],
            'form.make' => ['nullable', 'string', 'max:100'],
            'form.model' => ['nullable', 'string', 'max:100'],
            'form.year' => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'form.passkey' => ['nullable', 'string', 'max:100'],
            'form.is_active' => ['boolean'],
            'form.is_partner' => ['boolean'],
            'form.ngn_partner_id' => ['nullable', 'integer', 'exists:ngn_partners,id'],
            'form.email_sent' => ['boolean'],
            'form.tc_agreed' => ['boolean'],
        ]);

        $data = [
            'full_name' => $this->form['full_name'],
            'email' => $this->form['email'],
            'phone' => $this->form['phone'],
            'vrm' => $this->form['vrm'] ?: null,
            'make' => $this->form['make'] ?: null,
            'model' => $this->form['model'] ?: null,
            'year' => $this->form['year'] ?: null,
            'passkey' => $this->form['passkey'] ?: null,
            'is_active' => (bool) ($this->form['is_active'] ?? false),
            'is_partner' => (bool) ($this->form['is_partner'] ?? false),
            'ngn_partner_id' => $this->form['ngn_partner_id'] ?: null,
            'email_sent' => (bool) ($this->form['email_sent'] ?? false),
            'tc_agreed' => (bool) ($this->form['tc_agreed'] ?? false),
            'user_id' => backpack_user()?->id ?? auth()->id(),
        ];

        if ($this->clubMemberId) {
            ClubMember::findOrFail($this->clubMemberId)->update($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Club member updated.');
        } else {
            ClubMember::create($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Club member created.');
        }

        $this->redirect(route('flux-admin.club.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.club.form', [
            'partners' => NgnPartner::orderBy('companyname')->get(['id', 'companyname']),
        ]);
    }
}
