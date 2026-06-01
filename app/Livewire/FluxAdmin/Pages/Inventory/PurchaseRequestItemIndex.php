<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\BikeModel;
use App\Models\Make;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Purchase request items — Flux Admin')]
class PurchaseRequestItemIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return PurchaseRequestItem::class; }

    protected function formRules(): array
    {
        return [
            'formData.pr_id'          => ['required', 'integer', 'exists:purchase_requests,id'],
            'formData.part_number'    => ['nullable', 'string', 'max:100'],
            'formData.part_position'  => ['nullable', 'string', 'max:100'],
            'formData.brand_name_id'  => ['nullable', 'integer'],
            'formData.bike_model_id'  => ['nullable', 'integer'],
            'formData.reg_no'         => ['nullable', 'string', 'max:20'],
            'formData.chassis_no'     => ['nullable', 'string', 'max:100'],
            'formData.color'          => ['nullable', 'string', 'max:80'],
            'formData.year'           => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'formData.quantity'       => ['nullable', 'integer', 'min:1'],
            'formData.link_one'       => ['nullable', 'string', 'max:500'],
            'formData.link_two'       => ['nullable', 'string', 'max:500'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['quantity' => 1];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(PurchaseRequestItem::findOrFail($id));
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
        PurchaseRequestItem::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = PurchaseRequestItem::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('part_number', 'like', "%{$v}%")->orWhere('reg_no', 'like', "%{$v}%")->orWhere('chassis_no', 'like', "%{$v}%")))
            ->when($this->filter('pr_id'), fn ($q, $v) => $q->where('pr_id', $v))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $makes = Make::query()->orderBy('name')->get(['id', 'name']);
        $bikeModels = BikeModel::query()->orderBy('name')->get(['id', 'name']);
        $purchaseRequests = PurchaseRequest::query()->orderByDesc('id')->limit(200)->get(['id', 'date', 'note']);

        return view('flux-admin.pages.inventory.purchase-request-items-index', compact('rows', 'makes', 'bikeModels', 'purchaseRequests'));
    }
}
