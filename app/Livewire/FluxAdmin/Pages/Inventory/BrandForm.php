<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnBrand;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Brand — Flux Admin')]
class BrandForm extends Component
{
    use WithAuthorization;

    public ?NgnBrand $brand = null;

    public array $form = [];

    public function mount(?NgnBrand $brand = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->brand = $brand?->id ? $brand : null;

        if ($this->brand) {
            $this->form = $this->brand->getAttributes();
        } else {
            $this->form = ['is_active' => true, 'is_ecommerce' => false, 'sort_order' => 0];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.name'             => ['required', 'string', 'max:255'],
            'form.slug'             => ['nullable', 'string', 'max:255'],
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

        $payload = [
            'name'             => $this->form['name'],
            'slug'             => $this->form['slug'] ?? null,
            'description'      => $this->form['description'] ?? null,
            'image_url'        => $this->form['image_url'] ?? null,
            'sort_order'       => $this->form['sort_order'] ?? 0,
            'is_active'        => (bool) ($this->form['is_active'] ?? true),
            'is_ecommerce'     => (bool) ($this->form['is_ecommerce'] ?? false),
            'meta_title'       => $this->form['meta_title'] ?? null,
            'meta_description' => $this->form['meta_description'] ?? null,
        ];

        if ($this->brand) {
            $this->brand->update($payload);
        } else {
            NgnBrand::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Brand saved.');
        $this->redirect(route('flux-admin.inventory-brands.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.inventory.brand-form');
    }
}
