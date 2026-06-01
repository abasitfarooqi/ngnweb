<?php

namespace App\Livewire\FluxAdmin\Pages\Access;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\RentalTerminateAccess;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Rental terminate link — Flux Admin')]
class RentalTerminateForm extends Component
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
            $record         = RentalTerminateAccess::findOrFail($id);
            $this->form     = $record->getAttributes();

            if (! empty($this->form['expire_at'])) {
                try {
                    $this->form['expire_at'] = \Carbon\Carbon::parse($this->form['expire_at'])->format('Y-m-d\TH:i');
                } catch (\Throwable) {
                    $this->form['expire_at'] = null;
                }
            }
        } else {
            $this->form = [
                'passcode'  => Str::upper(Str::random(8)),
                'expire_at' => now()->addDays(14)->format('Y-m-d\TH:i'),
            ];
        }
    }

    public function regeneratePasscode(): void
    {
        $this->form['passcode'] = Str::upper(Str::random(8));
    }

    public function save(): void
    {
        $this->validate([
            'form.customer_id' => ['required', 'integer', 'exists:customers,id'],
            'form.booking_id'  => ['required', 'integer'],
            'form.passcode'    => ['required', 'string', 'max:64'],
            'form.expire_at'   => ['required', 'date'],
        ]);

        $data = [
            'customer_id' => $this->form['customer_id'],
            'booking_id'  => $this->form['booking_id'],
            'passcode'    => $this->form['passcode'],
            'expire_at'   => $this->form['expire_at'],
        ];

        if ($this->recordId) {
            RentalTerminateAccess::findOrFail($this->recordId)->update($data);
        } else {
            RentalTerminateAccess::create($data);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Link saved.');
        $this->redirect(route('flux-admin.rental-terminate-links.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.access.rental-terminate-form');
    }
}
