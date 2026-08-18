<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\BookingInvoice;
use App\Models\Customer;
use App\Models\Motorbike;
use App\Models\NgnDigitalInvoice;
use App\Models\NgnDigitalInvoiceItem;
use App\Support\FluxAdminFormPayload;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Digital Invoice — Flux Admin')]
class DigitalInvoiceForm extends Component
{
    use WithAuthorization;

    public ?NgnDigitalInvoice $digitalInvoice = null;

    public array $form = [];

    public array $items = [];

    public function mount(?NgnDigitalInvoice $digitalInvoice = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-ecommerce');
        $this->digitalInvoice = $digitalInvoice?->id ? $digitalInvoice : null;

        if ($this->digitalInvoice) {
            $attrs = $this->digitalInvoice->getAttributes();
            $attrs['issue_date'] = $this->digitalInvoice->issue_date ? Carbon::parse($this->digitalInvoice->issue_date)->format('Y-m-d') : null;
            $attrs['due_date']   = $this->digitalInvoice->due_date   ? Carbon::parse($this->digitalInvoice->due_date)->format('Y-m-d')   : null;
            $this->form = $attrs;
            $this->items = $this->digitalInvoice->items()
                ->orderBy('id')
                ->get(['item_name', 'sku', 'quantity', 'price', 'discount', 'tax', 'total', 'notes'])
                ->map(fn (NgnDigitalInvoiceItem $item) => $item->toArray())
                ->all();
        } else {
            $this->form = ['issue_date' => now()->toDateString(), 'status' => 'draft', 'invoice_type' => 'sale'];
            $this->items = [];
        }
    }

    public function updatedFormCustomerId($value): void
    {
        if (! $value) {
            return;
        }

        $customer = Customer::query()->find($value);
        if (! $customer) {
            return;
        }

        $this->form['customer_name'] = trim($customer->first_name . ' ' . $customer->last_name);
        $this->form['customer_email'] = $customer->email;
        $this->form['customer_phone'] = $customer->phone;
        $this->form['whatsapp'] = $customer->whatsapp;
    }

    public function updatedFormMotorbikeId($value): void
    {
        if (! $value) {
            return;
        }

        $motorbike = Motorbike::query()->find($value);
        if (! $motorbike) {
            return;
        }

        $this->form['registration_number'] = $motorbike->reg_no;
        $this->form['vin'] = $motorbike->vin_number;
        $this->form['make'] = $motorbike->make;
        $this->form['model'] = $motorbike->model;
        $this->form['year'] = $motorbike->year;
    }

