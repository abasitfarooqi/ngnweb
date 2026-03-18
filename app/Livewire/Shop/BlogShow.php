<?php

namespace App\Livewire\Shop;

use App\Services\ShopService;
use Livewire\Component;

class BlogShow extends Component
{
    public string $slug;

    protected ShopService $shop;

    public function boot(ShopService $shop): void
    {
        $this->shop = $shop;
    }

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $post = $this->shop->getBlogPost($this->slug);

        if (!$post) {
            abort(404);
        }

        return view('livewire.shop.blog-show', compact('post'))
            ->layout('components.layouts.public', [
                'title'       => ($post->seo_title ?: $post->title) . ' | NGN Motors Blog',
                'description' => $post->seo_description ?: substr(strip_tags($post->content), 0, 160),
            ]);
    }
}
