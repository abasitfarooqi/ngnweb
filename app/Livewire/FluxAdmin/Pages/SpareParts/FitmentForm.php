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
            'form.year'          => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'form.country_name'  => ['nullable', 'string', 'max:120'],
            'form.country_slug'  => ['nullable', 'string', 'max:120'],
            'form.colour_name'   => ['nullable', 'string', 'max:120'],
            'form.colour_slug'   => ['nullable', 'string', 'max:120'],
            'form.is_active'     => ['boolean'],
        ]);

        if (empty($this->form['country_slug']) && ! empty($this->form['country_name'])) {
            $this->form['country_slug'] = Str::slug($this->form['country_name']);
        }
        if (empty($this->form['colour_slug']) && ! empty($this->form['colour_name'])) {
            $this->form['colour_slug'] = Str::slug($this->form['colour_name']);
        }

        $payload = [
            'model_id'     => $this->form['model_id'],
            'year'         => $this->form['year'] ?? null,
            'country_name' => $this->form['country_name'] ?? null,
            'country_slug' => $this->form['country_slug'] ?? null,
            'colour_name'  => $this->form['colour_name'] ?? null,
            'colour_slug'  => $this->form['colour_slug'] ?? null,
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
