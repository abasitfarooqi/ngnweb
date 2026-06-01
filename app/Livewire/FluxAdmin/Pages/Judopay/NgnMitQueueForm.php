<?php

namespace App\Livewire\FluxAdmin\Pages\Judopay;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnMitQueue;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('NGN MIT queue entry — Flux Admin')]
class NgnMitQueueForm extends Component
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
            $record         = NgnMitQueue::findOrFail($id);
            $this->form     = $record->getAttributes();

            foreach (['invoice_date', 'mit_fire_date'] as $field) {
                if (! empty($this->form[$field])) {
                    try {
                        $this->form[$field] = \Carbon\Carbon::parse($this->form[$field])->format('Y-m-d');
                    } catch (\Throwable) {
                        $this->form[$field] = null;
                    }
                }
            }
        } else {
            $this->form = [
                'status'      => 'pending',
                'cleared'     => false,
                'mit_attempt' => 1,
            ];
        }
    }

    public function save(): void
    {
        $this->form['cleared'] = (bool) ($this->form['cleared'] ?? false);

        $this->validate([
            'form.invoice_number' => ['required', 'string', 'max:100'],
            'form.invoice_date'   => ['nullable', 'date'],
            'form.mit_fire_date'  => ['nullable', 'date'],
            'form.mit_attempt'    => ['nullable', 'integer', 'min:1'],
            'form.status'         => ['nullable', 'string', 'in:pending,processing,success,failed'],
            'form.cleared'        => ['boolean'],
        ]);

        $data = [
            'invoice_number' => $this->form['invoice_number'],
            'invoice_date'   => $this->form['invoice_date'] ?? null,
            'mit_fire_date'  => $this->form['mit_fire_date'] ?? null,
            'mit_attempt'    => $this->form['mit_attempt'] ?? 1,
            'status'         => $this->form['status'] ?? 'pending',
            'cleared'        => $this->form['cleared'],
        ];

        if ($this->recordId) {
            NgnMitQueue::findOrFail($this->recordId)->update($data);
        } else {
            NgnMitQueue::create($data);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
        $this->redirect(route('flux-admin.ngn-mit-queue.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.judopay.ngn-mit-queue-form');
    }
}
