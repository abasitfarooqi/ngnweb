<?php

namespace App\Livewire\FluxAdmin\Pages\Misc;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\RentingPricing;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Rental pricing — Flux Admin')]
class RentingPricingIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-renting-page');
        $this->exportable = true;
        $this->exportFilename = 'rental-pricing';
    }

    protected function formModel(): string
    {
        return RentingPricing::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.motorbike_id' => ['nullable', 'integer'],
            'formData.user_id' => ['nullable', 'integer'],
            'formData.weekly_price' => ['required', 'numeric', 'min:0'],
            'formData.minimum_deposit' => ['nullable', 'numeric', 'min:0'],
            'formData.update_date' => ['nullable', 'date'],
            'formData.iscurrent' => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'update_date' => now()->toDateString(),
            'user_id' => backpack_user()->id,
            'iscurrent' => true,
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $record = RentingPricing::findOrFail($id);
        $this->fillFromModel($record);
        $this->formData['update_date'] = $record->update_date
            ? Carbon::parse($record->update_date)->format('Y-m-d')
            : null;
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
        RentingPricing::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['motorbike:id,reg_no,make,model', 'user:id,first_name'])
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.misc.renting-pricing-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return RentingPricing::query()
            ->when($this->search, fn ($q, $v) => $q->whereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$v}%")->orWhere('make', 'like', "%{$v}%")->orWhere('model', 'like', "%{$v}%")))
            ->when($this->filter('iscurrent') !== '', fn ($q) => $q->where('iscurrent', $this->filter('iscurrent') === '1'));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with('motorbike:id,reg_no,make,model');
    }

    protected function exportColumns(): array
    {
        return [
            'Registration' => fn ($r) => $r->motorbike?->reg_no,
            'Make' => fn ($r) => $r->motorbike?->make, 'Model' => fn ($r) => $r->motorbike?->model,
            'Weekly price' => 'weekly_price', 'Minimum deposit' => 'minimum_deposit',
            'Current' => fn ($r) => $r->iscurrent ? 'Yes' : 'No',
            'Effective from' => fn ($r) => $r->update_date ? Carbon::parse($r->update_date)->format('Y-m-d') : '',
        ];
    }
}
