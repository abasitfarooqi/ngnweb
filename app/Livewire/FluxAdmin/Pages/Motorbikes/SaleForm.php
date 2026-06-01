<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Motorbike;
use App\Models\MotorbikesSale;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Motorbike Sale — Flux Admin')]
class SaleForm extends Component
{
    use WithAuthorization;

    public ?MotorbikesSale $motorbikesSale = null;

    public array $form = [];

    public string $motorbikeSearch = '';
    public array $motorbikeSuggestions = [];

    public function mount(?MotorbikesSale $motorbikesSale = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->motorbikesSale = $motorbikesSale;

        if ($motorbikesSale && $motorbikesSale->exists) {
            $this->form = $motorbikesSale->getAttributes();
            $this->motorbikeSearch = $motorbikesSale->motorbike?->reg_no ?? '';
        } else {
            $this->form = [
                'is_sold'      => false,
                'v5_available' => false,
            ];
        }
    }

    public function updatingMotorbikeSearch(): void
    {
        if (strlen($this->motorbikeSearch) < 2) {
            $this->motorbikeSuggestions = [];
            return;
        }
        $this->motorbikeSuggestions = Motorbike::where('reg_no', 'like', "%{$this->motorbikeSearch}%")
            ->limit(8)->get(['id', 'reg_no'])->map(fn ($m) => [
                'id'  => $m->id,
                'reg' => $m->reg_no,
            ])->toArray();
    }

    public function selectMotorbike(int $id, string $reg): void
    {
        $this->form['motorbike_id'] = $id;
        $this->motorbikeSearch      = $reg;
        $this->motorbikeSuggestions = [];
    }

    public function save(): void
    {
        $this->validate([
            'form.motorbike_id'  => ['required', 'integer'],
            'form.condition'     => ['nullable', 'string', 'max:120'],
            'form.mileage'       => ['nullable', 'integer'],
            'form.price'         => ['nullable', 'numeric'],
            'form.note'          => ['nullable', 'string'],
            'form.is_sold'       => ['boolean'],
            'form.buyer_name'    => ['nullable', 'string', 'max:255'],
            'form.buyer_phone'   => ['nullable', 'string', 'max:50'],
            'form.buyer_email'   => ['nullable', 'email', 'max:255'],
            'form.buyer_address' => ['nullable', 'string', 'max:500'],
            'form.v5_available'  => ['boolean'],
        ]);

        $data = [
            'motorbike_id'  => $this->form['motorbike_id'] ?? null,
            'condition'     => $this->form['condition'] ?? null,
            'mileage'       => $this->form['mileage'] ?? null,
            'price'         => $this->form['price'] ?? null,
            'note'          => $this->form['note'] ?? null,
            'is_sold'       => (bool) ($this->form['is_sold'] ?? false),
            'buyer_name'    => $this->form['buyer_name'] ?? null,
            'buyer_phone'   => $this->form['buyer_phone'] ?? null,
            'buyer_email'   => $this->form['buyer_email'] ?? null,
            'buyer_address' => $this->form['buyer_address'] ?? null,
            'v5_available'  => (bool) ($this->form['v5_available'] ?? false),
        ];

        if ($this->motorbikesSale && $this->motorbikesSale->exists) {
            $this->motorbikesSale->update($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Sale updated.');
        } else {
            MotorbikesSale::create($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Sale created.');
        }

        $this->redirect(route('flux-admin.motorbike-sales.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.motorbikes.sale-form');
    }
}
