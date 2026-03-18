<?php

namespace App\Livewire\Shop;

use App\Services\ShopService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BlogIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    protected ShopService $shop;

    public function boot(ShopService $shop): void
    {
        $this->shop = $shop;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $posts = $this->shop->getBlogPosts(9, $this->getPage());

        return view('livewire.shop.blog-index', compact('posts'))
            ->layout('components.layouts.public', [
                'title'       => 'Motorcycle Blog & News | NGN Motors',
                'description' => 'Read our latest motorcycle tips, guides, news and updates from NGN Motors London.',
            ]);
    }
}
