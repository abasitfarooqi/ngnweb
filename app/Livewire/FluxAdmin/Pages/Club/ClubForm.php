<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Models\ClubMember;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Club Member — Flux Admin')]
class ClubForm extends Component
{
    public ?int $clubMemberId = null;

    public array $form = [];

    public function mount(?ClubMember $clubMember = null): void
    {
        $this->resetErrorBag();
        if ($clubMember !== null) {
            $this->clubMemberId = $clubMember->id;
            $attrs = $clubMember->getAttributes();
            $this->form = [
                'full_name'  => $attrs['full_name'] ?? '',
                'email'      => $attrs['email'] ?? '',
                'phone'      => $attrs['phone'] ?? '',
                'vrm'        => $attrs['vrm'] ?? '',
                'make'       => $attrs['make'] ?? '',
                'model'      => $attrs['model'] ?? '',
                'year'       => $attrs['year'] ?? '',
                'passkey'    => $attrs['passkey'] ?? '',
                'is_active'  => (bool) ($attrs['is_active'] ?? true),
                'is_partner' => (bool) ($attrs['is_partner'] ?? false),
            ];
        } else {
            $this->form = [
                'full_name'  => '',
                'email'      => '',
                'phone'      => '',
                'vrm'        => '',
                'make'       => '',
                'model'      => '',
                'year'       => '',
                'passkey'    => '',
                'is_active'  => true,
                'is_partner' => false,
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.full_name'  => ['required', 'string', 'max:200'],
            'form.email'      => ['nullable', 'email', 'max:200'],
            'form.phone'      => ['nullable', 'string', 'max:50'],
            'form.vrm'        => ['nullable', 'string', 'max:20'],
            'form.make'       => ['nullable', 'string', 'max:100'],
            'form.model'      => ['nullable', 'string', 'max:100'],
            'form.year'       => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'form.passkey'    => ['nullable', 'string', 'max:100'],
            'form.is_active'  => ['boolean'],
            'form.is_partner' => ['boolean'],
        ]);

        $data = [
            'full_name'  => $this->form['full_name'],
            'email'      => $this->form['email'] ?: null,
            'phone'      => $this->form['phone'] ?: null,
            'vrm'        => $this->form['vrm'] ?: null,
            'make'       => $this->form['make'] ?: null,
            'model'      => $this->form['model'] ?: null,
            'year'       => $this->form['year'] ?: null,
            'passkey'    => $this->form['passkey'] ?: null,
            'is_active'  => (bool) ($this->form['is_active'] ?? false),
            'is_partner' => (bool) ($this->form['is_partner'] ?? false),
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
        return view('flux-admin.pages.club.form');
    }
}
