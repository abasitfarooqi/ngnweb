<?php

namespace App\Livewire\FluxAdmin\Pages\Blog;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Blog Post — Flux Admin')]
class BlogPostForm extends Component
{
    use WithAuthorization;

    public ?BlogPost $blogPost = null;

    public array $form = [];

    public function mount(?BlogPost $blogPost = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->blogPost = $blogPost?->id ? $blogPost : null;

        if ($this->blogPost) {
            $this->form = $this->blogPost->getAttributes();
        } else {
            $this->form = [];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.title'           => ['required', 'string', 'max:255'],
            'form.slug'            => ['nullable', 'string', 'max:255'],
            'form.content'         => ['nullable', 'string'],
            'form.category_id'     => ['nullable', 'integer', 'exists:blog_categories,id'],
            'form.seo_title'       => ['nullable', 'string', 'max:255'],
            'form.seo_description' => ['nullable', 'string', 'max:500'],
        ]);

        if (empty($this->form['slug']) && ! empty($this->form['title'])) {
            $this->form['slug'] = Str::slug($this->form['title']);
        }

        $payload = [
            'title'           => $this->form['title'],
            'slug'            => $this->form['slug'] ?? null,
            'content'         => $this->form['content'] ?? null,
            'category_id'     => $this->form['category_id'] ?: null,
            'seo_title'       => $this->form['seo_title'] ?? null,
            'seo_description' => $this->form['seo_description'] ?? null,
        ];

        if ($this->blogPost) {
            $this->blogPost->update($payload);
        } else {
            BlogPost::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Post saved.');
        $this->redirect(route('flux-admin.blog-posts.index'), navigate: true);
    }

    public function render()
    {
        $categories = BlogCategory::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.blog.post-form', compact('categories'));
    }
}
