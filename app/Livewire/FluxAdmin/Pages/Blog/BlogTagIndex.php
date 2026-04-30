<?php

namespace App\Livewire\FluxAdmin\Pages\Blog;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\BlogTag;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Blog tags — Flux Admin')]
class BlogTagIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
    }

    protected function formModel(): string
    {
        return BlogTag::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.name' => ['required', 'string', 'max:255'],
            'formData.slug' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function beforeSave(array $attributes): array
    {
        if (empty($attributes['slug']) && ! empty($attributes['name'])) {
            $attributes['slug'] = Str::slug($attributes['name']);
        }

        return $attributes;
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
        $this->fillFromModel(BlogTag::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        BlogTag::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = BlogTag::query()
            ->when($this->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('flux-admin.pages.blog.tags-index', ['rows' => $rows]);
    }
}
