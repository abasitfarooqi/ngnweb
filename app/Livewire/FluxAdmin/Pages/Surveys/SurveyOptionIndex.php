<?php

namespace App\Livewire\FluxAdmin\Pages\Surveys;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\NgnSurveyOption;
use App\Models\NgnSurveyQuestion;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Survey options — Flux Admin')]
class SurveyOptionIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-surveys'); }

    protected function formModel(): string { return NgnSurveyOption::class; }

    protected function formRules(): array
    {
        return [
            'formData.question_id' => ['required', 'integer', 'exists:ngn_survey_questions,id'],
            'formData.option_text' => ['required', 'string', 'max:500'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(NgnSurveyOption::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Option saved.');
    }

    public function delete(int $id): void
    {
        NgnSurveyOption::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = NgnSurveyOption::query()
            ->with(['question:id,question_text,survey_id', 'question.survey:id,title'])
            ->when($this->search, fn ($q, $v) => $q->where('option_text', 'like', "%{$v}%"))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $questions = NgnSurveyQuestion::orderBy('id')->get(['id', 'question_text']);

        return view('flux-admin.pages.surveys.options', compact('rows', 'questions'));
    }
}
