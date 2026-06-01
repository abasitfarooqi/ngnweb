<?php

namespace App\Livewire\FluxAdmin\Pages\Surveys;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\NgnSurvey;
use App\Models\NgnSurveyQuestion;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Survey questions — Flux Admin')]
class SurveyQuestionIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-surveys'); }

    protected function formModel(): string { return NgnSurveyQuestion::class; }

    protected function formRules(): array
    {
        return [
            'formData.survey_id'     => ['required', 'integer', 'exists:ngn_surveys,id'],
            'formData.question_text' => ['required', 'string', 'max:500'],
            'formData.question_type' => ['required', 'string', 'in:text,radio,checkbox,select'],
            'formData.is_required'   => ['boolean'],
            'formData.order'         => ['nullable', 'integer'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['question_type' => 'text', 'is_required' => false, 'order' => 0];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(NgnSurveyQuestion::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Question saved.');
    }

    public function delete(int $id): void
    {
        NgnSurveyQuestion::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = NgnSurveyQuestion::query()
            ->with('survey:id,title')
            ->when($this->search, fn ($q, $v) => $q->where('question_text', 'like', "%{$v}%"))
            ->when($this->filter('survey_id'), fn ($q, $v) => $q->where('survey_id', $v))
            ->orderBy('survey_id')->orderBy('order')
            ->paginate($this->perPage);

        $surveys = NgnSurvey::orderBy('title')->get(['id', 'title']);

        return view('flux-admin.pages.surveys.questions', compact('rows', 'surveys'));
    }
}
