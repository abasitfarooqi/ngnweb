<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnCategory;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Category — Flux Admin')]
class CategoryForm extends Component
{
    use WithAuthorization;

    public ?NgnCategory $category = null;

    public array $form = [];

    public function mount(?NgnCategory $category = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->category = $category?->id ? $category : null;

        if ($this->category) {
            $this->form = $this->category->getAttributes();
        } else {
            $this->form = ['is_active' => true, 'is_ecommerce' => false, 'sort_order' => 0];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.name'             => ['required', 'string', 'max:255'],
            'form.slug'             => ['nullable', 'string', 'max:255'],
            'form.super_category_id'=> ['nullable', 'integer', 'exists:ngn_categories,id'],
            'form.description'      => ['nullable', 'string'],
            'form.image_url'        => ['nullable', 'string', 'max:1024'],
            'form.sort_order'       => ['nullable', 'integer', 'min:0'],
            'form.is_active'        => ['boolean'],
            'form.is_ecommerce'     => ['boolean'],
            'form.meta_title'       => ['nullable', 'string', 'max:255'],
            'form.meta_description' => ['nullable', 'string'],
        ]);

        if (empty($this->form['slug']) && ! empty($this->form['name'])) {
            $this->form['slug'] = Str::slug($this->form['name']);
        }
        if (($this->form['super_category_id'] ?? null) === '') {
            $this->form['super_category_id'] = null;
        }

        $payload = [
            'name'              => $this->form['name'],
            'slug'              => $this->form['slug'] ?? null,
            'super_category_id' => $this->form['super_category_id'] ?? null,
            'description'       => $this->form['description'] ?? null,
            'image_url'         => $this->form['image_url'] ?? null,
            'sort_order'        => $this->form['sort_order'] ?? 0,
            'is_active'         => (bool) ($this->form['is_active'] ?? true),
            'is_ecommerce'      => (bool) ($this->form['is_ecommerce'] ?? false),
            'meta_title'        => $this->form['meta_title'] ?? null,
            'meta_description'  => $this->form['meta_description'] ?? null,
        ];

        if ($this->category) {
            $this->category->update($payload);
        } else {
            NgnCategory::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Category saved.');
        $this->redirect(route('flux-admin.inventory-categories.index'), navigate: true);
    }

    public function render()
    {
        $superCats = NgnCategory::query()->whereNull('super_category_id')->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.inventory.category-form', compact('superCats'));
    }
}
