<?php

namespace App\Livewire\FluxAdmin\Pages\Misc;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Motorbike;
use App\Models\RentingPricing;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Rental pricing — Flux Admin')]
class RentingPricingForm extends Component
{
    use WithAuthorization;

    public ?int $recordId = null;

    /** @var array<string, mixed> */
    public array $form = [];

    public string $motorbikeSearch = '';

    /** @var list<array{id: int, reg: string, label: string}> */
    public array $motorbikeSuggestions = [];

    public function mount(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-renting-page');

        if ($id) {
            $this->recordId = $id;
            $record = RentingPricing::query()->findOrFail($id);
            $this->form = $record->getAttributes();

            if (! empty($this->form['update_date'])) {
                try {
                    $this->form['update_date'] = Carbon::parse($this->form['update_date'])->format('Y-m-d');
                } catch (\Throwable) {
                    $this->form['update_date'] = null;
                }
            }

            if (! empty($this->form['motorbike_id'])) {
                $bike = Motorbike::query()->find($this->form['motorbike_id']);
                $this->motorbikeSearch = $bike?->reg_no ?? '';
            }
        } else {
            $this->form = [
                'update_date' => now()->toDateString(),
                'user_id' => backpack_user()?->id,
                'iscurrent' => true,
                'motorbike_id' => null,
            ];
        }
    }

    public function updatedMotorbikeSearch(string $value): void
    {
        if (! empty($this->form['motorbike_id'])) {
            $selected = Motorbike::query()->find($this->form['motorbike_id']);
            $selectedCompact = strtoupper(preg_replace('/\s+/', '', (string) ($selected?->reg_no ?? '')) ?? '');
            $valueCompact = strtoupper(preg_replace('/\s+/', '', $value) ?? '');
            if ($selected && $selectedCompact !== '' && $selectedCompact === $valueCompact) {
                $this->motorbikeSuggestions = [];

                return;
            }
        }

        $this->form['motorbike_id'] = null;

        if (strlen(trim($value)) < 2) {
            $this->motorbikeSuggestions = [];

            return;
        }

        $needle = preg_replace('/\s+/', '', $value) ?? '';
        $this->motorbikeSuggestions = Motorbike::query()
            ->where(function ($q) use ($value, $needle) {
                $q->where('reg_no', 'like', "%{$value}%")
                    ->orWhereRaw("REPLACE(reg_no, ' ', '') LIKE ?", ["%{$needle}%"]);
            })
            ->orderBy('reg_no')
            ->limit(10)
            ->get(['id', 'reg_no', 'make', 'model'])
            ->map(fn (Motorbike $m) => [
                'id' => $m->id,
                'reg' => (string) $m->reg_no,
                'label' => trim($m->reg_no.' — '.($m->make ?? '').' '.($m->model ?? '').' (#'.$m->id.')'),
            ])
            ->values()
            ->all();
    }

    public function selectMotorbike(int $id): void
    {
        $motorbike = Motorbike::query()->find($id);
        if (! $motorbike) {
            return;
        }

        $this->form['motorbike_id'] = $motorbike->id;
        $this->motorbikeSearch = (string) $motorbike->reg_no;
        $this->motorbikeSuggestions = [];
    }

    public function commitMotorbikeSearch(): void
    {
        if (! empty($this->form['motorbike_id'])) {
            return;
        }

        if ($this->motorbikeSuggestions === []) {
            $this->updatedMotorbikeSearch($this->motorbikeSearch);
        }

        if ($this->motorbikeSuggestions === []) {
            return;
        }

        $compact = strtoupper(preg_replace('/\s+/', '', $this->motorbikeSearch) ?? '');
        foreach ($this->motorbikeSuggestions as $suggestion) {
            $reg = strtoupper(preg_replace('/\s+/', '', (string) ($suggestion['reg'] ?? '')) ?? '');
            if ($compact !== '' && $reg === $compact) {
                $this->selectMotorbike((int) $suggestion['id']);

                return;
            }
        }

        if (count($this->motorbikeSuggestions) === 1) {
            $this->selectMotorbike((int) $this->motorbikeSuggestions[0]['id']);
        }
    }

    public function save(): void
    {
        $this->commitMotorbikeSearch();
        $this->form['iscurrent'] = (bool) ($this->form['iscurrent'] ?? false);

        $this->validate([
            'form.motorbike_id' => ['required', 'integer', 'exists:motorbikes,id'],
            'form.user_id' => ['nullable', 'integer'],
            'form.weekly_price' => ['required', 'numeric', 'min:0'],
            'form.minimum_deposit' => ['nullable', 'numeric', 'min:0'],
            'form.update_date' => ['nullable', 'date'],
            'form.iscurrent' => ['boolean'],
        ], [
            'form.motorbike_id.required' => 'Search and select a motorbike by registration (VRM).',
        ]);

        $data = [
            'motorbike_id' => $this->form['motorbike_id'],
            'user_id' => $this->form['user_id'] ?? backpack_user()?->id,
            'weekly_price' => $this->form['weekly_price'],
            'minimum_deposit' => $this->form['minimum_deposit'] ?? null,
            'update_date' => $this->form['update_date'] ?? null,
            'iscurrent' => $this->form['iscurrent'],
        ];

        if ($this->recordId) {
            RentingPricing::query()->findOrFail($this->recordId)->update($data);
        } else {
            RentingPricing::query()->create($data);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
        $this->redirect(route('flux-admin.renting-pricing.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.misc.renting-pricing-form');
    }
}
