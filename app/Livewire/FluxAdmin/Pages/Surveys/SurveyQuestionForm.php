<?php

namespace App\Livewire\FluxAdmin\Pages\Surveys;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnSurvey;
use App\Models\NgnSurveyQuestion;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Survey Question — Flux Admin')]
class SurveyQuestionForm extends Component
{
    use WithAuthorization;

    public ?NgnSurveyQuestion $surveyQuestion = null;

    public array $form = [];

    public function mount(?NgnSurveyQuestion $surveyQuestion = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-surveys');
        $this->surveyQuestion = $surveyQuestion?->id ? $surveyQuestion : null;

        if ($this->surveyQuestion) {
            $this->form = $this->surveyQuestion->getAttributes();
        } else {
            $this->form = ['question_type' => 'text', 'is_required' => false, 'order' => 0];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.survey_id'     => ['required', 'integer', 'exists:ngn_surveys,id'],
            'form.question_text' => ['required', 'string', 'max:500'],
            'form.question_type' => ['required', 'string', 'in:text,radio,checkbox,select'],
            'form.is_required'   => ['boolean'],
            'form.order'         => ['nullable', 'integer'],
        ]);

        $payload = [
            'survey_id'     => $this->form['survey_id'],
            'question_text' => $this->form['question_text'],
            'question_type' => $this->form['question_type'],
            'is_required'   => (bool) ($this->form['is_required'] ?? false),
            'order'         => $this->form['order'] ?? 0,
        ];

        if ($this->surveyQuestion) {
            $this->surveyQuestion->update($payload);
        } else {
            NgnSurveyQuestion::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Question saved.');
        $this->redirect(route('flux-admin.survey-questions.index'), navigate: true);
    }

    public function render()
    {
        $surveys = NgnSurvey::orderBy('title')->get(['id', 'title']);

        return view('flux-admin.pages.surveys.question-form', compact('surveys'));
    }
}
