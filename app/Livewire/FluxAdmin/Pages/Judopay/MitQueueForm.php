<?php

namespace App\Livewire\FluxAdmin\Pages\Judopay;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\JudopayMitQueue;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Judopay MIT queue entry — Flux Admin')]
class MitQueueForm extends Component
{
    use WithAuthorization;

    public ?int $recordId = null;

    public array $form = [];

    public function mount(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');

        if ($id) {
            $this->recordId = $id;
            $record         = JudopayMitQueue::findOrFail($id);
            $this->form     = $record->getAttributes();

            if (! empty($this->form['mit_fire_date'])) {
                try {
                    $this->form['mit_fire_date'] = \Carbon\Carbon::parse($this->form['mit_fire_date'])->format('Y-m-d');
                } catch (\Throwable) {
                    $this->form['mit_fire_date'] = null;
                }
            }
        } else {
            $this->form = [
                'fired'   => false,
                'cleared' => false,
                'retry'   => 0,
            ];
        }
    }

    public function save(): void
    {
        $this->form['fired']   = (bool) ($this->form['fired'] ?? false);
        $this->form['cleared'] = (bool) ($this->form['cleared'] ?? false);

        $this->validate([
            'form.ngn_mit_queue_id'         => ['required', 'integer'],
            'form.judopay_payment_reference' => ['nullable', 'string'],
            'form.mit_fire_date'             => ['nullable', 'date'],
            'form.retry'                     => ['nullable', 'integer', 'min:0'],
            'form.fired'                     => ['boolean'],
            'form.cleared'                   => ['boolean'],
        ]);

        $data = [
            'ngn_mit_queue_id'          => $this->form['ngn_mit_queue_id'],
            'judopay_payment_reference'  => $this->form['judopay_payment_reference'] ?? null,
            'mit_fire_date'              => $this->form['mit_fire_date'] ?? null,
            'retry'                      => $this->form['retry'] ?? 0,
            'fired'                      => $this->form['fired'],
            'cleared'                    => $this->form['cleared'],
        ];

        if ($this->recordId) {
            JudopayMitQueue::findOrFail($this->recordId)->update($data);
        } else {
            JudopayMitQueue::create($data);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
        $this->redirect(route('flux-admin.judopay-mit-queue.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.judopay.mit-queue-form');
    }
}
