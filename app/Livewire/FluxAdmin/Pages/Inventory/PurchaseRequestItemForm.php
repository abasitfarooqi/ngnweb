<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\BikeModel;
use App\Models\Make;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Purchase Request Item — Flux Admin')]
class PurchaseRequestItemForm extends Component
{
    use WithAuthorization;

    public ?PurchaseRequestItem $purchaseRequestItem = null;

    public array $form = [];

    public function mount(?PurchaseRequestItem $purchaseRequestItem = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->purchaseRequestItem = $purchaseRequestItem?->id ? $purchaseRequestItem : null;

        if ($this->purchaseRequestItem) {
            $this->form = $this->purchaseRequestItem->getAttributes();
        } else {
            $this->form = ['quantity' => 1];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.pr_id'         => ['required', 'integer', 'exists:purchase_requests,id'],
            'form.part_number'   => ['nullable', 'string', 'max:100'],
            'form.part_position' => ['nullable', 'string', 'max:100'],
            'form.brand_name_id' => ['nullable', 'integer'],
            'form.bike_model_id' => ['nullable', 'integer'],
            'form.reg_no'        => ['nullable', 'string', 'max:20'],
            'form.chassis_no'    => ['nullable', 'string', 'max:100'],
            'form.color'         => ['nullable', 'string', 'max:80'],
            'form.year'          => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'form.quantity'      => ['nullable', 'integer', 'min:1'],
            'form.link_one'      => ['nullable', 'string', 'max:500'],
            'form.link_two'      => ['nullable', 'string', 'max:500'],
        ]);

        $payload = [
            'pr_id'         => $this->form['pr_id'],
            'part_number'   => $this->form['part_number'] ?? null,
            'part_position' => $this->form['part_position'] ?? null,
            'brand_name_id' => $this->form['brand_name_id'] ?: null,
            'bike_model_id' => $this->form['bike_model_id'] ?: null,
            'reg_no'        => $this->form['reg_no'] ?? null,
            'chassis_no'    => $this->form['chassis_no'] ?? null,
            'color'         => $this->form['color'] ?? null,
            'year'          => $this->form['year'] ?? null,
            'quantity'      => $this->form['quantity'] ?? 1,
            'link_one'      => $this->form['link_one'] ?? null,
            'link_two'      => $this->form['link_two'] ?? null,
        ];

        if ($this->purchaseRequestItem) {
            $this->purchaseRequestItem->update($payload);
        } else {
            PurchaseRequestItem::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Item saved.');
        $this->redirect(route('flux-admin.purchase-request-items.index'), navigate: true);
    }

    public function render()
    {
        $makes            = Make::query()->orderBy('name')->get(['id', 'name']);
        $bikeModels       = BikeModel::query()->orderBy('name')->get(['id', 'name']);
        $purchaseRequests = PurchaseRequest::query()->orderByDesc('id')->limit(200)->get(['id', 'date', 'note']);

        return view('flux-admin.pages.inventory.purchase-request-item-form', compact('makes', 'bikeModels', 'purchaseRequests'));
    }
}
