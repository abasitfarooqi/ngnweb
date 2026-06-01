<?php

namespace App\Livewire\FluxAdmin\Pages\Surveys;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnSurveyOption;
use App\Models\NgnSurveyQuestion;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Survey Option — Flux Admin')]
class SurveyOptionForm extends Component
{
    use WithAuthorization;

    public ?NgnSurveyOption $surveyOption = null;

    public array $form = [];

    public function mount(?NgnSurveyOption $surveyOption = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-surveys');
        $this->surveyOption = $surveyOption?->id ? $surveyOption : null;

        if ($this->surveyOption) {
            $this->form = $this->surveyOption->getAttributes();
        } else {
            $this->form = [];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.question_id' => ['required', 'integer', 'exists:ngn_survey_questions,id'],
            'form.option_text' => ['required', 'string', 'max:500'],
        ]);

        $payload = [
            'question_id' => $this->form['question_id'],
            'option_text' => $this->form['option_text'],
        ];

        if ($this->surveyOption) {
            $this->surveyOption->update($payload);
        } else {
            NgnSurveyOption::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Option saved.');
        $this->redirect(route('flux-admin.survey-options.index'), navigate: true);
    }

    public function render()
    {
        $questions = NgnSurveyQuestion::orderBy('id')->get(['id', 'question_text']);

        return view('flux-admin.pages.surveys.option-form', compact('questions'));
    }
}
