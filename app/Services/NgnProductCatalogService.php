<?php

namespace App\Services;

use App\Models\NgnProduct;
use App\Models\NgnProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class NgnProductCatalogService
{
    public const SIZE_OPTIONS = ['XS', 'S', 'SM', 'M', 'L', 'XL', 'XXL', 'One size'];

    public function defaultForm(): array
    {
        return [
            'sku'                  => '',
            'ean'                  => '',
            'name'                 => '',
            'variation'            => '',
            'description'          => '',
            'extended_description' => '',
            'colour'               => '',
            'size_label'           => '',
            'brand_id'             => '',
            'category_id'          => '',
            'model_id'             => '',
            'normal_price'         => '',
            'pos_price'            => '',
            'pos_vat'              => '',
            'global_stock'         => '',
            'vatable'              => true,
            'is_oxford'            => false,
            'dead'                 => false,
            'is_ecommerce'         => true,
            'slug'                 => '',
            'meta_title'           => '',
            'meta_description'     => '',
            'video_url'            => '',
            'image_url'            => '',
            'has_variants'         => false,
        ];
    }

    public function defaultVariantRow(): array
    {
        return [
            'id'           => null,
            'slug'         => '',
            'size_label'   => '',
            'colour'       => '',
            'variation'    => '',
            'sku'          => '',
            'normal_price' => '',
            'global_stock' => '',
            'brand_id'     => '',
            'category_id'  => '',
            'model_id'     => '',
            'dead'         => false,
        ];
    }

    public function loadProduct(NgnProduct $product): array
    {
        $mapVariant = fn (NgnProduct $variant) => [
            'id'           => $variant->id,
            'slug'         => $variant->slug ?? '',
            'size_label'   => $variant->size_label ?? '',
            'colour'       => $variant->colour ?? '',
            'variation'    => $variant->variation ?? '',
            'sku'          => $variant->sku ?? '',
            'normal_price' => $variant->normal_price,
            'global_stock' => $variant->global_stock,
            'brand_id'     => $variant->brand_id,
            'category_id'  => $variant->category_id,
            'model_id'     => $variant->model_id,
            'dead'         => (bool) $variant->dead,
        ];

        $variants = $product->childVariants()
            ->orderBy('size_label')
            ->orderBy('id')
            ->get()
            ->map($mapVariant)
            ->values()
            ->all();

        if ($variants === [] && trim((string) $product->slug) !== '') {
            $siblings = NgnProduct::query()
                ->where('slug', $product->slug)
                ->whereNull('parent_product_id')
                ->orderBy('id')
                ->get();

            if ($siblings->count() > 1) {
                $variants = $siblings->map($mapVariant)->values()->all();
                $product = $siblings->first();
            }
        }

        $form = array_merge($this->defaultForm(), $product->only(array_keys($this->defaultForm())));
        $form['has_variants'] = $variants !== [];

        $gallery = $product->productImages()
            ->orderBy('id')
            ->get(['id', 'image_url'])
            ->map(fn (NgnProductImage $image) => [
                'id'        => $image->id,
                'image_url' => $image->image_url,
            ])
            ->values()
            ->all();

        return compact('form', 'variants', 'gallery');
    }

    public function save(
        array $form,
        array $variants,
        ?NgnProduct $product = null,
        mixed $mainImageUpload = null,
        array $galleryUploads = [],
        mixed $videoUpload = null,
        array $removeGalleryIds = []
    ): NgnProduct {
        return DB::transaction(function () use ($form, $variants, $product, $mainImageUpload, $galleryUploads, $videoUpload, $removeGalleryIds) {
            $payload = $this->normaliseParentPayload($form);

            if (! empty($form['has_variants'])) {
                $payload['variation'] = null;
                $payload['size_label'] = null;
            }

            if ($mainImageUpload) {
                $payload['image_url'] = $this->storeImage($mainImageUpload);
            }

            if ($videoUpload) {
                $payload['video_url'] = $this->storeVideo($videoUpload);
            } elseif (array_key_exists('video_url', $form)) {
                $payload['video_url'] = trim((string) ($form['video_url'] ?? '')) ?: null;
            }

            if ($product) {
                $product->update($payload);
            } else {
                $product = NgnProduct::create($payload);
            }

            $this->syncGallery($product, $galleryUploads, $removeGalleryIds);

            if (! empty($form['has_variants'])) {
                $this->syncVariants($product, $variants, $payload);
            } else {
                $product->childVariants()->delete();
            }

            return $product->fresh(['productImages', 'childVariants']);
        });
    }

    public function storeImage(mixed $file): string
    {
        $stored = $this->storeUpload($file, 'product_images');

        return $stored;
    }

    public function storeVideo(mixed $file): string
    {
        return $this->storeUpload($file, 'product_videos');
    }

    public function variantLabel(NgnProduct $variant): string
    {
        $parts = array_filter([
            $variant->size_label,
            $variant->variation,
            $variant->colour,
            $variant->sku,
        ]);

        return $parts !== [] ? implode(' · ', $parts) : 'Variant';
    }

    public function resolveBaseSlug(array $form): string
    {
        $slug = trim((string) ($form['slug'] ?? ''));
        if ($slug !== '') {
            return Str::slug($slug);
        }

        return Str::slug((string) ($form['name'] ?? ''));
    }

    public function previewVariantSlug(string $baseSlug, array $row, int $index, array $allRows): string
    {
        if ($baseSlug === '' || $this->variantRowIsEmpty($row)) {
            return '';
        }

        return $this->variantSlug($baseSlug, $row, $index, $allRows);
    }

    public function describeVariantSlugMode(string $baseSlug, array $row, int $index, array $allRows): string
    {
        if ($baseSlug === '' || $this->variantRowIsEmpty($row)) {
            return '';
        }

        $resolved = $this->variantSlug($baseSlug, $row, $index, $allRows);
        $custom = trim((string) ($row['slug'] ?? ''));
        $size = trim((string) ($row['size_label'] ?? ''));
        $colour = trim((string) ($row['colour'] ?? ''));
        $variation = trim((string) ($row['variation'] ?? ''));

        if ($custom !== '') {
            return 'Custom slug';
        }

        if ($colour !== '') {
            return 'Colour line (shared slug)';
        }

        if ($variation !== '' && $size === '') {
            return 'Size in variation (shared slug)';
        }

        if ($size !== '') {
            return 'Per-size URL';
        }

        return 'Inherits parent slug';
    }

    private function normaliseParentPayload(array $form): array
    {
        $slug = trim((string) ($form['slug'] ?? ''));
        if ($slug === '' && ! empty($form['name'])) {
            $slug = Str::slug((string) $form['name']);
        }

        return [
            'sku'                  => trim((string) ($form['sku'] ?? '')) ?: $this->generateSku((string) ($form['name'] ?? 'product')),
            'ean'                  => $this->nullableString($form['ean'] ?? null),
            'name'                 => trim((string) $form['name']),
            'variation'            => $this->nullableString($form['variation'] ?? null),
            'description'          => $this->nullableString($form['description'] ?? null),
            'extended_description' => $this->nullableString($form['extended_description'] ?? null),
            'colour'               => $this->nullableString($form['colour'] ?? null),
            'size_label'           => $this->nullableString($form['size_label'] ?? null),
            'brand_id'             => $this->nullableInt($form['brand_id'] ?? null) ?? 1,
            'category_id'          => $this->nullableInt($form['category_id'] ?? null) ?? 1,
            'model_id'             => $this->nullableInt($form['model_id'] ?? null) ?? 1,
            'normal_price'         => $this->nullableDecimal($form['normal_price'] ?? null) ?? 0,
            'pos_price'            => $this->nullableDecimal($form['pos_price'] ?? null) ?? 0,
            'pos_vat'              => $this->nullableDecimal($form['pos_vat'] ?? null) ?? 0,
            'global_stock'         => $this->nullableDecimal($form['global_stock'] ?? null) ?? 0,
            'vatable'              => (bool) ($form['vatable'] ?? false),
            'is_oxford'            => (bool) ($form['is_oxford'] ?? false),
            'dead'                 => (bool) ($form['dead'] ?? false),
            'is_ecommerce'         => (bool) ($form['is_ecommerce'] ?? false),
            'slug'                 => $slug,
            'meta_title'           => $this->nullableString($form['meta_title'] ?? null) ?? '',
            'meta_description'     => $this->nullableString($form['meta_description'] ?? null) ?? '',
            'parent_product_id'    => null,
        ];
    }

    private function syncVariants(NgnProduct $parent, array $variants, array $parentPayload): void
    {
        if ($this->usesSharedVariantSlug($parentPayload, $variants)) {
            $this->syncSharedSlugVariants($parent, $variants, $parentPayload);

            return;
        }

        $keptIds = [];

        foreach ($variants as $index => $row) {
            if ($this->variantRowIsEmpty($row)) {
                continue;
            }

            $variantPayload = $this->buildVariantPayload($parent, $parentPayload, $row, $index, $variants, true);

            if (! empty($row['id'])) {
                $variant = NgnProduct::query()
                    ->where('parent_product_id', $parent->id)
                    ->whereKey($row['id'])
                    ->first();

                if ($variant) {
                    $variant->update($variantPayload);
                    $keptIds[] = $variant->id;

                    continue;
                }
            }

            $created = NgnProduct::create($variantPayload);
            $keptIds[] = $created->id;
        }

        $parent->childVariants()->whereNotIn('id', $keptIds)->delete();
    }

    private function syncSharedSlugVariants(NgnProduct $parent, array $variants, array $parentPayload): void
    {
        $keptIds = [];
        $sharedSlug = $parentPayload['slug'];

        foreach ($variants as $index => $row) {
            if ($this->variantRowIsEmpty($row)) {
                continue;
            }

            $variantPayload = $this->buildVariantPayload($parent, $parentPayload, $row, $index, $variants, false);
            $variantPayload['slug'] = $sharedSlug;
            $variantPayload['parent_product_id'] = null;

            if (! empty($row['id'])) {
                $existing = NgnProduct::query()->whereKey($row['id'])->first();
                if ($existing) {
                    $existing->update($variantPayload);
                    $keptIds[] = $existing->id;

                    continue;
                }
            }

            if ($index === 0) {
                $parent->update($variantPayload);
                $keptIds[] = $parent->id;

                continue;
            }

            $created = NgnProduct::create($variantPayload);
            $keptIds[] = $created->id;
        }

        NgnProduct::query()
            ->where('slug', $sharedSlug)
            ->whereNull('parent_product_id')
            ->whereNotIn('id', $keptIds)
            ->delete();

        $parent->childVariants()->delete();
    }

    private function buildVariantPayload(
        NgnProduct $parent,
        array $parentPayload,
        array $row,
        int $index,
        array $variants,
        bool $asChild
    ): array {
        return [
            'parent_product_id'    => $asChild ? $parent->id : null,
            'name'                 => $parentPayload['name'],
            'description'          => $parentPayload['description'],
            'extended_description' => $parentPayload['extended_description'],
            'slug'                 => $this->variantSlug($parentPayload['slug'], $row, $index, $variants),
            'size_label'           => $this->nullableString($row['size_label'] ?? null),
            'colour'               => $this->nullableString($row['colour'] ?? null),
            'variation'            => $this->nullableString($row['variation'] ?? null),
            'sku'                  => trim((string) ($row['sku'] ?? '')) ?: $this->generateSku($parentPayload['slug'].'-'.$index),
            'brand_id'             => $this->nullableInt($row['brand_id'] ?? null) ?? $parentPayload['brand_id'],
            'category_id'          => $this->nullableInt($row['category_id'] ?? null) ?? $parentPayload['category_id'],
            'model_id'             => $this->nullableInt($row['model_id'] ?? null) ?? $parentPayload['model_id'],
            'normal_price'         => $this->nullableDecimal($row['normal_price'] ?? null) ?? $parentPayload['normal_price'],
            'pos_price'            => $this->nullableDecimal($row['normal_price'] ?? null) ?? $parentPayload['pos_price'],
            'global_stock'         => $this->nullableDecimal($row['global_stock'] ?? null) ?? 0,
            'vatable'              => $parentPayload['vatable'],
            'is_oxford'            => $parentPayload['is_oxford'],
            'dead'                 => (bool) ($row['dead'] ?? false),
            'is_ecommerce'         => $parentPayload['is_ecommerce'],
            'meta_title'           => $parentPayload['meta_title'],
            'meta_description'     => $parentPayload['meta_description'],
            'image_url'            => $parent->image_url,
            'video_url'            => $parent->video_url,
        ];
    }

    private function usesSharedVariantSlug(array $parentPayload, array $variants): bool
    {
        $baseSlug = trim((string) ($parentPayload['slug'] ?? ''));
        if ($baseSlug === '') {
            return false;
        }

        $hasRow = false;

        foreach ($variants as $index => $row) {
            if ($this->variantRowIsEmpty($row)) {
                continue;
            }

            $hasRow = true;

            if ($this->variantSlug($baseSlug, $row, $index, $variants) !== $baseSlug) {
                return false;
            }
        }

        return $hasRow;
    }

    private function syncGallery(NgnProduct $product, array $galleryUploads, array $removeGalleryIds): void
    {
        if ($removeGalleryIds !== []) {
            NgnProductImage::query()
                ->where('product_id', $product->id)
                ->whereIn('id', $removeGalleryIds)
                ->delete();
        }

        foreach ($galleryUploads as $upload) {
            if (! $upload) {
                continue;
            }

            NgnProductImage::create([
                'product_id' => $product->id,
                'sku'        => $product->sku,
                'image_url'  => $this->storeImage($upload),
            ]);
        }
    }

    private function storeUpload(mixed $file, string $disk): string
    {
        if ($file instanceof TemporaryUploadedFile || $file instanceof UploadedFile) {
            return $file->store('', $disk);
        }

        throw new \InvalidArgumentException('Unsupported upload type.');
    }

    private function variantSlug(string $baseSlug, array $row, int $index, array $allRows): string
    {
        $custom = trim((string) ($row['slug'] ?? ''));
        if ($custom !== '') {
            return Str::slug($custom);
        }

        $colour = trim((string) ($row['colour'] ?? ''));
        $size = trim((string) ($row['size_label'] ?? ''));
        $variation = trim((string) ($row['variation'] ?? ''));

        if ($colour !== '') {
            $colourSlug = Str::slug($colour);

            return str_ends_with($baseSlug, $colourSlug)
                ? $baseSlug
                : rtrim($baseSlug, '-').'-'.$colourSlug;
        }

        if ($variation !== '' && $size === '') {
            return $baseSlug;
        }

        if ($size !== '') {
            return rtrim($baseSlug, '-').'-variant-'.Str::slug($size);
        }

        return $baseSlug;
    }

    private function variantRowIsEmpty(array $row): bool
    {
        return trim((string) ($row['sku'] ?? '')) === ''
            && trim((string) ($row['size_label'] ?? '')) === ''
            && trim((string) ($row['colour'] ?? '')) === ''
            && trim((string) ($row['variation'] ?? '')) === '';
    }

    private function generateSku(string $seed): string
    {
        return 'NGN-'.Str::upper(Str::substr(Str::slug($seed, ''), 0, 12)).'-'.Str::upper(Str::random(4));
    }

    private function nullableString(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function nullableDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }

    public function publicAssetUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        if (Storage::disk('product_images')->exists($path)) {
            return Storage::disk('product_images')->url($path);
        }

        if (Storage::disk('product_videos')->exists($path)) {
            return Storage::disk('product_videos')->url($path);
        }

        return asset('assets/images/store/products/'.$path);
    }
}
