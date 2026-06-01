<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\Motorbike;
use App\Models\MotorbikeRepair;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Motorbike Repair — Flux Admin')]
class RepairForm extends Component
{
    use WithAuthorization;

    public ?MotorbikeRepair $motorbikeRepair = null;

    public array $form = [];

    public string $motorbikeSearch = '';
    public array $motorbikeSuggestions = [];

    public function mount(?MotorbikeRepair $motorbikeRepair = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-services-and-repairs-and-report');
        $this->motorbikeRepair = $motorbikeRepair;

        if ($motorbikeRepair && $motorbikeRepair->exists) {
            $attrs = $motorbikeRepair->getAttributes();
            foreach (['arrival_date', 'repaired_date', 'returned_date'] as $f) {
                if (! empty($attrs[$f])) {
                    try {
                        $attrs[$f] = Carbon::parse($attrs[$f])->format('Y-m-d');
                    } catch (\Throwable) {
                        $attrs[$f] = null;
                    }
                }
            }
            $this->form = $attrs;
            $this->motorbikeSearch = $motorbikeRepair->motorbike?->reg_no ?? '';
        } else {
            $this->form = [
                'is_repaired'  => false,
                'is_returned'  => false,
                'arrival_date' => now()->format('Y-m-d'),
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
        $this->validate([
            'form.motorbike_id'  => ['required', 'integer'],
            'form.fullname'      => ['required', 'string', 'max:255'],
            'form.phone'         => ['nullable', 'string', 'max:50'],
            'form.email'         => ['nullable', 'email', 'max:255'],
            'form.arrival_date'  => ['nullable', 'date'],
            'form.notes'         => ['nullable', 'string'],
            'form.is_repaired'   => ['boolean'],
            'form.repaired_date' => ['nullable', 'date'],
            'form.is_returned'   => ['boolean'],
            'form.returned_date' => ['nullable', 'date'],
            'form.branch_id'     => ['nullable', 'integer'],
        ]);

        $data = [
            'motorbike_id'  => $this->form['motorbike_id'] ?? null,
            'fullname'      => $this->form['fullname'] ?? null,
            'phone'         => $this->form['phone'] ?? null,
            'email'         => $this->form['email'] ?? null,
            'arrival_date'  => $this->form['arrival_date'] ?? null,
            'notes'         => $this->form['notes'] ?? null,
            'is_repaired'   => (bool) ($this->form['is_repaired'] ?? false),
            'repaired_date' => $this->form['repaired_date'] ?? null,
            'is_returned'   => (bool) ($this->form['is_returned'] ?? false),
            'returned_date' => $this->form['returned_date'] ?? null,
            'branch_id'     => $this->form['branch_id'] ?? null,
        ];

        if ($this->motorbikeRepair && $this->motorbikeRepair->exists) {
            $this->motorbikeRepair->update($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Repair updated.');
        } else {
            MotorbikeRepair::create($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Repair created.');
        }

        $this->redirect(route('flux-admin.motorbike-repairs.index'), navigate: true);
    }

    public function render()
    {
        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.motorbikes.repair-form', compact('branches'));
    }
}
