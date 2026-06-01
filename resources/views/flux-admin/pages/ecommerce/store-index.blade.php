<div>
    <x-flux-admin::data-table title="Store front" description="Every product flagged for the Oxford or eCommerce storefront (with live branch stock).">
        <x-slot:actions>
            <x-flux-admin::export-button />
            <a href="{{ route('flux-admin.store-front.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New product</flux:button></a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name or SKU…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_oxford" placeholder="Oxford">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Oxford only</flux:select.option>
                        <flux:select.option value="0">Not Oxford</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_ecommerce" placeholder="eCommerce">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">eCommerce only</flux:select.option>
                        <flux:select.option value="0">Not eCommerce</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'name'" :direction="$sortField === 'name' ? $sortDirection : null" wire:click="sortBy('name')">Name</flux:table.column>
                <flux:table.column>SKU</flux:table.column>
                <flux:table.column>Brand</flux:table.column>
                <flux:table.column>Category</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column>Stock (all branches)</flux:table.column>
                <flux:table.column>Flags</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $p)
                    @php
                        $byBranch = $p->stockMovements->groupBy('branch_id')->map(function ($moves) {
                            return [
                                'branch' => optional($moves->first()->branch)->name,
                                'stock' => $moves->sum(fn ($m) => ((int) $m->in) - ((int) $m->out)),
                            ];
                        });
                        $total = $byBranch->sum('stock');
                    @endphp
                    <flux:table.row wire:key="sp-{{ $p->id }}">
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ \Illuminate\Support\Str::limit($p->name, 60) }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-500">{{ $p->sku }}</flux:table.cell>
                        <flux:table.cell>{{ $p->brand?->name ?: '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $p->category?->name ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">£{{ number_format((float) $p->normal_price, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="font-medium">{{ $total }}</div>
                            <div class="text-xs text-zinc-500">{{ $byBranch->map(fn ($b) => ($b['branch'] ?: '—').': '.$b['stock'])->join(' · ') ?: 'No movements' }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                @if($p->is_oxford)<flux:badge size="xs" color="blue">Oxford</flux:badge>@endif
                                @if($p->is_ecommerce)<flux:badge size="xs" color="purple">eCom</flux:badge>@endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('flux-admin.store-front.edit', $p->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" icon="trash" wire:click="delete({{ $p->id }})" wire:confirm="Delete this product?" class="!rounded-none text-red-600 dark:text-red-400">Del</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="8" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No storefront products.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[700px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit product' : 'New product' }}</flux:heading>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-flux-admin::field-group label="Name" required :error="$errors->first('formData.name')">
                    <flux:input wire:model="formData.name" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="SKU" :error="$errors->first('formData.sku')">
                    <flux:input wire:model="formData.sku" />
                </x-flux-admin::field-group>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-flux-admin::field-group label="Brand" :error="$errors->first('formData.brand_id')">
                    <flux:select wire:model="formData.brand_id">
                        <flux:select.option value="">— none —</flux:select.option>
                        @foreach($brands as $brand)
                            <flux:select.option value="{{ $brand->id }}">{{ $brand->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Category" :error="$errors->first('formData.category_id')">
                    <flux:select wire:model="formData.category_id">
                        <flux:select.option value="">— none —</flux:select.option>
                        @foreach($categories as $cat)
                            <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </x-flux-admin::field-group>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <x-flux-admin::field-group label="Normal price (£)" :error="$errors->first('formData.normal_price')">
                    <flux:input type="number" step="0.01" wire:model="formData.normal_price" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="POS price (£)" :error="$errors->first('formData.pos_price')">
                    <flux:input type="number" step="0.01" wire:model="formData.pos_price" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Global stock" :error="$errors->first('formData.global_stock')">
                    <flux:input type="number" wire:model="formData.global_stock" />
                </x-flux-admin::field-group>
            </div>

            <x-flux-admin::field-group label="Slug" :error="$errors->first('formData.slug')">
                <flux:input wire:model="formData.slug" />
            </x-flux-admin::field-group>

            <x-flux-admin::field-group label="Description" :error="$errors->first('formData.description')">
                <flux:textarea wire:model="formData.description" rows="3" />
            </x-flux-admin::field-group>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <flux:checkbox wire:model="formData.is_oxford" id="is_oxford" />
                    <label for="is_oxford" class="text-sm text-zinc-700 dark:text-zinc-300">Oxford</label>
                </div>
                <div class="flex items-center gap-2">
                    <flux:checkbox wire:model="formData.is_ecommerce" id="is_ecommerce" />
                    <label for="is_ecommerce" class="text-sm text-zinc-700 dark:text-zinc-300">eCommerce</label>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
