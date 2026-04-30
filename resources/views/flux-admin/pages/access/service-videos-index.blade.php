<div>
    <x-flux-admin::data-table title="Service videos" description="Rental bike service/handover video recordings.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" :href="url(config('backpack.base.route_prefix').'/renting-service-video/create')" class="!rounded-none">Upload video</flux:button>
        </x-slot:actions>
        <x-slot:toolbar><x-flux-admin::filter-bar search-placeholder="Search booking ID…" /></x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'recorded_at'" :direction="$sortField === 'recorded_at' ? $sortDirection : null" wire:click="sortBy('recorded_at')">Recorded</flux:table.column>
                <flux:table.column>Booking</flux:table.column>
                <flux:table.column>Path</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="sv-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->recorded_at ? \Carbon\Carbon::parse($r->recorded_at)->format('d M Y H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">#{{ $r->booking_id }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400 max-w-md truncate">{{ $r->video_path }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                @if($r->video_path)
                                    <flux:button size="xs" variant="ghost" :href="\Illuminate\Support\Facades\Storage::url($r->video_path)" target="_blank" icon="play" class="!rounded-none">Play</flux:button>
                                @endif
                                <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/renting-service-video/'.$r->id.'/edit')" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="4" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
