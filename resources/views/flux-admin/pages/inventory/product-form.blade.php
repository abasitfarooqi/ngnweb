<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route($redirectRoute ?? 'flux-admin.inventory-products.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Products</a>
                <span>/</span>
                <span>{{ $product ? 'Edit' : 'New product' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $product ? 'Edit product: '.$product->name : 'New product' }}</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Images, video, taxonomy, and optional size/colour variants with per-size pricing and shop URLs.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route($redirectRoute ?? 'flux-admin.inventory-products.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save product</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Product details</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Name" required :error="$errors->first('form.name')">
                    <flux:input wire:model="form.name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="SKU" :error="$errors->first('form.sku')">
                    <flux:input wire:model="form.sku" placeholder="Auto-generated if empty" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="EAN" :error="$errors->first('form.ean')">
                    <flux:input wire:model="form.ean" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Slug" :error="$errors->first('form.slug')">
                    <flux:input wire:model.live="form.slug" placeholder="atom-2-bast-b7-gloss-red-white-blue" />
                    @if($shopPreviewUrl)
                        <p class="mt-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                            Shop URL:
                            <a href="{{ $shopPreviewUrl }}" target="_blank" rel="noopener" class="underline break-all">{{ $shopPreviewUrl }}</a>
                        </p>
                    @else
                        <p class="mt-1.5 text-xs text-zinc-500 dark:text-zinc-400">Leave blank to generate from the product name. Used in <code class="text-[11px]">/shop/product/{slug}</code>.</p>
                    @endif
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Variation label" :error="$errors->first('form.variation')">
                    <flux:input wire:model="form.variation" placeholder="e.g. Matte black" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Colour" :error="$errors->first('form.colour')">
                    <flux:input wire:model="form.colour" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Size (single product)" :error="$errors->first('form.size_label')">
                    <flux:select wire:model="form.size_label" placeholder="None">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach($sizeOptions as $size)
                            <flux:select.option value="{{ $size }}">{{ $size }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Normal price (£)" :error="$errors->first('form.normal_price')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.normal_price" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="POS price (£)" :error="$errors->first('form.pos_price')">
                    <flux:input type="number" step="0.01" min="0" wire:model="form.pos_price" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Global stock" :error="$errors->first('form.global_stock')">
                    <flux:input type="number" step="1" min="0" wire:model="form.global_stock" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Description" :error="$errors->first('form.description')">
                    <flux:textarea wire:model="form.description" rows="4" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Extended description" :error="$errors->first('form.extended_description')">
                    <flux:textarea wire:model="form.extended_description" rows="4" />
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Category, brand &amp; model</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-flux-admin::field-group label="Brand" :error="$errors->first('form.brand_id')">
                    <flux:select wire:model="form.brand_id" variant="listbox" searchable placeholder="Select brand">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach($brands as $b)
                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Category" :error="$errors->first('form.category_id')">
                    <flux:select wire:model="form.category_id" variant="listbox" searchable placeholder="Select category">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach($categories as $c)
                            <flux:select.option value="{{ $c->id }}">{{ $c->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Model" :error="$errors->first('form.model_id')">
                    <flux:select wire:model="form.model_id" variant="listbox" searchable placeholder="Select model">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach($models as $m)
                            <flux:select.option value="{{ $m->id }}">{{ $m->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
            </div>
            <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                Manage lists:
                <a href="{{ route('flux-admin.inventory-brands.index') }}" class="underline">Brands</a>,
                <a href="{{ route('flux-admin.inventory-categories.index') }}" class="underline">Categories</a>,
                <a href="{{ route('flux-admin.inventory-models.index') }}" class="underline">Models</a>.
            </p>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Images &amp; video</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <x-flux-admin::field-group label="Main image" :error="$errors->first('mainImageUpload')">
                        <input type="file" wire:model="mainImageUpload" accept="image/*" class="block w-full text-sm text-zinc-700 dark:text-zinc-300">
                    </x-flux-admin::field-group>
                    @if($mainImageUpload)
                        <img src="{{ $mainImageUpload->temporaryUrl() }}" alt="Preview" class="mt-2 h-32 w-32 object-contain border border-zinc-200 dark:border-zinc-700">
                    @elseif(!empty($form['image_url']))
                        <img src="{{ $catalog->publicAssetUrl($form['image_url']) }}" alt="Current main" class="mt-2 h-32 w-32 object-contain border border-zinc-200 dark:border-zinc-700">
                    @endif
                </div>
                <div>
                    <x-flux-admin::field-group label="Gallery images" :error="$errors->first('galleryUploads.*')">
                        <input type="file" wire:model="galleryUploads" accept="image/*" multiple class="block w-full text-sm text-zinc-700 dark:text-zinc-300">
                    </x-flux-admin::field-group>
                    @if($gallery !== [])
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($gallery as $image)
                                <div class="relative border border-zinc-200 dark:border-zinc-700 p-1">
                                    <img src="{{ $catalog->publicAssetUrl($image['image_url']) }}" alt="Gallery" class="h-20 w-20 object-contain">
                                    <button type="button" wire:click="markGalleryForRemoval({{ $image['id'] }})" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs px-1.5 py-0.5">×</button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Video URL (YouTube, Vimeo, or direct link)" :error="$errors->first('form.video_url')">
                    <flux:input wire:model="form.video_url" placeholder="https://..." />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Or upload MP4/WebM" :error="$errors->first('videoUpload')">
                    <input type="file" wire:model="videoUpload" accept="video/mp4,video/webm,video/quicktime" class="block w-full text-sm text-zinc-700 dark:text-zinc-300">
                    <p class="mt-1 text-xs text-zinc-500">For large files, use Video URL instead. Server upload limit: {{ ini_get('upload_max_filesize') ?: '2M' }}.</p>
                    @if(!empty($form['video_url']) && empty($videoUpload))
                        <p class="mt-2 text-xs text-zinc-500 break-all">{{ $form['video_url'] }}</p>
                    @endif
                </x-flux-admin::field-group>
            </div>
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
                <div>
                    <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Variants</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Enable for multiple sizes, colours, or prices. Leave price blank to inherit the parent price.</p>
                </div>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model.live="form.has_variants" class="accent-zinc-900 dark:accent-zinc-200"> This product has variants
                </label>
            </div>

            <flux:callout variant="info" icon="information-circle" class="mb-4">
                <flux:callout.heading>How shop slugs work</flux:callout.heading>
                <flux:callout.text>
                    <p class="mb-2">Customers open <strong>/shop/product/{slug}</strong>. All variant rows that share a slug appear on one product page with a size/colour selector.</p>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        <li><strong>Shared slug (sizes)</strong> — set <em>Variation</em> to L, XL, 2XL and leave <em>Slug</em> blank. All rows use the parent slug, e.g. <code class="text-[11px]">atom-2-bast-b7-gloss-red-white-blue</code>.</li>
                        <li><strong>Colour in slug</strong> — put the colour in the parent slug or variant <em>Colour</em> field. Sizes under that colour share one slug.</li>
                        <li><strong>Per-size URL</strong> — pick a <em>Size</em> (XS, SM, M…) and leave slug blank. Each row becomes <code class="text-[11px]">{parent}-variant-xs</code>. Visiting that URL pre-selects that size.</li>
                        <li><strong>Custom slug</strong> — type a slug on the row to override auto rules.</li>
                    </ul>
                </flux:callout.text>
            </flux:callout>

            @if($form['has_variants'])
                <div class="space-y-4">
                    @foreach($variants as $index => $variant)
                        <div class="border border-zinc-200 dark:border-zinc-700 p-4" wire:key="variant-row-{{ $index }}">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                                <x-flux-admin::field-group label="Size" :error="$errors->first('variants.'.$index.'.size_label')">
                                    <flux:select wire:model="variants.{{ $index }}.size_label" placeholder="Size">
                                        <flux:select.option value="">—</flux:select.option>
                                        @foreach($sizeOptions as $size)
                                            <flux:select.option value="{{ $size }}">{{ $size }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </x-flux-admin::field-group>
                                <x-flux-admin::field-group label="Colour" :error="$errors->first('variants.'.$index.'.colour')">
                                    <flux:input wire:model="variants.{{ $index }}.colour" />
                                </x-flux-admin::field-group>
                                <x-flux-admin::field-group label="Variation" :error="$errors->first('variants.'.$index.'.variation')">
                                    <flux:input wire:model="variants.{{ $index }}.variation" />
                                </x-flux-admin::field-group>
                                <x-flux-admin::field-group label="Slug (optional)" :error="$errors->first('variants.'.$index.'.slug')">
                                    <flux:input wire:model.live="variants.{{ $index }}.slug" placeholder="Leave blank for auto" />
                                    @if(!empty($variantSlugPreviews[$index]))
                                        <p class="mt-1 text-[11px] text-zinc-500 dark:text-zinc-400">
                                            {{ $variantSlugModes[$index] ?? 'Resolved' }}:
                                            <code class="break-all">{{ $variantSlugPreviews[$index] }}</code>
                                            @if(($variantSlugPreviews[$index] ?? '') !== ($baseSlug ?? ''))
                                                · <a href="{{ route('shop.product', $variantSlugPreviews[$index]) }}" target="_blank" rel="noopener" class="underline">preview</a>
                                            @endif
                                        </p>
                                    @endif
                                </x-flux-admin::field-group>
                                <x-flux-admin::field-group label="SKU" :error="$errors->first('variants.'.$index.'.sku')">
                                    <flux:input wire:model="variants.{{ $index }}.sku" />
                                </x-flux-admin::field-group>
                                <x-flux-admin::field-group label="Price override (£)" :error="$errors->first('variants.'.$index.'.normal_price')">
                                    <flux:input type="number" step="0.01" min="0" wire:model="variants.{{ $index }}.normal_price" placeholder="Optional" />
                                </x-flux-admin::field-group>
                                <x-flux-admin::field-group label="Stock" :error="$errors->first('variants.'.$index.'.global_stock')">
                                    <flux:input type="number" step="1" min="0" wire:model="variants.{{ $index }}.global_stock" />
                                </x-flux-admin::field-group>
                                <x-flux-admin::field-group label="Brand override" :error="$errors->first('variants.'.$index.'.brand_id')">
                                    <flux:select wire:model="variants.{{ $index }}.brand_id" variant="listbox" searchable placeholder="Inherit parent">
                                        <flux:select.option value="">Inherit</flux:select.option>
                                        @foreach($brands as $b)
                                            <flux:select.option value="{{ $b->id }}">{{ $b->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </x-flux-admin::field-group>
                                <x-flux-admin::field-group label="Category override" :error="$errors->first('variants.'.$index.'.category_id')">
                                    <flux:select wire:model="variants.{{ $index }}.category_id" variant="listbox" searchable placeholder="Inherit parent">
                                        <flux:select.option value="">Inherit</flux:select.option>
                                        @foreach($categories as $c)
                                            <flux:select.option value="{{ $c->id }}">{{ $c->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </x-flux-admin::field-group>
                                <x-flux-admin::field-group label="Model override" :error="$errors->first('variants.'.$index.'.model_id')">
                                    <flux:select wire:model="variants.{{ $index }}.model_id" variant="listbox" searchable placeholder="Inherit parent">
                                        <flux:select.option value="">Inherit</flux:select.option>
                                        @foreach($models as $m)
                                            <flux:select.option value="{{ $m->id }}">{{ $m->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </x-flux-admin::field-group>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                                    <input type="checkbox" wire:model="variants.{{ $index }}.dead" class="accent-zinc-900 dark:accent-zinc-200"> Discontinued
                                </label>
                                <flux:button type="button" wire:click="removeVariantRow({{ $index }})" variant="ghost" size="sm" class="!rounded-none text-red-600">Remove row</flux:button>
                            </div>
                        </div>
                    @endforeach
                    <flux:button type="button" wire:click="addVariantRow" variant="outline" size="sm" class="!rounded-none">Add variant row</flux:button>
                </div>
            @endif
        </div>

        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">SEO &amp; visibility</h2>
            <div class="flux-admin-form-grid grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Meta title" :error="$errors->first('form.meta_title')">
                    <flux:input wire:model="form.meta_title" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Meta description" :error="$errors->first('form.meta_description')">
                    <flux:input wire:model="form.meta_description" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4 flex flex-wrap gap-6">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.vatable" class="accent-zinc-900 dark:accent-zinc-200"> Vatable
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_oxford" class="accent-zinc-900 dark:accent-zinc-200"> Oxford product
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.is_ecommerce" class="accent-zinc-900 dark:accent-zinc-200"> Visible on shop
                </label>
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <input type="checkbox" wire:model="form.dead" class="accent-zinc-900 dark:accent-zinc-200"> Dead (discontinued)
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route($redirectRoute ?? 'flux-admin.inventory-products.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save product</flux:button>
        </div>
    </form>
</div>
