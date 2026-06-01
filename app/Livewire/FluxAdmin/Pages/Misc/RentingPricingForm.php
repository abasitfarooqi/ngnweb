<?php

namespace App\Livewire\FluxAdmin\Pages\Misc;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\RentingPricing;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Rental pricing — Flux Admin')]
class RentingPricingForm extends Component
{
    use WithAuthorization;

    public ?int $recordId = null;

    public array $form = [];

    public function mount(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-renting-page');

        if ($id) {
            $this->recordId = $id;
            $record         = RentingPricing::findOrFail($id);
            $this->form     = $record->getAttributes();

            if (! empty($this->form['update_date'])) {
                try {
                    $this->form['update_date'] = Carbon::parse($this->form['update_date'])->format('Y-m-d');
                } catch (\Throwable) {
                    $this->form['update_date'] = null;
                }
            }
        } else {
            $this->form = [
                'update_date' => now()->toDateString(),
                'user_id'     => backpack_user()?->id,
                'iscurrent'   => true,
            ];
        }
    }

    public function save(): void
    {
        $this->form['iscurrent'] = (bool) ($this->form['iscurrent'] ?? false);

        $this->validate([
            'form.motorbike_id'    => ['nullable', 'integer'],
            'form.user_id'         => ['nullable', 'integer'],
            'form.weekly_price'    => ['required', 'numeric', 'min:0'],
            'form.minimum_deposit' => ['nullable', 'numeric', 'min:0'],
            'form.update_date'     => ['nullable', 'date'],
            'form.iscurrent'       => ['boolean'],
        ]);

        $data = [
            'motorbike_id'    => $this->form['motorbike_id'] ?? null,
            'user_id'         => $this->form['user_id'] ?? backpack_user()?->id,
            'weekly_price'    => $this->form['weekly_price'],
            'minimum_deposit' => $this->form['minimum_deposit'] ?? null,
            'update_date'     => $this->form['update_date'] ?? null,
            'iscurrent'       => $this->form['iscurrent'],
        ];

        if ($this->recordId) {
            RentingPricing::findOrFail($this->recordId)->update($data);
        } else {
            RentingPricing::create($data);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
        $this->redirect(route('flux-admin.renting-pricing.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.misc.renting-pricing-form');
    }
}
