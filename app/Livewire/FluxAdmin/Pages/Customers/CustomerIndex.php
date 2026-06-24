<?php

namespace App\Livewire\FluxAdmin\Pages\Customers;

use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Branch;
use App\Models\Customer;
use App\Support\CustomerPortalCredentialIssuer;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Customers — Flux Admin')]
class CustomerIndex extends Component
{
    use WithCrudForm;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public bool $showForm = false;

    public string $filterVerification = '';

    public string $filterClub = '';

    public function mount(): void
    {
        $this->exportFilename = 'customers';
        $this->sortField = 'id';
        $this->sortDirection = 'desc';
    }

    protected function formModel(): string
    {
        return Customer::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.first_name'               => ['required', 'string', 'max:100'],
            'formData.last_name'                => ['required', 'string', 'max:100'],
            'formData.email'                    => ['nullable', 'email', 'max:200'],
            'formData.phone'                    => ['nullable', 'string', 'max:50'],
            'formData.whatsapp'                 => ['nullable', 'string', 'max:50'],
            'formData.dob'                      => ['nullable', 'date'],
            'formData.address'                  => ['nullable', 'string', 'max:500'],
            'formData.postcode'                 => ['nullable', 'string', 'max:20'],
            'formData.city'                     => ['nullable', 'string', 'max:100'],
            'formData.country'                  => ['nullable', 'string', 'max:100'],
            'formData.nationality'              => ['nullable', 'string', 'max:100'],
            'formData.emergency_contact'        => ['nullable', 'string', 'max:100'],
            'formData.license_number'           => ['nullable', 'string', 'max:100'],
            'formData.license_issuance_date'    => ['nullable', 'date'],
            'formData.license_expiry_date'      => ['nullable', 'date'],
            'formData.license_issuance_authority' => ['nullable', 'string', 'max:100'],
            'formData.reputation_note'          => ['nullable', 'string', 'max:2000'],
            'formData.rating'                   => ['nullable', 'integer', 'min:1', 'max:5'],
            'formData.preferred_branch_id'      => ['nullable', 'integer'],
            'formData.verification_status'      => ['nullable', 'string', 'in:verified,pending,rejected,unverified'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['verification_status' => 'unverified'];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $customer = Customer::findOrFail($id);
        $this->fillFromModel($customer);
        // Format date fields as Y-m-d for HTML date inputs
        foreach (['dob', 'license_issuance_date', 'license_expiry_date'] as $field) {
            if (! empty($this->formData[$field])) {
                try {
                    $this->formData[$field] = \Carbon\Carbon::parse($this->formData[$field])->format('Y-m-d');
                } catch (\Throwable) {
                    $this->formData[$field] = null;
                }
            }
        }
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Customer saved.');
    }

    public function deleteCustomer(int $id): void
    {
        Customer::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Customer deleted.');
    }

    public function render()
    {
        $customers = $this->baseQuery()->with('preferredBranch')->paginate($this->perPage);
        $branches  = Branch::orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.customers.index', compact('customers', 'branches'));
    }

    protected function baseQuery(): Builder
    {
        return Customer::query()
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->search}%"]);
                });
            })
            ->when($this->filterVerification !== '', fn ($q) => $q->where('verification_status', $this->filterVerification))
            ->when($this->filterClub !== '', fn ($q) => $q->where('is_club', $this->filterClub === '1'))
            ->orderBy($this->sortField, $this->sortDirection);
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery();
    }

    protected function exportColumns(): array
    {
        return [
            'ID'             => 'id',
            'First name'     => 'first_name',
            'Last name'      => 'last_name',
            'Email'          => 'email',
            'Phone'          => 'phone',
            'DOB'            => fn ($c) => $c->dob?->format('d/m/Y') ?? '',
            'Address'        => 'address',
            'Portal active'  => fn ($c) => $c->is_register ? 'Yes' : 'No',
            'Rating'         => 'rating',
            'Created at'     => fn ($c) => $c->created_at?->format('d/m/Y H:i') ?? '',
        ];
    }

    public function sendPortalCredentials(int $customerId): void
    {
        $customer = Customer::findOrFail($customerId);

        if (! CustomerPortalCredentialIssuer::issueAndNotify($customer)) {
            $this->dispatch('flux-admin:toast', type: 'danger', message: 'Customer has no email address.');

            return;
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Portal credentials sent via email and SMS.');
    }

    public function updatingFilterVerification(): void
    {
        $this->resetPage();
    }

    public function updatingFilterClub(): void
    {
        $this->resetPage();
    }
}
