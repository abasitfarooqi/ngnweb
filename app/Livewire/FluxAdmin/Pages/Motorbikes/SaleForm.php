<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Motorbike;
use App\Models\MotorbikesSale;
use Carbon\Carbon;
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
            $this->form['date_of_purchase'] = $motorbikesSale->date_of_purchase ? Carbon::parse($motorbikesSale->date_of_purchase)->format('Y-m-d') : null;
            $this->form['date_of_sale'] = $motorbikesSale->date_of_sale ? Carbon::parse($motorbikesSale->date_of_sale)->format('Y-m-d') : null;
            $this->motorbikeSearch = $motorbikesSale->motorbike?->reg_no ?? '';
        } else {
            $this->form = [
                'is_sold'      => false,
                'v5_available' => false,
                'date_of_purchase' => now()->toDateString(),
                'date_of_sale' => now()->toDateString(),
                'engine' => 'NOT CHECKED',
                'suspension' => 'NOT CHECKED',
                'brakes' => 'NOT CHECKED',
                'belt' => 'NOT CHECKED',
                'electrical' => 'NOT CHECKED',
                'tires' => 'NOT CHECKED',
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
        $buyerRules = (bool) ($this->form['is_sold'] ?? false)
            ? ['required', 'string', 'max:255']
            : ['nullable', 'string', 'max:255'];

        $this->validate([
            'form.motorbike_id'  => ['required', 'integer'],
            'form.condition'     => ['nullable', 'string', 'max:120'],
            'form.mileage'       => ['nullable', 'numeric'],
            'form.date_of_purchase' => ['nullable', 'date'],
            'form.date_of_sale'  => ['nullable', 'date'],
            'form.price'         => ['nullable', 'numeric'],
            'form.engine'        => ['nullable', 'string', 'max:255'],
            'form.suspension'    => ['nullable', 'string', 'max:255'],
            'form.brakes'        => ['nullable', 'string', 'max:255'],
            'form.belt'          => ['nullable', 'string', 'max:255'],
            'form.electrical'    => ['nullable', 'string', 'max:255'],
            'form.tires'         => ['nullable', 'string', 'max:255'],
            'form.accessories'   => ['nullable', 'string'],
            'form.note'          => ['nullable', 'string'],
            'form.is_sold'       => ['boolean'],
            'form.buyer_name'    => $buyerRules,
            'form.buyer_phone'   => [(bool) ($this->form['is_sold'] ?? false) ? 'required' : 'nullable', 'string', 'max:50'],
            'form.buyer_email'   => ['nullable', 'email', 'max:255'],
            'form.buyer_address' => [(bool) ($this->form['is_sold'] ?? false) ? 'required' : 'nullable', 'string', 'max:500'],
            'form.v5_available'  => ['boolean'],
        ]);

        if (! (bool) ($this->form['is_sold'] ?? false)) {
            $this->form['buyer_name'] = null;
            $this->form['buyer_phone'] = null;
            $this->form['buyer_email'] = null;
            $this->form['buyer_address'] = null;
        }

        $data = [
            'motorbike_id'  => $this->form['motorbike_id'] ?? null,
            'condition'     => ($this->form['condition'] ?? null) ?: '-',
            'mileage'       => $this->form['mileage'] ?? 0,
            'date_of_purchase' => ($this->form['date_of_purchase'] ?? null) ?: now()->toDateString(),
            'date_of_sale'  => ($this->form['date_of_sale'] ?? null) ?: now()->toDateString(),
            'price'         => $this->form['price'] ?? 0,
            'engine'        => ($this->form['engine'] ?? null) ?: 'NOT CHECKED',
            'suspension'    => ($this->form['suspension'] ?? null) ?: 'NOT CHECKED',
            'brakes'        => ($this->form['brakes'] ?? null) ?: 'NOT CHECKED',
            'belt'          => ($this->form['belt'] ?? null) ?: 'NOT CHECKED',
            'electrical'    => ($this->form['electrical'] ?? null) ?: 'NOT CHECKED',
            'tires'         => ($this->form['tires'] ?? null) ?: 'NOT CHECKED',
            'accessories'   => $this->form['accessories'] ?? null,
            'note'          => $this->form['note'] ?? '',
            'is_sold'       => (bool) ($this->form['is_sold'] ?? false),
            'buyer_name'    => $this->form['buyer_name'] ?? null,
            'buyer_phone'   => $this->form['buyer_phone'] ?? null,
            'buyer_email'   => $this->form['buyer_email'] ?? null,
            'buyer_address' => $this->form['buyer_address'] ?? null,
            'v5_available'  => (bool) ($this->form['v5_available'] ?? false),
            'user_id'       => auth()->id(),
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
