<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Ecommerce\EcOrder;
use App\Support\EcOrderLineTypeQuery;
use App\Support\FluxAdminEntityLabel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('E-commerce orders — Flux Admin')]
class EcOrderIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    #[Url(history: true, except: '')]
    public string $filterOrderId = '';

    public string $listTitle = 'E-commerce orders';

    public string $listDescription = 'All online orders placed through the webshop.';

    public string $listIndexRoute = 'flux-admin.ec-orders.index';

    protected string $lineTypeFilter = '';

    public function mount(): void
    {
        $this->authorizeModule('see-menu-ecommerce');
        $this->exportable = true;
        $this->exportFilename = 'ecommerce-orders';
        $this->sortField = 'order_date';
    }

    protected function formModel(): string { return EcOrder::class; }

    protected function formRules(): array
    {
        return [
            'formData.order_date'          => ['required', 'date'],
            'formData.customer_id'         => ['nullable', 'integer'],
            'formData.branch_id'           => ['nullable', 'integer'],
            'formData.order_status'        => ['required', 'string'],
            'formData.total_amount'        => ['nullable', 'numeric'],
            'formData.discount'            => ['nullable', 'numeric'],
            'formData.tax'                 => ['nullable', 'numeric'],
            'formData.grand_total'         => ['nullable', 'numeric'],
            'formData.shipping_cost'       => ['nullable', 'numeric'],
            'formData.shipping_status'     => ['nullable', 'string'],
            'formData.shipping_date'       => ['nullable', 'date'],
            'formData.payment_status'      => ['nullable', 'string'],
            'formData.payment_date'        => ['nullable', 'date'],
            'formData.payment_reference'   => ['nullable', 'string', 'max:255'],
            'formData.currency'            => ['nullable', 'string', 'max:10'],
            'formData.shipping_method_id'  => ['nullable', 'integer'],
            'formData.payment_method_id'   => ['nullable', 'integer'],
            'formData.customer_address_id' => ['nullable', 'integer'],
        ];
    }

    public function updatingFilterOrderId(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'order_date'    => now()->toDateString(),
            'order_status'  => 'pending',
            'payment_status' => 'unpaid',
            'shipping_status' => 'pending',
            'currency'      => 'GBP',
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $record = EcOrder::findOrFail($id);
        $this->fillFromModel($record);
        $this->formData['order_date']   = $record->order_date   ? Carbon::parse($record->order_date)->format('Y-m-d')   : null;
        $this->formData['shipping_date'] = $record->shipping_date ? Carbon::parse($record->shipping_date)->format('Y-m-d') : null;
        $this->formData['payment_date']  = $record->payment_date  ? Carbon::parse($record->payment_date)->format('Y-m-d')  : null;
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
        EcOrder::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with([
                'customer:id,email,customer_id',
                'customer.customer:id,first_name,last_name,phone,email',
                'branch:id,name',
                'shippingMethod:id,name',
                'orderItems:order_id,item_type,product_name',
            ])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.ecommerce.orders-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        $query = EcOrder::query();

        EcOrderLineTypeQuery::apply($query, $this->lineTypeFilter !== '' ? $this->lineTypeFilter : null);

        return $query
            ->when($this->search, function (Builder $q): void {
                $v = $this->search;
                $q->where(function (Builder $q) use ($v): void {
                    if (is_numeric($v)) {
                        $q->orWhere('id', (int) $v);
                    }
                    $q->orWhereHas('customer', fn (Builder $q) => $q->where('email', 'like', "%{$v}%"))
                        ->orWhereHas('customer.customer', fn (Builder $q) => $q
                            ->where('first_name', 'like', "%{$v}%")
                            ->orWhere('last_name', 'like', "%{$v}%")
                            ->orWhere('phone', 'like', "%{$v}%")
                            ->orWhere('email', 'like', "%{$v}%"));
                });
            })
            ->when($this->filterOrderId !== '', fn ($q) => $q->where('id', (int) $this->filterOrderId))
            ->when($this->filter('order_status'), fn ($q, $v) => $q->where('order_status', $v))
            ->when($this->filter('payment_status'), fn ($q, $v) => $q->where('payment_status', $v))
            ->when($this->filter('shipping_status'), fn ($q, $v) => $q->where('shipping_status', $v));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with(['customer.customer', 'branch', 'shippingMethod']);
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Date' => fn ($r) => $r->order_date ? Carbon::parse($r->order_date)->format('Y-m-d') : '',
            'Customer' => fn ($r) => FluxAdminEntityLabel::customerAuth($r->customer),
            'Email' => fn ($r) => $r->customer?->customer?->email ?? $r->customer?->email,
            'Phone' => fn ($r) => $r->customer?->customer?->phone,
            'Branch' => fn ($r) => $r->branch?->name,
            'Grand total' => 'grand_total', 'Currency' => 'currency',
            'Order status' => 'order_status', 'Payment' => 'payment_status', 'Shipping' => 'shipping_status',
            'Shipping method' => fn ($r) => $r->shippingMethod?->name ?? '',
        ];
    }
}
