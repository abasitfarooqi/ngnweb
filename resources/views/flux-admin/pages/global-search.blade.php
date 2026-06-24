<div>
    <div class="mb-6">
        <h1 class="flux-admin-page-title text-2xl font-bold text-zinc-900 dark:text-white">Global search</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
            Search across {{ number_format($registryCount) }} Flux Admin lists (any text column). Minimum 2 characters.
        </p>
    </div>

    <div class="flux-admin-toolbar border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 mb-6">
        <form wire:submit.prevent="$refresh" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="min-w-0 flex-1">
                <flux:input
                    wire:model.live.debounce.400ms="query"
                    icon="magnifying-glass"
                    placeholder="Search customers, registrations, PCNs, parts, emails…"
                    variant="outline"
                    autofocus
                />
            </div>
            @if($query !== '')
                <flux:button type="button" wire:click="$set('query', '')" variant="ghost" icon="x-mark" class="!rounded-none">Clear</flux:button>
            @endif
        </form>
    </div>

    @if(mb_strlen(trim($query)) > 0 && mb_strlen(trim($query)) < 2)
        <flux:callout variant="warning" icon="information-circle">
            <flux:callout.text>Type at least 2 characters to search.</flux:callout.text>
        </flux:callout>
    @elseif($query !== '' && $total === 0)
        <flux:callout variant="info" icon="magnifying-glass">
            <flux:callout.text>No matches in {{ $resourcesSearched }} lists for “{{ $query }}”.</flux:callout.text>
        </flux:callout>
    @elseif($query !== '')
        <p class="mb-4 text-sm text-zinc-600 dark:text-zinc-400">
            {{ number_format($total) }} result{{ $total === 1 ? '' : 's' }} from {{ $resourcesSearched }} lists.
        </p>

        <div class="flux-admin-table-panel border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>List</flux:table.column>
                    <flux:table.column>Record</flux:table.column>
                    <flux:table.column>Match</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($results as $hit)
                        <flux:table.row wire:key="search-{{ $hit['label'] }}-{{ $hit['id'] }}">
                            <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $hit['label'] }}</flux:table.cell>
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">{{ $hit['title'] }}</flux:table.cell>
                            <flux:table.cell class="text-zinc-500 dark:text-zinc-400 text-xs">{{ $hit['snippet'] ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-wrap items-center gap-1">
                                    @if($hit['show_url'])
                                        <a href="{{ $hit['show_url'] }}">
                                            <flux:button size="xs" variant="ghost" icon="eye" class="!rounded-none">View</flux:button>
                                        </a>
                                    @endif
                                    @if($hit['edit_url'])
                                        <a href="{{ $hit['edit_url'] }}">
                                            <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                        </a>
                                    @endif
                                    <a href="{{ $hit['index_url'] }}">
                                        <flux:button size="xs" variant="ghost" icon="queue-list" class="!rounded-none">List</flux:button>
                                    </a>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    @else
        <flux:callout variant="info" icon="light-bulb">
            <flux:callout.text>Enter a keyword to search motorbikes, customers, finance, PCNs, inventory, spare parts, club, blog, support, and more.</flux:callout.text>
        </flux:callout>
    @endif
</div>
