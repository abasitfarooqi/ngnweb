<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\ClaimMotorbike;
use App\Models\Motorbike;
use App\Support\FluxAdminFormPayload;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Motorbike Claim — Flux Admin')]
class ClaimForm extends Component
{
    use WithAuthorization;

    public ?ClaimMotorbike $claimMotorbike = null;

    public array $form = [];

    public string $motorbikeSearch = '';
    public array $motorbikeSuggestions = [];

    public function mount(?ClaimMotorbike $claimMotorbike = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->claimMotorbike = $claimMotorbike;

        if ($claimMotorbike && $claimMotorbike->exists) {
            $attrs = $claimMotorbike->getAttributes();
            foreach (['case_date', 'received_date', 'returned_date'] as $f) {
                if (! empty($attrs[$f])) {
                    try {
                        $attrs[$f] = \Carbon\Carbon::parse($attrs[$f])->format('Y-m-d');
                    } catch (\Throwable) {
                        $attrs[$f] = null;
                    }
                }
            }
            $this->form = $attrs;
            $this->motorbikeSearch = $claimMotorbike->motorbike?->reg_no ?? '';
        } else {
            $this->form = [
                'case_date'   => now()->toDateString(),
                'user_id'     => FluxAdminFormPayload::adminUserId(),
                'is_received' => false,
                'is_returned' => false,
                'notes'       => '',
                'email'       => '',
                'phone'       => '',
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

        $this->validate([
            'form.fullname'      => ['required', 'string', 'max:255'],
            'form.email'         => ['required', 'email', 'max:255'],
            'form.phone'         => ['required', 'string', 'max:50'],
            'form.case_date'     => ['required', 'date'],
            'form.motorbike_id'  => ['required', 'integer', 'exists:motorbikes,id'],
            'form.branch_id'     => ['required', 'integer', 'exists:branches,id'],
            'form.notes'         => ['nullable', 'string'],
            'form.is_received'   => ['boolean'],
            'form.received_date' => ['nullable', 'date'],
            'form.is_returned'   => ['boolean'],
            'form.returned_date' => ['nullable', 'date'],
            'form.user_id'       => ['nullable', 'integer'],
        ]);

        $data = FluxAdminFormPayload::onlyPersistable(ClaimMotorbike::class, [
            'fullname'      => $this->form['fullname'],
            'email'         => $this->form['email'],
            'phone'         => $this->form['phone'],
            'case_date'     => $this->form['case_date'],
            'motorbike_id'  => $this->form['motorbike_id'],
            'branch_id'     => $this->form['branch_id'],
            'notes'         => trim((string) ($this->form['notes'] ?? '')) !== '' ? $this->form['notes'] : '—',
            'is_received'   => (bool) ($this->form['is_received'] ?? false),
            'received_date' => $this->form['received_date'] ?? null,
            'is_returned'   => (bool) ($this->form['is_returned'] ?? false),
            'returned_date' => $this->form['returned_date'] ?? null,
            'user_id'       => $this->form['user_id'] ?? FluxAdminFormPayload::adminUserId(),
        ]);

        if ($this->claimMotorbike && $this->claimMotorbike->exists) {
            $this->claimMotorbike->update($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Claim updated.');
        } else {
            ClaimMotorbike::create($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Claim created.');
        }

        $this->redirect(route('flux-admin.motorbike-claims.index'), navigate: true);
    }

    public function render()
    {
        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.vehicles.claim-form', compact('branches'));
    }
}
