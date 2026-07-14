<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\SpFitment;
use App\Models\SpModel;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('SP Fitment — Flux Admin')]
class FitmentForm extends Component
{
    use WithAuthorization;

    public ?SpFitment $spFitment = null;

    public array $form = [];

    public function mount(?SpFitment $spFitment = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->spFitment = $spFitment?->id ? $spFitment : null;

        if ($this->spFitment) {
            $this->form = $this->spFitment->getAttributes();
        } else {
            $this->form = ['is_active' => true];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.model_id'      => ['required', 'integer', 'exists:sp_models,id'],
            'form.year'          => ['required', 'integer', 'min:1900', 'max:2100'],
            'form.country_name'  => ['required', 'string', 'max:120'],
            'form.country_slug'  => ['nullable', 'string', 'max:120'],
            'form.colour_name'   => ['required', 'string', 'max:120'],
            'form.colour_slug'   => ['nullable', 'string', 'max:120'],
            'form.is_active'     => ['boolean'],
        ], [], [
            'form.country_name' => 'country',
            'form.colour_name'  => 'colour',
        ]);

        if (empty($this->form['country_slug']) && ! empty($this->form['country_name'])) {
            $this->form['country_slug'] = Str::slug($this->form['country_name']);
        }
        if (empty($this->form['colour_slug']) && ! empty($this->form['colour_name'])) {
            $this->form['colour_slug'] = Str::slug($this->form['colour_name']);
        }

        $duplicate = SpFitment::query()
            ->where('model_id', $this->form['model_id'])
            ->where('year', $this->form['year'] ?? null)
            ->where('country_slug', $this->form['country_slug'] ?? null)
            ->where('colour_slug', $this->form['colour_slug'] ?? null)
            ->when($this->spFitment?->id, fn ($q) => $q->whereKeyNot($this->spFitment->id))
            ->exists();

        if ($duplicate) {
            $this->addError('form.year', 'A fitment with this model, year, country and colour already exists.');

            return;
        }

        $payload = [
            'model_id'     => $this->form['model_id'],
            'year'         => $this->form['year'],
            'country_name' => $this->form['country_name'] ?? '',
            'country_slug' => $this->form['country_slug'] ?? '',
            'colour_name'  => $this->form['colour_name'] ?? '',
            'colour_slug'  => $this->form['colour_slug'] ?? '',
            'is_active'    => (bool) ($this->form['is_active'] ?? true),
        ];

        if ($this->spFitment) {
            $this->spFitment->update($payload);
        } else {
            SpFitment::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Fitment saved.');
        $this->redirect(route('flux-admin.sp-fitments.index'), navigate: true);
    }

    public function render()
    {
        $models = SpModel::query()->with('make:id,name')->orderBy('name')->get();

        return view('flux-admin.pages.spare-parts.fitment-form', compact('models'));
    }
}
