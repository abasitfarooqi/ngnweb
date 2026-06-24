<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\SpPart;
use App\Models\SpStockMovement;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Spare parts stock movement — Flux Admin')]
class SpStockMovementForm extends Component
{
    use WithAuthorization;

    public ?SpStockMovement $spStockMovement = null;

    public array $form = [];

    public function mount(?SpStockMovement $spStockMovement = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->spStockMovement = $spStockMovement;

        if ($spStockMovement && $spStockMovement->exists) {
            $this->form = $spStockMovement->getAttributes();
            if (! empty($this->form['transaction_date'])) {
                $this->form['transaction_date'] = \Carbon\Carbon::parse($this->form['transaction_date'])->format('Y-m-d');
            }
        } else {
            $this->form = [
                'transaction_date' => now()->toDateString(),
                'transaction_type' => 'purchase',
                'in' => 0,
                'out' => 0,
                'user_id' => auth()->id(),
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.branch_id' => ['required', 'integer', 'exists:branches,id'],
            'form.sp_part_id' => ['required', 'integer', 'exists:sp_parts,id'],
            'form.transaction_date' => ['required', 'date'],
            'form.transaction_type' => ['required', 'string', 'max:50'],
            'form.in' => ['nullable', 'numeric', 'min:0'],
            'form.out' => ['nullable', 'numeric', 'min:0'],
            'form.ref_doc_no' => ['nullable', 'string', 'max:120'],
            'form.remarks' => ['nullable', 'string'],
        ]);

        $payload = collect($this->form)->only([
            'branch_id', 'sp_part_id', 'transaction_date', 'transaction_type',
            'in', 'out', 'ref_doc_no', 'remarks',
        ])->all();
        $payload['user_id'] = $this->form['user_id'] ?? auth()->id();

        if ($this->spStockMovement && $this->spStockMovement->exists) {
            $this->spStockMovement->update($payload);
            $message = 'Movement saved.';
        } else {
            SpStockMovement::create($payload);
            $message = 'Movement created.';
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: $message);
        $this->redirect(route('flux-admin.sp-stock-movements.index'), navigate: true);
    }

    public function render()
    {
        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);
        $parts = SpPart::query()->orderBy('part_number')->limit(500)->get(['id', 'part_number', 'name']);

        return view('flux-admin.pages.spare-parts.sp-stock-movement-form', compact('branches', 'parts'));
    }
}
