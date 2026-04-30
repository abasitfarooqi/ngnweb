<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\ClubMember;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Club member vehicles — Flux Admin')]
class MemberVehicleIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'club-member-vehicles';
        $this->sortField = 'full_name';
        $this->sortDirection = 'asc';
    }

    protected function formModel(): string { return ClubMember::class; }

    protected function formRules(): array
    {
        return [
            'formData.vrm' => ['nullable', 'string', 'max:20'],
            'formData.make' => ['nullable', 'string', 'max:120'],
            'formData.model' => ['nullable', 'string', 'max:120'],
            'formData.year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
        ];
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(ClubMember::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.club.member-vehicle-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return ClubMember::query()
            ->when($this->search, function ($q, $v) {
                $q->where(function ($qq) use ($v) {
                    $qq->where('full_name', 'like', "%{$v}%")
                        ->orWhere('email', 'like', "%{$v}%")
                        ->orWhere('phone', 'like', "%{$v}%")
                        ->orWhere('vrm', 'like', "%{$v}%")
                        ->orWhere('make', 'like', "%{$v}%")
                        ->orWhere('model', 'like', "%{$v}%");
                });
            });
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'Member' => 'full_name', 'Email' => 'email', 'Phone' => 'phone',
            'VRM' => 'vrm', 'Make' => 'make', 'Model' => 'model', 'Year' => 'year',
        ];
    }
}
