<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\Motorbike;
use App\Models\MotorbikeRepair;
use App\Models\MotorbikeRepairObservation;
use App\Models\MotorbikeRepairServicesList;
use App\Models\MotorbikeRepairUpdate;
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

    public array $updates = [];

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
            $this->hydrateRepeatables($motorbikeRepair);
        } else {
            $this->form = [
                'is_repaired'  => false,
                'is_returned'  => false,
                'arrival_date' => now()->format('Y-m-d'),
            ];
            $this->observations = [];
            $this->updates = [];
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

    public function addUpdate(): void
    {
        $this->updates[] = $this->emptyUpdate();
    }

    public function removeUpdate(int $index): void
    {
        unset($this->updates[$index]);
        $this->updates = array_values($this->updates);
    }

    public function toggleUpdateServices(int $index): void
    {
        $this->updates[$index]['show_services'] = ! (bool) ($this->updates[$index]['show_services'] ?? false);
    }

    public function updatingMotorbikeSearch(): void
    {
        $term = trim($this->motorbikeSearch);
        if (strlen($term) < 1) {
            $this->motorbikeSuggestions = [];

            return;
        }

        $this->motorbikeSuggestions = Motorbike::query()
            ->where(function ($query) use ($term): void {
                $query->where('reg_no', 'like', "%{$term}%")
                    ->orWhere('make', 'like', "%{$term}%")
                    ->orWhere('model', 'like', "%{$term}%");
                if (ctype_digit($term)) {
                    $query->orWhere('id', (int) $term);
                }
            })
            ->orderBy('reg_no')
            ->limit(8)
            ->get(['id', 'reg_no', 'make', 'model'])
            ->map(fn (Motorbike $bike) => [
                'id' => $bike->id,
                'reg' => $bike->reg_no,
                'label' => trim($bike->reg_no.' · '.trim(($bike->make ?? '').' '.($bike->model ?? '')).' (#'.$bike->id.')'),
            ])
            ->all();
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
        $this->updates = array_values(array_filter($this->updates, fn (array $update): bool => $this->updateHasContent($update)));

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
            'updates' => ['array'],
            'updates.*.job_description' => ['required', 'string', 'max:3000'],
            'updates.*.price' => ['required', 'numeric', 'min:0'],
            'updates.*.note' => ['nullable', 'string', 'max:3000'],
            'updates.*.services' => ['array'],
            'updates.*.services.*' => ['integer', 'exists:motorbike_repair_services_lists,id'],
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

        $creating = ! ($this->motorbikeRepair && $this->motorbikeRepair->exists);

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

            $this->syncUpdates($repair);
            $this->hydrateRepeatables($repair);
        });

        $this->dispatch('flux-admin:toast', type: 'success', message: $creating ? 'Repair created.' : 'Repair updated.');

        if ($creating && $this->motorbikeRepair) {
            $this->redirect(route('flux-admin.motorbike-repairs.edit', $this->motorbikeRepair), navigate: true);
        }
    }

    public function render()
    {
        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);
        $services = MotorbikeRepairServicesList::query()->orderBy('name')->get(['id', 'name', 'price']);

        return view('flux-admin.pages.motorbikes.repair-form', compact('branches', 'services'));
    }

    /**
     * @return array{id: ?int, job_description: string, price: string, note: string, services: list<string>, show_services: bool}
     */
    private function emptyUpdate(): array
    {
        return [
            'id' => null,
            'job_description' => '',
            'price' => '0',
            'note' => '',
            'services' => [],
            'show_services' => false,
        ];
    }

    /** @param  array<string, mixed>  $update */
    private function updateHasContent(array $update): bool
    {
        return trim((string) ($update['job_description'] ?? '')) !== ''
            || trim((string) ($update['note'] ?? '')) !== ''
            || (float) ($update['price'] ?? 0) > 0
            || (is_array($update['services'] ?? null) && $update['services'] !== []);
    }

    private function hydrateRepeatables(MotorbikeRepair $repair): void
    {
        $this->observations = $repair->observations()
            ->orderBy('id')
            ->get(['observation_description'])
            ->map(fn (MotorbikeRepairObservation $observation) => $observation->toArray())
            ->all();

        $this->updates = $repair->updates()
            ->with('services')
            ->orderBy('id')
            ->get()
            ->map(fn (MotorbikeRepairUpdate $update) => [
                'id' => $update->id,
                'job_description' => (string) $update->job_description,
                'price' => (string) $update->price,
                'note' => (string) ($update->note ?? ''),
                'services' => $update->services->pluck('id')->map(fn ($id) => (string) $id)->all(),
                'show_services' => $update->services->isNotEmpty(),
            ])
            ->all();
    }

    private function syncUpdates(MotorbikeRepair $repair): void
    {
        $keptIds = [];

        foreach ($this->updates as $row) {
            $payload = [
                'motorbike_repair_id' => $repair->id,
                'job_description' => (string) ($row['job_description'] ?? ''),
                'price' => $row['price'] ?? 0,
                'note' => $row['note'] ?? null,
            ];

            $id = (int) ($row['id'] ?? 0);
            $update = $id > 0
                ? MotorbikeRepairUpdate::query()->where('motorbike_repair_id', $repair->id)->find($id)
                : null;

            if ($update) {
                $update->update($payload);
            } else {
                $update = MotorbikeRepairUpdate::query()->create($payload);
            }

            $serviceIds = collect($row['services'] ?? [])
                ->map(fn ($serviceId) => (int) $serviceId)
                ->filter(fn ($serviceId) => $serviceId > 0)
                ->unique()
                ->values()
                ->all();

            $update->services()->sync($serviceIds);
            $keptIds[] = $update->id;
        }

        $repair->updates()
            ->whereNotIn('id', $keptIds)
            ->get()
            ->each(function (MotorbikeRepairUpdate $removed): void {
                $removed->services()->detach();
                $removed->delete();
            });
    }
}