    public function addItem(): void
    {
        $this->items[] = [
            'item_name' => '',
            'sku' => '',
            'quantity' => 1,
            'price' => 0,
            'discount' => 0,
            'tax' => 0,
            'total' => 0,
            'notes' => '',
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->recalculateItems();
    }

    public function updatedItems(): void
    {
        $this->recalculateItems();
    }

    protected function recalculateItems(): void
    {
        foreach ($this->items as $index => $item) {
            $quantity = is_numeric($item['quantity'] ?? null) ? (float) $item['quantity'] : 0;
            $price = is_numeric($item['price'] ?? null) ? (float) $item['price'] : 0;
            $discount = is_numeric($item['discount'] ?? null) ? (float) $item['discount'] : 0;
            $tax = is_numeric($item['tax'] ?? null) ? (float) $item['tax'] : 0;

            $this->items[$index]['total'] = number_format(($quantity * $price) - $discount + $tax, 2, '.', '');
        }
    }

    public function save(): void
    {
        $this->items = array_values(array_filter($this->items, function (array $item): bool {
            return trim((string) ($item['item_name'] ?? '')) !== ''
                || trim((string) ($item['sku'] ?? '')) !== ''
                || trim((string) ($item['notes'] ?? '')) !== ''
                || (float) ($item['price'] ?? 0) > 0
                || (float) ($item['discount'] ?? 0) > 0
                || (float) ($item['tax'] ?? 0) > 0;
        }));

        $this->validate([
            'form.invoice_number'      => ['nullable', 'string', 'max:100', Rule::unique('ngn_digital_invoices', 'invoice_number')->ignore($this->digitalInvoice?->id)],
            'form.invoice_type'        => ['required', 'in:repair,rental,sale,service'],
            'form.invoice_category'    => ['nullable', 'in:new,used,parts,service'],
            'form.booking_invoice_id'  => ['nullable', 'integer', 'exists:booking_invoices,id'],
            'form.customer_id'         => ['nullable', 'integer', 'exists:customers,id'],
            'form.customer_name'       => ['nullable', 'string', 'max:255'],
            'form.customer_email'      => ['nullable', 'email', 'max:255'],
            'form.customer_phone'      => ['nullable', 'string', 'max:50'],
            'form.whatsapp'            => ['nullable', 'string', 'max:50'],
            'form.motorbike_id'        => ['nullable', 'integer', 'exists:motorbikes,id'],
            'form.registration_number' => ['nullable', 'string', 'max:50'],
            'form.make'                => ['nullable', 'string', 'max:100'],
            'form.model'               => ['nullable', 'string', 'max:100'],
            'form.year'                => ['nullable', 'integer'],
            'form.vin'                 => ['nullable', 'string', 'max:100'],
            'form.issue_date'          => ['required', 'date'],
            'form.due_date'            => ['nullable', 'date'],
            'form.amount'              => ['nullable', 'numeric'],
            'form.total_paid'          => ['nullable', 'numeric'],
            'form.status'              => ['required', 'in:draft,approved,sent,paid,cancelled'],
            'form.notes'               => ['nullable', 'string'],
            'form.internal_notes'      => ['nullable', 'string'],
            'items'                    => ['array'],
            'items.*.item_name'        => ['required', 'string', 'max:255'],
            'items.*.sku'              => ['nullable', 'string', 'max:255'],
            'items.*.quantity'         => ['required', 'integer', 'min:1'],
            'items.*.price'            => ['required', 'numeric', 'min:0'],
            'items.*.discount'         => ['nullable', 'numeric', 'min:0'],
            'items.*.tax'              => ['nullable', 'numeric', 'min:0'],
            'items.*.notes'            => ['nullable', 'string', 'max:255'],
        ]);

        $this->recalculateItems();

        $lineTotal = collect($this->items)->sum(fn (array $item) => (float) ($item['total'] ?? 0));

        $payload = FluxAdminFormPayload::onlyPersistable(NgnDigitalInvoice::class, [
            'invoice_number'      => $this->form['invoice_number'] ?? null,
            'invoice_type'        => $this->form['invoice_type'],
            'invoice_category'    => $this->form['invoice_category'] ?? null,
            'booking_invoice_id'  => ($this->form['booking_invoice_id'] ?? null) ?: null,
            'customer_id'         => ($this->form['customer_id'] ?? null) ?: null,
            'customer_name'       => $this->form['customer_name'] ?? null,
            'customer_email'      => $this->form['customer_email'] ?? null,
            'customer_phone'      => $this->form['customer_phone'] ?? null,
            'whatsapp'            => $this->form['whatsapp'] ?? null,
            'motorbike_id'        => ($this->form['motorbike_id'] ?? null) ?: null,
            'registration_number' => $this->form['registration_number'] ?? null,
            'make'                => $this->form['make'] ?? null,
            'model'               => $this->form['model'] ?? null,
            'year'                => $this->form['year'] ?? null,
            'vin'                 => $this->form['vin'] ?? null,
            'issue_date'          => $this->form['issue_date'],
            'due_date'            => $this->form['due_date'] ?: null,
            'amount'              => $this->form['amount'] ?? null,
            'total_paid'          => $this->form['total_paid'] ?? null,
            'total'               => $lineTotal,
            'status'              => $this->form['status'],
            'notes'               => $this->form['notes'] ?? null,
            'internal_notes'      => $this->form['internal_notes'] ?? null,
        ]);

        if (! $this->digitalInvoice) {
            $payload['created_by'] = FluxAdminFormPayload::adminUserId();
        }

        DB::transaction(function () use ($payload): void {
            if ($this->digitalInvoice) {
                $this->digitalInvoice->update($payload);
                $invoice = $this->digitalInvoice->refresh();
            } else {
                $invoice = NgnDigitalInvoice::create($payload);
                $this->digitalInvoice = $invoice;
            }

            $invoice->items()->delete();

            foreach ($this->items as $item) {
                if (trim((string) ($item['item_name'] ?? '')) === '') {
                    continue;
                }

                $invoice->items()->create([
                    'item_name' => $item['item_name'],
                    'sku' => $item['sku'] ?? null,
                    'quantity' => $item['quantity'] ?? 1,
                    'price' => $item['price'] ?? 0,
                    'discount' => $item['discount'] ?? 0,
                    'tax' => $item['tax'] ?? 0,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $invoice->forceFill([
                'total' => $invoice->items()->sum('total'),
            ])->saveQuietly();
        });

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Invoice saved.');
        $this->redirect(route('flux-admin.digital-invoices.index'), navigate: true);
    }

    public function render()
    {
        $customers = Customer::query()
            ->orderBy('first_name')
            ->limit(500)
            ->get(['id', 'first_name', 'last_name', 'phone', 'email']);
        $motorbikes = Motorbike::query()
            ->orderBy('reg_no')
            ->limit(500)
            ->get(['id', 'reg_no', 'make', 'model', 'year', 'vin_number']);
        $bookingInvoices = BookingInvoice::query()
            ->latest('invoice_date')
            ->limit(300)
            ->get(['id', 'booking_id', 'invoice_date', 'amount', 'is_paid']);

        return view('flux-admin.pages.ecommerce.digital-invoice-form', compact('customers', 'motorbikes', 'bookingInvoices'));
    }
}
