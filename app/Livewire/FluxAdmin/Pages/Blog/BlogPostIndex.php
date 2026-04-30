<?php

namespace App\Livewire\FluxAdmin\Pages\Blog;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\BlogPost;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Blog posts — Flux Admin')]
class BlogPostIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
    }

    public function render()
    {
        $rows = BlogPost::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('title', 'like', "%{$v}%")->orWhere('slug', 'like', "%{$v}%")))
            ->when($this->filter('category_id'), fn ($q, $v) => $q->where('category_id', $v))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $categories = \App\Models\BlogCategory::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.blog.posts-index', compact('rows', 'categories'));
    }

    public function delete(int $id): void
    {
        BlogPost::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Post deleted.');
    }
}
