<?php

namespace App\Livewire\FluxAdmin\Pages\Surveys;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\NgnSurveyAnswer;
use App\Models\NgnSurveyResponse;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Survey answers — Flux Admin')]
class SurveyAnswerIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void { $this->authorizeModule('see-menu-surveys'); }

    public function render()
    {
        $rows = NgnSurveyAnswer::query()
            ->with(['response:id,survey_id,contact_name', 'response.survey:id,title'])
            ->when($this->search, fn ($q, $v) => $q->where('answer_text', 'like', "%{$v}%"))
            ->when($this->filter('response_id'), fn ($q, $v) => $q->where('response_id', $v))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.surveys.answers', ['rows' => $rows]);
    }
}
