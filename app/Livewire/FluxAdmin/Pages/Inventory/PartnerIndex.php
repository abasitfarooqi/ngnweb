<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\NgnPartner;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Partners — Flux Admin')]
class PartnerIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return NgnPartner::class; }

    protected function formRules(): array
    {
        return [
            'formData.companyname' => ['required', 'string', 'max:255'],
            'formData.company_address' => ['nullable', 'string', 'max:500'],
            'formData.company_number' => ['nullable', 'string', 'max:50'],
            'formData.first_name' => ['nullable', 'string', 'max:120'],
            'formData.last_name' => ['nullable', 'string', 'max:120'],
            'formData.phone' => ['nullable', 'string', 'max:40'],
            'formData.mobile' => ['nullable', 'string', 'max:40'],
            'formData.email' => ['nullable', 'email', 'max:255'],
            'formData.website' => ['nullable', 'string', 'max:255'],
            'formData.fleet_size' => ['nullable', 'integer', 'min:0'],
            'formData.operating_since' => ['nullable', 'date'],
            'formData.is_approved' => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['is_approved' => false];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(NgnPartner::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Partner saved.');
    }

    public function delete(int $id): void
    {
        NgnPartner::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = NgnPartner::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('companyname', 'like', "%{$v}%")->orWhere('first_name', 'like', "%{$v}%")->orWhere('last_name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%")->orWhere('phone', 'like', "%{$v}%")))
            ->when($this->filter('is_approved') !== '', fn ($q) => $q->where('is_approved', $this->filter('is_approved') === '1'))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.inventory.partners-index', ['rows' => $rows]);
    }
}
