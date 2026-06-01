<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\ClaimMotorbike;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Motorbike claims — Flux Admin')]
class ClaimIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'case_date';
    }

    protected function formModel(): string
    {
        return ClaimMotorbike::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.fullname'       => ['required', 'string', 'max:255'],
            'formData.email'          => ['nullable', 'email', 'max:255'],
            'formData.phone'          => ['nullable', 'string', 'max:50'],
            'formData.case_date'      => ['required', 'date'],
            'formData.motorbike_id'   => ['nullable', 'integer'],
            'formData.branch_id'      => ['nullable', 'integer'],
            'formData.notes'          => ['nullable', 'string'],
            'formData.is_received'    => ['boolean'],
            'formData.received_date'  => ['nullable', 'date'],
            'formData.is_returned'    => ['boolean'],
            'formData.returned_date'  => ['nullable', 'date'],
            'formData.user_id'        => ['nullable', 'integer'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'case_date'   => now()->toDateString(),
            'user_id'     => backpack_user()->id,
            'is_received' => false,
            'is_returned' => false,
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(ClaimMotorbike::findOrFail($id));
        foreach (['case_date', 'received_date', 'returned_date'] as $field) {
            if (!empty($this->formData[$field])) {
                $this->formData[$field] = \Carbon\Carbon::parse($this->formData[$field])->format('Y-m-d');
            }
        }
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        ClaimMotorbike::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = ClaimMotorbike::query()
            ->with(['motorbike:id,reg_no', 'branch:id,name'])
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('fullname', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%")->orWhere('phone', 'like', "%{$v}%")->orWhereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$v}%"))))
            ->when($this->filter('is_received') !== '', fn ($q) => $q->where('is_received', $this->filter('is_received') === '1'))
            ->when($this->filter('is_returned') !== '', fn ($q) => $q->where('is_returned', $this->filter('is_returned') === '1'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.vehicles.claims-index', ['rows' => $rows]);
    }
}
