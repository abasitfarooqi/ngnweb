<?php

namespace App\Livewire\FluxAdmin\Pages\Surveys;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnSurvey;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Survey — Flux Admin')]
class SurveyForm extends Component
{
    use WithAuthorization;

    public ?NgnSurvey $survey = null;

    public array $form = [];

    public function mount(?NgnSurvey $survey = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-surveys');
        $this->survey = $survey?->id ? $survey : null;

        if ($this->survey) {
            $this->form = $this->survey->getAttributes();
        } else {
            $this->form = ['is_active' => true];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.title'       => ['required', 'string', 'max:255'],
            'form.slug'        => ['nullable', 'string', 'max:255'],
            'form.description' => ['nullable', 'string'],
            'form.is_active'   => ['boolean'],
        ]);

        $payload = [
            'title'       => $this->form['title'],
            'slug'        => $this->form['slug'] ?? null,
            'description' => $this->form['description'] ?? null,
            'is_active'   => (bool) ($this->form['is_active'] ?? true),
        ];

        if ($this->survey) {
            $this->survey->update($payload);
        } else {
            NgnSurvey::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Survey saved.');
        $this->redirect(route('flux-admin.surveys.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.surveys.survey-form');
    }
}
