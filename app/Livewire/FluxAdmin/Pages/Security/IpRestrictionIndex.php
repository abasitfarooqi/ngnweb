<?php

namespace App\Livewire\FluxAdmin\Pages\Security;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\IpRestriction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('IP restrictions — Flux Admin')]
class IpRestrictionIndex extends Component
{
    use WithAuthorization;
    use WithCrudForm;
    use WithDataTable;
    use WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-security');
        $this->sortField = 'updated_at';
    }

    protected function formModel(): string
    {
        return IpRestriction::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.ip_address' => ['required', 'string', 'max:45'],
            'formData.status' => ['required', Rule::in(['allowed', 'blocked'])],
            'formData.restriction_type' => ['required', Rule::in(['admin_only', 'full_site'])],
            'formData.label' => ['nullable', 'string', 'max:255'],
            'formData.user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'ip_address' => '',
            'status' => 'blocked',
            'restriction_type' => 'full_site',
            'label' => '',
            'user_id' => null,
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $restriction = IpRestriction::findOrFail($id);
        $this->fillFromModel($restriction);
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: $this->recordId ? 'Restriction updated.' : 'Restriction created.');
    }

    public function delete(int $id): void
    {
        IpRestriction::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Restriction deleted.');
    }

    public function render()
    {
        $restrictions = $this->baseQuery()
            ->with('user:id,first_name,last_name,email')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.security.ip-restrictions-index', [
            'restrictions' => $restrictions,
        ]);
    }

    private function baseQuery(): Builder
    {
        return IpRestriction::query()
            ->when($this->search, function ($q): void {
                $q->where(function ($q): void {
                    $q->where('ip_address', 'like', "%{$this->search}%")
                        ->orWhere('label', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($this->filter('restriction_type'), fn ($q, $v) => $q->where('restriction_type', $v));
    }
}
