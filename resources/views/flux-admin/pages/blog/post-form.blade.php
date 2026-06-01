<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-1">
                <a href="{{ route('flux-admin.blog-posts.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-200 transition">Blog Posts</a>
                <span>/</span>
                <span>{{ $blogPost ? 'Edit' : 'New post' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $blogPost ? 'Edit post: '.$blogPost->title : 'New blog post' }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('flux-admin.blog-posts.index') }}">
                <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button wire:click="save" variant="primary" size="sm" class="!rounded-none">Save post</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-5" novalidate>
        <div class="border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 p-5">
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Post details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Title" required :error="$errors->first('form.title')">
                    <flux:input wire:model="form.title" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Slug" :error="$errors->first('form.slug')" hint="Leave empty to auto-generate from title.">
                    <flux:input wire:model="form.slug" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Category" :error="$errors->first('form.category_id')">
                    <flux:select wire:model="form.category_id" placeholder="Select category">
                        <flux:select.option value="">None</flux:select.option>
                        @foreach($categories as $cat)
                            <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="SEO title" :error="$errors->first('form.seo_title')">
                    <flux:input wire:model="form.seo_title" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="SEO description" :error="$errors->first('form.seo_description')" class="md:col-span-2">
                    <flux:input wire:model="form.seo_description" />
                </x-flux-admin::field-group>
            </div>
            <div class="mt-4">
                <x-flux-admin::field-group label="Content" :error="$errors->first('form.content')">
                    <flux:textarea wire:model="form.content" rows="12" />
                </x-flux-admin::field-group>
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('flux-admin.blog-posts.index') }}">
                <flux:button type="button" variant="ghost" class="!rounded-none">Cancel</flux:button>
            </a>
            <flux:button type="submit" variant="primary" class="!rounded-none">Save post</flux:button>
        </div>
    </form>
</div>
