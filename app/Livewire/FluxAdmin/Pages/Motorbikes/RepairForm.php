<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\Motorbike;
use App\Models\MotorbikeRepair;
use App\Models\MotorbikeRepairObservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

    public array $observations = [];

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
            $this->observations = $motorbikeRepair->observations()
                ->orderBy('id')
                ->get(['observation_description'])
                ->map(fn (MotorbikeRepairObservation $observation) => $observation->toArray())
                ->all();
        } else {
            $this->form = [
                'is_repaired'  => false,
                'is_returned'  => false,
                'arrival_date' => now()->format('Y-m-d'),
            ];
            $this->observations = [];
        }
    }

    public function addObservation(): void
    {
        $this->observations[] = ['observation_description' => ''];
    }

    public function removeObservation(int $index): void
    {
        unset($this->observations[$index]);
        $this->observations = array_values($this->observations);
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

    public function commitMotorbikeSearch(): void
    {
        if (! empty($this->form['motorbike_id'])) {
            return;
        }

        if ($this->motorbikeSuggestions === [] && strlen($this->motorbikeSearch) >= 2) {
            $this->updatingMotorbikeSearch();
        }

        if ($this->motorbikeSuggestions === []) {
            return;
        }

        $compact = strtoupper(preg_replace('/\s+/', '', $this->motorbikeSearch) ?? '');
        foreach ($this->motorbikeSuggestions as $suggestion) {
            $reg = strtoupper(preg_replace('/\s+/', '', (string) ($suggestion['reg'] ?? '')) ?? '');
            if ($compact !== '' && $reg === $compact) {
                $this->selectMotorbike((int) $suggestion['id'], (string) $suggestion['reg']);

                return;
            }
        }

        if (count($this->motorbikeSuggestions) === 1) {
            $first = $this->motorbikeSuggestions[0];
            $this->selectMotorbike((int) $first['id'], (string) $first['reg']);
        }
    }

    public function save(): void
    {
        $this->commitMotorbikeSearch();
        $this->observations = array_values(array_filter($this->observations, fn (array $observation): bool => trim((string) ($observation['observation_description'] ?? '')) !== ''));

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
            'observations'       => ['array'],
            'observations.*.observation_description' => ['required', 'string', 'max:3000'],
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
            'user_id'       => auth()->id(),
        ];

        DB::transaction(function () use ($data): void {
            if ($this->motorbikeRepair && $this->motorbikeRepair->exists) {
                $this->motorbikeRepair->update($data);
                $repair = $this->motorbikeRepair->refresh();
            } else {
                $repair = MotorbikeRepair::create($data);
                $this->motorbikeRepair = $repair;
            }

            $repair->observations()->delete();

            foreach ($this->observations as $observation) {
                $repair->observations()->create([
                    'observation_description' => $observation['observation_description'],
                ]);
            }
        });

        $this->dispatch('flux-admin:toast', type: 'success', message: $this->motorbikeRepair?->wasRecentlyCreated ? 'Repair created.' : 'Repair updated.');

        $this->redirect(route('flux-admin.motorbike-repairs.index'), navigate: true);
    }

    public function render()
    {
        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.motorbikes.repair-form', compact('branches'));
    }
}
