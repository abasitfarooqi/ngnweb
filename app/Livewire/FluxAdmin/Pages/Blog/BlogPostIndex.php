<?php

namespace App\Livewire\FluxAdmin\Pages\Blog;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Blog posts — Flux Admin')]
class BlogPostIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return BlogPost::class; }

    protected function formRules(): array
    {
        return [
            'formData.title'           => ['required', 'string', 'max:255'],
            'formData.slug'            => ['nullable', 'string', 'max:255'],
            'formData.content'         => ['nullable', 'string'],
            'formData.category_id'     => ['nullable', 'integer', 'exists:blog_categories,id'],
            'formData.seo_title'       => ['nullable', 'string', 'max:255'],
            'formData.seo_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function beforeSave(array $attributes): array
    {
        if (empty($attributes['slug']) && ! empty($attributes['title'])) {
            $attributes['slug'] = Str::slug($attributes['title']);
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
        $this->fillFromModel(BlogPost::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Post saved.');
    }

    public function delete(int $id): void
    {
        BlogPost::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Post deleted.');
    }

    public function render()
    {
        $rows = BlogPost::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('title', 'like', "%{$v}%")->orWhere('slug', 'like', "%{$v}%")))
            ->when($this->filter('category_id'), fn ($q, $v) => $q->where('category_id', $v))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $categories = BlogCategory::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.blog.posts-index', compact('rows', 'categories'));
    }
}
