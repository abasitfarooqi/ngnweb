<?php

namespace App\Livewire\FluxAdmin\Pages\Surveys;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\NgnSurvey;
use App\Models\NgnSurveyResponse;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Survey responses — Flux Admin')]
class SurveyResponseIndex extends Component
{
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-surveys');
        $this->exportable = true;
        $this->exportFilename = 'survey-responses';
    }

    public function delete(int $id): void
    {
        NgnSurveyResponse::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Response deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['survey:id,title', 'customer:id,first_name,last_name', 'answers'])
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $surveys = NgnSurvey::orderBy('title')->get(['id', 'title']);

        return view('flux-admin.pages.surveys.responses', compact('rows', 'surveys'));
    }

    protected function baseQuery(): Builder
    {
        return NgnSurveyResponse::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('contact_name', 'like', "%{$v}%")->orWhere('contact_email', 'like', "%{$v}%")))
            ->when($this->filter('survey_id'), fn ($q, $v) => $q->where('survey_id', $v));
    }

    protected function exportQuery(): Builder { return $this->baseQuery()->with('survey:id,title'); }

    protected function exportColumns(): array
    {
        return [
            'ID'           => 'id',
            'Survey'       => fn ($r) => $r->survey?->title,
            'Name'         => 'contact_name',
            'Email'        => 'contact_email',
            'Phone'        => 'contact_phone',
            'Opt-in'       => fn ($r) => $r->is_contact_opt_in ? 'Yes' : 'No',
            'Submitted'    => fn ($r) => $r->created_at?->format('Y-m-d H:i'),
        ];
    }
}
