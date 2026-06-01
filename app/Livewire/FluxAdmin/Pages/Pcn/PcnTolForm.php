<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\PcnTolRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('PCN TOL request — Flux Admin')]
class PcnTolForm extends Component
{
    use WithAuthorization;

    public ?int $recordId = null;

    public array $form = [];

    public function mount(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-pcn-portal');

        if ($id) {
            $this->recordId = $id;
            $record         = PcnTolRequest::findOrFail($id);
            $this->form     = $record->getAttributes();

            foreach (['request_date', 'letter_sent_at'] as $field) {
                if (! empty($this->form[$field])) {
                    try {
                        $this->form[$field] = \Carbon\Carbon::parse($this->form[$field])->format(
                            $field === 'letter_sent_at' ? 'Y-m-d\TH:i' : 'Y-m-d'
                        );
                    } catch (\Throwable) {
                        $this->form[$field] = null;
                    }
                }
            }
        } else {
            $this->form = [
                'status'       => 'pending',
                'request_date' => now()->format('Y-m-d'),
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.update_id'      => ['required', 'integer', 'exists:pcn_case_updates,id'],
            'form.request_date'   => ['required', 'date'],
            'form.status'         => ['required', 'string', 'in:pending,sent,approved,rejected'],
            'form.letter_sent_at' => ['nullable', 'date'],
            'form.note'           => ['nullable', 'string', 'max:2000'],
        ]);

        $data = [
            'update_id'      => $this->form['update_id'],
            'request_date'   => $this->form['request_date'],
            'status'         => $this->form['status'],
            'letter_sent_at' => $this->form['letter_sent_at'] ?? null,
            'note'           => $this->form['note'] ?? null,
        ];

        if ($this->recordId) {
            PcnTolRequest::findOrFail($this->recordId)->update($data);
        } else {
            $data['user_id'] = auth()->id();
            PcnTolRequest::create($data);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'TOL request saved.');
        $this->redirect(route('flux-admin.pcn-tol-requests.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.pcn.pcn-tol-form');
    }
}
