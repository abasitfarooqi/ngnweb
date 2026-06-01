<?php

namespace App\Livewire\FluxAdmin\Pages\Finance;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\ApplicationItem;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Finance application items — Flux Admin')]
class ApplicationItemIndex extends Component
{
    use WithAuthorization;
    use WithCrudForm;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-finance-applications');
        $this->exportable = true;
        $this->exportFilename = 'application-items';
    }

    protected function formModel(): string { return ApplicationItem::class; }

    protected function formRules(): array
    {
        return [
            'formData.application_id'    => ['required', 'integer'],
            'formData.motorbike_id'      => ['required', 'integer'],
            'formData.start_date'        => ['nullable', 'date'],
            'formData.due_date'          => ['nullable', 'date'],
            'formData.end_date'          => ['nullable', 'date'],
            'formData.weekly_instalment' => ['nullable', 'numeric', 'min:0'],
            'formData.is_posted'         => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['is_posted' => false];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(ApplicationItem::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->formData['is_posted'] = (bool) ($this->formData['is_posted'] ?? false);
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Application item saved.');
    }

    public function delete(int $id): void
    {
        ApplicationItem::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Application item deleted.');
    }

    public function render()
    {
        $items = $this->baseQuery()
            ->with(['application.customer:id,first_name,last_name', 'motorbike:id,reg_no,make,model'])
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.finance.application-items-index', ['items' => $items]);
    }

    protected function baseQuery(): Builder
    {
        return ApplicationItem::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(function ($q) use ($term): void {
                    $q->where('app_id', 'like', "%{$term}%")
                        ->orWhereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$term}%"))
                        ->orWhereHas('application.customer', fn ($q) => $q->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%"));
                });
            })
            ->when($this->filter('is_posted') !== '', fn ($q) => $q->where('is_posted', $this->filter('is_posted') === '1'));
    }

    protected function exportQuery(): Builder { return $this->baseQuery()->with(['application.customer', 'motorbike']); }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id', 'App ID' => 'app_id', 'Application' => 'application_id',
            'Customer' => fn ($i) => $i->application?->customer ? $i->application->customer->first_name.' '.$i->application->customer->last_name : '',
            'Registration' => fn ($i) => $i->motorbike?->reg_no,
            'Make/Model' => fn ($i) => trim(($i->motorbike?->make ?? '').' '.($i->motorbike?->model ?? '')),
            'Posted' => fn ($i) => $i->is_posted ? 'Yes' : 'No',
        ];
    }
}
