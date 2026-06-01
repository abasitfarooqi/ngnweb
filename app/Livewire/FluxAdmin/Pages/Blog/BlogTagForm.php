<?php

namespace App\Livewire\FluxAdmin\Pages\Blog;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\BlogTag;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Blog Tag — Flux Admin')]
class BlogTagForm extends Component
{
    use WithAuthorization;

    public ?BlogTag $blogTag = null;

    public array $form = [];

    public function mount(?BlogTag $blogTag = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->blogTag = $blogTag?->id ? $blogTag : null;

        if ($this->blogTag) {
            $this->form = $this->blogTag->getAttributes();
        } else {
            $this->form = [];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.name' => ['required', 'string', 'max:255'],
            'form.slug' => ['nullable', 'string', 'max:255'],
        ]);

        if (empty($this->form['slug']) && ! empty($this->form['name'])) {
            $this->form['slug'] = Str::slug($this->form['name']);
        }

        $payload = [
            'name' => $this->form['name'],
            'slug' => $this->form['slug'] ?? null,
        ];

        if ($this->blogTag) {
            $this->blogTag->update($payload);
        } else {
            BlogTag::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Tag saved.');
        $this->redirect(route('flux-admin.blog-tags.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.blog.blog-tag-form');
    }
}
