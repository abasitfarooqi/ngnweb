<?php

namespace App\Livewire\FluxAdmin\Pages\Access;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\UploadDocumentAccess;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Upload document links — Flux Admin')]
class UploadDocumentIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return UploadDocumentAccess::class; }

    protected function formRules(): array
    {
        return [
            'formData.customer_id' => ['required', 'integer', 'exists:customers,id'],
            'formData.booking_id' => ['nullable', 'integer'],
            'formData.passcode' => ['required', 'string', 'max:64'],
            'formData.expires_at' => ['required', 'date'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'passcode' => Str::upper(Str::random(8)),
            'expires_at' => now()->addDays(14)->format('Y-m-d H:i'),
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(UploadDocumentAccess::findOrFail($id));
        if (! empty($this->formData['expires_at'])) {
            $this->formData['expires_at'] = \Carbon\Carbon::parse($this->formData['expires_at'])->format('Y-m-d\TH:i');
        }
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Link saved.');
    }

    public function delete(int $id): void
    {
        UploadDocumentAccess::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function regeneratePasscode(): void
    {
        $this->formData['passcode'] = Str::upper(Str::random(8));
    }

    public function render()
    {
        $rows = UploadDocumentAccess::query()
            ->with('customer:id,first_name,last_name')
            ->when($this->search, fn ($q, $v) => $q->where('passcode', 'like', "%{$v}%")->orWhere('booking_id', $v)->orWhereHas('customer', fn ($q) => $q->where('first_name', 'like', "%{$v}%")->orWhere('last_name', 'like', "%{$v}%")))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.access.upload-document-index', ['rows' => $rows]);
    }
}
