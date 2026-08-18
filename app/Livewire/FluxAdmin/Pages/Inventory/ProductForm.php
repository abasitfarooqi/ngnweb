<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnBrand;
use App\Models\NgnCategory;
use App\Models\NgnModel;
use App\Models\NgnProduct;
use App\Services\NgnProductCatalogService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('flux-admin.layouts.app')]
#[Title('Product — Flux Admin')]
class ProductForm extends Component
{
    use WithAuthorization;
    use WithFileUploads;

    public ?NgnProduct $product = null;

    public array $form = [];

    public array $variants = [];

    public array $gallery = [];

    public array $removeGalleryIds = [];

    public $mainImageUpload = null;

    public array $galleryUploads = [];

    public $videoUpload = null;

    public string $redirectRoute = 'flux-admin.inventory-products.index';

    protected NgnProductCatalogService $catalog;

    public function boot(NgnProductCatalogService $catalog): void
    {
        $this->catalog = $catalog;
    }

    public function mount(?NgnProduct $product = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->product = $product?->id ? $product : null;

        if ($this->product) {
            $loaded = $this->catalog->loadProduct($this->product);
            $this->form = $loaded['form'];
            $this->variants = $loaded['variants'] !== [] ? $loaded['variants'] : [$this->catalog->defaultVariantRow()];
            $this->gallery = $loaded['gallery'];
        } else {
            $this->form = $this->catalog->defaultForm();
            $this->variants = [$this->catalog->defaultVariantRow()];
        }
    }

    public function addVariantRow(): void
    {
        $this->variants[] = $this->catalog->defaultVariantRow();
    }

    public function removeVariantRow(int $index): void
    {
        if (count($this->variants) <= 1) {
            $this->variants = [$this->catalog->defaultVariantRow()];

            return;
        }

        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    public function markGalleryForRemoval(int $imageId): void
    {
        $this->removeGalleryIds[] = $imageId;
        $this->gallery = array_values(array_filter(
            $this->gallery,
            fn (array $row) => (int) ($row['id'] ?? 0) !== $imageId
        ));
    }

    public function save(): void
    {
        $this->validate([
            'form.name'                 => ['required', 'string', 'max:255'],
            'form.sku'                  => ['nullable', 'string', 'max:100', Rule::unique('ngn_products', 'sku')->ignore($this->product?->id)],
            'form.ean'                  => ['nullable', 'string', 'max:50'],
            'form.variation'            => ['nullable', 'string', 'max:255'],
            'form.description'          => ['nullable', 'string'],
            'form.extended_description' => ['nullable', 'string'],
            'form.colour'               => ['nullable', 'string', 'max:100'],
            'form.size_label'           => ['nullable', 'string', 'max:32'],
            'form.brand_id'             => ['nullable', 'integer', 'exists:ngn_brands,id'],
            'form.category_id'          => ['nullable', 'integer', 'exists:ngn_categories,id'],
            'form.model_id'             => ['nullable', 'integer', 'exists:ngn_models,id'],
            'form.normal_price'         => ['nullable', 'numeric', 'min:0'],
            'form.pos_price'            => ['nullable', 'numeric', 'min:0'],
            'form.pos_vat'              => ['nullable', 'numeric', 'min:0'],
            'form.global_stock'         => ['nullable', 'numeric', 'min:0'],
            'form.vatable'              => ['nullable', 'boolean'],
            'form.is_oxford'            => ['nullable', 'boolean'],
            'form.dead'                 => ['nullable', 'boolean'],
            'form.is_ecommerce'         => ['nullable', 'boolean'],
            'form.has_variants'         => ['nullable', 'boolean'],
            'form.slug'                 => ['nullable', 'string', 'max:255'],
            'form.meta_title'           => ['nullable', 'string', 'max:255'],
            'form.meta_description'     => ['nullable', 'string', 'max:500'],
            'form.video_url'            => ['nullable', 'string', 'max:1024'],
            'mainImageUpload'           => ['nullable', 'image', 'max:8192'],
            'galleryUploads.*'          => ['nullable', 'image', 'max:8192'],
            'videoUpload'               => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime', 'max:51200'],
            'variants'                  => ['array'],
            'variants.*.size_label'     => ['nullable', 'string', 'max:32'],
            'variants.*.colour'         => ['nullable', 'string', 'max:100'],
            'variants.*.variation'      => ['nullable', 'string', 'max:255'],
            'variants.*.slug'         => ['nullable', 'string', 'max:255'],
            'variants.*.sku'            => ['nullable', 'string', 'max:100'],
            'variants.*.normal_price'   => ['nullable', 'numeric', 'min:0'],
            'variants.*.global_stock'   => ['nullable', 'numeric', 'min:0'],
            'variants.*.brand_id'       => ['nullable', 'integer', 'exists:ngn_brands,id'],
            'variants.*.category_id'    => ['nullable', 'integer', 'exists:ngn_categories,id'],
            'variants.*.model_id'       => ['nullable', 'integer', 'exists:ngn_models,id'],
            'variants.*.dead'           => ['nullable', 'boolean'],
        ]);

        $this->catalog->save(
            $this->form,
            $this->variants,
            $this->product,
            $this->mainImageUpload,
            $this->galleryUploads,
            $this->videoUpload,
            $this->removeGalleryIds
        );

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Product saved.');
        $this->redirect(route($this->redirectRoute), navigate: true);
    }

    public function render()
    {
        $brands     = NgnBrand::query()->orderBy('name')->get(['id', 'name']);
        $categories = NgnCategory::query()->orderBy('name')->get(['id', 'name']);
        $models     = NgnModel::query()->orderBy('name')->get(['id', 'name']);
        $sizeOptions = NgnProductCatalogService::SIZE_OPTIONS;
        $catalog = $this->catalog;
        $baseSlug = $catalog->resolveBaseSlug($this->form);
        $shopPreviewUrl = $baseSlug !== '' ? route('shop.product', $baseSlug) : null;
        $variantSlugPreviews = [];
        $variantSlugModes = [];

        if (! empty($this->form['has_variants']) && $baseSlug !== '') {
            foreach ($this->variants as $index => $row) {
                $variantSlugPreviews[$index] = $catalog->previewVariantSlug($baseSlug, $row, $index, $this->variants);
                $variantSlugModes[$index] = $catalog->describeVariantSlugMode($baseSlug, $row, $index, $this->variants);
            }
        }

        return view('flux-admin.pages.inventory.product-form', compact(
            'brands',
            'categories',
            'models',
            'sizeOptions',
            'catalog',
            'baseSlug',
            'shopPreviewUrl',
            'variantSlugPreviews',
            'variantSlugModes',
        ))
            ->with('redirectRoute', $this->redirectRoute);
    }
}
