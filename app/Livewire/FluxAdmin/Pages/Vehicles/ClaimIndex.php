<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\ClaimMotorbike;
use App\Support\FluxAdminFormPayload;
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
            'formData.email'          => ['required', 'email', 'max:255'],
            'formData.phone'          => ['required', 'string', 'max:50'],
            'formData.case_date'      => ['required', 'date'],
            'formData.motorbike_id'   => ['required', 'integer', 'exists:motorbikes,id'],
            'formData.branch_id'      => ['required', 'integer', 'exists:branches,id'],
            'formData.notes'          => ['nullable', 'string'],
            'formData.is_received'    => ['boolean'],
            'formData.received_date'  => ['nullable', 'date'],
            'formData.is_returned'    => ['boolean'],
            'formData.returned_date'  => ['nullable', 'date'],
            'formData.user_id'        => ['nullable', 'integer'],
        ];
    }

    protected function beforeSave(array $attributes): array
    {
        if (empty($attributes['user_id'])) {
            $attributes['user_id'] = FluxAdminFormPayload::adminUserId();
        }

        if (trim((string) ($attributes['notes'] ?? '')) === '') {
            $attributes['notes'] = '—';
        }

        return $attributes;
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'case_date'   => now()->toDateString(),
            'user_id'     => FluxAdminFormPayload::adminUserId(),
            'is_received' => false,
            'is_returned' => false,
            'email'       => '',
            'phone'       => '',
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
