<?php

namespace App\Livewire\FluxAdmin\Pages\Customers;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Customer;
use App\Support\CustomerPortalCredentialIssuer;
use App\Support\FluxAdminAccess;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Portal users — Flux Admin')]
class PortalUserIndex extends Component
{
    use WithAuthorization;
    use WithPagination;

    #[Url(history: true, except: '')]
    public string $search = '';

    #[Url(history: true, except: 'all')]
    public string $viewFilter = 'all';

    #[Url(history: true, except: 'all')]
    public string $accountFilter = 'all';

    #[Url(history: true, except: 'all')]
    public string $portalFilter = 'all';

    #[Url(history: true, except: 'all')]
    public string $uploadFilter = 'all';

    #[Url(history: true, except: 'all')]
    public string $verificationFilter = 'all';

    public ?int $selectedCustomerId = null;
    public string $newPassword = '';
    public string $newPassword_confirmation = '';

    public function mount(): void
    {
        if (! FluxAdminAccess::canManagePortalUsers()) {
            abort(403);
        }
    }

    public function toggleCustomer(int $id): void
    {
        $customer = Customer::query()->with('customerAuth')->findOrFail($id);
        $customer->forceFill(['is_active' => ! (bool) $customer->is_active])->save();
        if ($customer->customerAuth) {
            $customer->customerAuth->forceFill(['is_active' => (bool) $customer->is_active])->save();
        }
        $this->dispatch('flux-admin:toast', type: 'success', message: $customer->is_active ? 'Customer activated.' : 'Customer deactivated.');
    }

    public function togglePortal(int $id): void
    {
        $customer = Customer::query()->with('customerAuth')->findOrFail($id);
        if (! $customer->customerAuth) {
            $this->dispatch('flux-admin:toast', type: 'danger', message: 'Create portal access first by sending credentials.');
            return;
        }
        $active = ! (bool) $customer->customerAuth->is_active;
        $customer->customerAuth->forceFill(['is_active' => $active])->save();
        $customer->forceFill(['is_register' => $active])->save();
        $this->dispatch('flux-admin:toast', type: 'success', message: $active ? 'Portal activated.' : 'Portal deactivated.');
    }

    public function toggleUploadAccess(int $id): void
    {
        $customer = Customer::query()->findOrFail($id);
        $customer->forceFill(['portal_upload_access' => ! (bool) $customer->portal_upload_access])->save();
        $this->dispatch('flux-admin:toast', type: 'success', message: $customer->portal_upload_access ? 'Document upload access granted.' : 'Document upload access removed.');
    }

    public function sendCredentials(int $id): void
    {
        $customer = Customer::query()->findOrFail($id);
        if (! CustomerPortalCredentialIssuer::issueAndNotify($customer)) {
            $this->dispatch('flux-admin:toast', type: 'danger', message: 'Customer has no email address.');
            return;
        }
        $this->dispatch('flux-admin:toast', type: 'success', message: 'New credentials sent by email and SMS.');
    }

    public function setPassword(int $id): void
    {
        $this->validate(['newPassword' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()]], [
            'newPassword.confirmed' => 'The password confirmation does not match.',
        ]);
        $customer = Customer::query()->with('customerAuth')->findOrFail($id);
        if (! $customer->customerAuth) {
            $this->addError('newPassword', 'Create portal access before setting a password.');
            return;
        }
        $customer->customerAuth->forceFill(['password' => Hash::make($this->newPassword), 'is_active' => true])->save();
        $customer->forceFill(['is_register' => true])->save();
        $this->reset('newPassword', 'newPassword_confirmation', 'selectedCustomerId');
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Password updated. Use Send credentials to deliver it securely.');
    }

    public function render()
    {
        $customers = Customer::query()->with('customerAuth')
            ->when(trim($this->search) !== '', function ($query): void {
                $term = '%'.trim($this->search).'%';
                $query->where(function ($q) use ($term): void {
                    foreach (['first_name', 'last_name', 'email', 'phone', 'whatsapp', 'address', 'postcode', 'city', 'country', 'nationality', 'emergency_contact', 'license_number', 'license_issuance_authority', 'license_issuance_date', 'license_expiry_date', 'reputation_note', 'verification_status'] as $field) {
                        $q->orWhere($field, 'like', $term);
                    }
                    if (ctype_digit(trim($this->search))) {
                        $q->orWhere('id', (int) trim($this->search));
                    }
                });
            })
            ->when($this->viewFilter === 'portal', fn ($query) => $query->where('is_register', true)->whereHas('customerAuth'))
            ->when($this->viewFilter === 'registered', fn ($query) => $query->where('is_register', true))
            ->when($this->viewFilter === 'normal', fn ($query) => $query->where(function ($q): void {
                $q->where('is_register', false)->orWhereDoesntHave('customerAuth');
            }))
            ->when($this->viewFilter === 'inactive', fn ($query) => $query->where(function ($q): void {
                $q->where('is_active', false)->orWhereHas('customerAuth', fn ($auth) => $auth->where('is_active', false));
            }))
            ->when($this->accountFilter === 'active', fn ($query) => $query->where('is_active', true))
            ->when($this->accountFilter === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($this->portalFilter === 'active', fn ($query) => $query->where('is_register', true)->whereHas('customerAuth', fn ($auth) => $auth->where('is_active', true)))
            ->when($this->portalFilter === 'inactive', fn ($query) => $query->where(function ($q): void {
                $q->where('is_register', false)->orWhereDoesntHave('customerAuth')->orWhereHas('customerAuth', fn ($auth) => $auth->where('is_active', false));
            }))
            ->when($this->uploadFilter === 'allowed', fn ($query) => $query->where('portal_upload_access', true))
            ->when($this->uploadFilter === 'disabled', fn ($query) => $query->where('portal_upload_access', false))
            ->when($this->verificationFilter !== 'all', fn ($query) => $this->verificationFilter === 'verified'
                ? $query->whereHas('customerAuth', fn ($auth) => $auth->whereNotNull('email_verified_at'))
                : $query->where(function ($q): void {
                    $q->whereDoesntHave('customerAuth')->orWhereHas('customerAuth', fn ($auth) => $auth->whereNull('email_verified_at'));
                }))
            ->orderBy('first_name')->orderBy('last_name')->paginate(25);

        return view('flux-admin.pages.customers.portal-user-index', compact('customers'));
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'viewFilter', 'accountFilter', 'portalFilter', 'uploadFilter', 'verificationFilter');
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedViewFilter(): void { $this->resetPage(); }
    public function updatedAccountFilter(): void { $this->resetPage(); }
    public function updatedPortalFilter(): void { $this->resetPage(); }
    public function updatedUploadFilter(): void { $this->resetPage(); }
    public function updatedVerificationFilter(): void { $this->resetPage(); }
}
