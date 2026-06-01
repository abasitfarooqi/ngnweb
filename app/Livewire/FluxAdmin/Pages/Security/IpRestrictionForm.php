<?php

namespace App\Livewire\FluxAdmin\Pages\Security;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\IpRestriction;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('IP Restriction — Flux Admin')]
class IpRestrictionForm extends Component
{
    use WithAuthorization;

    public ?IpRestriction $ipRestriction = null;

    public array $form = [];

    public function mount(?IpRestriction $ipRestriction = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-security');
        $this->ipRestriction = $ipRestriction?->id ? $ipRestriction : null;

        if ($this->ipRestriction) {
            $this->form = $this->ipRestriction->getAttributes();
        } else {
            $this->form = [
                'ip_address'       => '',
                'status'           => 'blocked',
                'restriction_type' => 'full_site',
                'label'            => '',
                'user_id'          => null,
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.ip_address'       => ['required', 'string', 'max:45'],
            'form.status'           => ['required', Rule::in(['allowed', 'blocked'])],
            'form.restriction_type' => ['required', Rule::in(['admin_only', 'full_site'])],
            'form.label'            => ['nullable', 'string', 'max:255'],
            'form.user_id'          => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $payload = [
            'ip_address'       => $this->form['ip_address'],
            'status'           => $this->form['status'],
            'restriction_type' => $this->form['restriction_type'],
            'label'            => $this->form['label'] ?: null,
            'user_id'          => $this->form['user_id'] ?: null,
        ];

        if ($this->ipRestriction) {
            $this->ipRestriction->update($payload);
        } else {
            IpRestriction::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'IP restriction saved.');
        $this->redirect(route('flux-admin.ip-restrictions.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.security.ip-restriction-form');
    }
}
