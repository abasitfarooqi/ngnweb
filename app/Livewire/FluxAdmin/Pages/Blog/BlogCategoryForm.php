<?php

namespace App\Livewire\FluxAdmin\Pages\Blog;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\BlogCategory;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Blog Category — Flux Admin')]
class BlogCategoryForm extends Component
{
    use WithAuthorization;

    public ?BlogCategory $blogCategory = null;

    public array $form = [];

    public function mount(?BlogCategory $blogCategory = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->blogCategory = $blogCategory?->id ? $blogCategory : null;

        if ($this->blogCategory) {
            $this->form = $this->blogCategory->getAttributes();
        } else {
            $this->form = [];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.name'          => ['required', 'string', 'max:255'],
            'form.slug'          => ['nullable', 'string', 'max:255'],
            'form.blog_category' => ['nullable', 'string', 'max:255'],
        ]);

        if (empty($this->form['slug']) && ! empty($this->form['name'])) {
            $this->form['slug'] = Str::slug($this->form['name']);
        }

        $payload = [
            'name'          => $this->form['name'],
            'slug'          => $this->form['slug'] ?? null,
            'blog_category' => $this->form['blog_category'] ?? null,
        ];

        if ($this->blogCategory) {
            $this->blogCategory->update($payload);
        } else {
            BlogCategory::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Category saved.');
        $this->redirect(route('flux-admin.blog-categories.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.blog.blog-category-form');
    }
}
