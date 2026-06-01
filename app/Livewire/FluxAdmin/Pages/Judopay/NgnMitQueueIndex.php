<?php

namespace App\Livewire\FluxAdmin\Pages\Judopay;

use App\Helpers\JudopayMit;
use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\JudopayMitQueue;
use App\Models\NgnMitQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('NGN MIT queue — Flux Admin')]
class NgnMitQueueIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'ngn-mit-queue';
        $this->sortField = 'mit_fire_date';
    }

    protected function formModel(): string { return NgnMitQueue::class; }

    protected function formRules(): array
    {
        return [
            'formData.invoice_number' => ['required', 'string', 'max:100'],
            'formData.invoice_date'   => ['nullable', 'date'],
            'formData.mit_fire_date'  => ['nullable', 'date'],
            'formData.mit_attempt'    => ['nullable', 'integer', 'min:1'],
            'formData.status'         => ['nullable', 'string', 'in:pending,processing,success,failed'],
            'formData.cleared'        => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['status' => 'pending', 'cleared' => false, 'mit_attempt' => 1];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(NgnMitQueue::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        NgnMitQueue::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function addToQueue(int $id): void
    {
        $userId = backpack_user()?->id;

        if (! $userId) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Authentication required.');

            return;
        }

        try {
            $result = JudopayMit::addToLiveChamber($id, $userId);
            $this->dispatch('flux-admin:toast',
                type: $result['success'] ? 'success' : 'error',
                message: $result['message'] ?? ($result['success'] ? 'Added to live chamber.' : 'Failed.')
            );
        } catch (\Throwable $e) {
            Log::channel('judopay')->error('addToQueue (NgnMitQueue) via Flux Admin failed', ['id' => $id, 'error' => $e->getMessage()]);
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Error: '.$e->getMessage());
        }
    }

    public function stopQueue(int $liveQueueId): void
    {
        try {
            $item = JudopayMitQueue::with('ngnMitQueue')->findOrFail($liveQueueId);
            $ngnQueue = $item->ngnMitQueue;

            if ($ngnQueue) {
                $ngnQueue->is_in_live_chamber = false;
                $ngnQueue->live_chamber_item_id = null;
                $ngnQueue->save();
            }

            $item->delete();
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Queue item stopped.');
        } catch (\Throwable $e) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Error: '.$e->getMessage());
        }
    }

    public function render()
    {
        $rows = $this->baseQuery()->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        return view('flux-admin.pages.judopay.ngn-mit-queue-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return NgnMitQueue::query()
            ->when($this->search, fn ($q, $v) => $q->where('invoice_number', 'like', "%{$v}%"))
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($this->filter('cleared') !== '', fn ($q) => $q->where('cleared', $this->filter('cleared') === '1'));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id', 'Invoice' => 'invoice_number',
            'Invoice date' => fn ($r) => $r->invoice_date ? \Carbon\Carbon::parse($r->invoice_date)->format('Y-m-d') : '',
            'Fire date' => fn ($r) => $r->mit_fire_date ? \Carbon\Carbon::parse($r->mit_fire_date)->format('Y-m-d') : '',
            'Attempt' => 'mit_attempt', 'Status' => 'status',
            'Cleared' => fn ($r) => $r->cleared ? 'Yes' : 'No',
            'Cleared at' => fn ($r) => $r->cleared_at?->format('Y-m-d H:i'),
        ];
    }
}
