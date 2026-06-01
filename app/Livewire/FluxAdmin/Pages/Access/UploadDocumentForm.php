<?php

namespace App\Livewire\FluxAdmin\Pages\Access;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\UploadDocumentAccess;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Upload document link — Flux Admin')]
class UploadDocumentForm extends Component
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
            $record         = UploadDocumentAccess::findOrFail($id);
            $this->form     = $record->getAttributes();

            if (! empty($this->form['expires_at'])) {
                try {
                    $this->form['expires_at'] = \Carbon\Carbon::parse($this->form['expires_at'])->format('Y-m-d\TH:i');
                } catch (\Throwable) {
                    $this->form['expires_at'] = null;
                }
            }
        } else {
            $this->form = [
                'passcode'   => Str::upper(Str::random(8)),
                'expires_at' => now()->addDays(14)->format('Y-m-d\TH:i'),
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
            'form.booking_id'  => ['nullable', 'integer'],
            'form.passcode'    => ['required', 'string', 'max:64'],
            'form.expires_at'  => ['required', 'date'],
        ]);

        $data = [
            'customer_id' => $this->form['customer_id'],
            'booking_id'  => $this->form['booking_id'] ?? null,
            'passcode'    => $this->form['passcode'],
            'expires_at'  => $this->form['expires_at'],
        ];

        if ($this->recordId) {
            UploadDocumentAccess::findOrFail($this->recordId)->update($data);
        } else {
            UploadDocumentAccess::create($data);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Link saved.');
        $this->redirect(route('flux-admin.upload-document-links.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.access.upload-document-form');
    }
}
