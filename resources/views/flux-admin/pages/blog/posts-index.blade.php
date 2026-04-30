<div>
    <x-flux-admin::data-table title="Blog posts" description="Publish and manage blog content.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/blog-post/create')" class="!rounded-none">New post</flux:button>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search title or slug…">
                <div class="min-w-0 w-full sm:min-w-[12rem] sm:flex-1 lg:w-48 lg:flex-none">
                    <flux:select wire:model.live="filters.category_id" placeholder="Category">
                        <flux:select.option value="">All categories</flux:select.option>
                        @foreach($categories as $c)
                            <flux:select.option value="{{ $c->id }}">{{ $c->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Slug</flux:table.column>
                <flux:table.column>Category</flux:table.column>
                <flux:table.column>Updated</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="bp-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white max-w-md truncate">{{ $r->title }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $r->slug }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $categories->firstWhere('id', $r->category_id)?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->updated_at?->format('d M Y') }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/blog-post/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" icon="trash" wire:click="delete({{ $r->id }})" wire:confirm="Delete this post?" class="!rounded-none text-red-600">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="5" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No posts.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
