<?php

namespace App\Livewire\FluxAdmin\Pages\Customers;

use App\Models\Customer;
use App\Support\FluxAdminAccess;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('flux-admin.layouts.app')]
#[Title('Portal user editor — Flux Admin')]
class PortalUserEdit extends CustomerForm
{
    public string $portalPassword = '';
    public string $portalPassword_confirmation = '';

    public function mount(?Customer $customer = null): void
    {
        if (! FluxAdminAccess::canManagePortalUsers()) {
            abort(403);
        }
        if (! $customer) {
            abort(404);
        }

        parent::mount($customer);
        $this->customer->loadMissing('customerAuth');
    }

    public function isPortalUserEditor(): bool
    {
        return true;
    }

    public function save(): void
    {
        $this->guardPortalEditor();
        parent::save();
    }

    public function setProfileEditingUnlocked(bool $unlocked): void
    {
        $this->guardPortalEditor();
        parent::setProfileEditingUnlocked($unlocked);
    }

    public function setDocumentReuploadUnlocked(bool $unlocked): void
    {
        $this->guardPortalEditor();
        parent::setDocumentReuploadUnlocked($unlocked);
    }

    public function setPortalPassword(): void
    {
        $this->guardPortalEditor();
        $this->validate(['portalPassword' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()]], [
            'portalPassword.confirmed' => 'The password confirmation does not match.',
        ]);

        $this->customer?->loadMissing('customerAuth');
        if (! $this->customer?->customerAuth) {
            $this->addError('portalPassword', 'Create portal access by sending credentials first.');
            return;
        }

        $this->customer->customerAuth->forceFill([
            'password' => Hash::make($this->portalPassword),
            'is_active' => true,
        ])->save();
        $this->customer->forceFill(['is_register' => true])->save();
        $this->reset('portalPassword', 'portalPassword_confirmation');
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Portal password updated. Send credentials to deliver the new password.');
    }

    public function sendPortalResetLink(string $channel = 'email'): void
    {
        $this->guardPortalEditor();
        if (! in_array($channel, ['email', 'sms', 'both'], true) || ! $this->customer || ! CustomerPortalCredentialIssuer::sendResetLink($this->customer, $channel)) {
            $this->dispatch('flux-admin:toast', type: 'danger', message: 'Portal email/password record is not available.');
            return;
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: $channel === 'sms' ? 'Password reset link sent by SMS.' : 'Password reset link sent by email.');
    }

    private function guardPortalEditor(): void
    {
        if (! FluxAdminAccess::canManagePortalUsers()) {
            abort(403);
        }
    }
}
