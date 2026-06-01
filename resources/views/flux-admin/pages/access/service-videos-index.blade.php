<div>
    <x-flux-admin::data-table title="Service videos" description="Rental bike service/handover video recordings.">
        <x-slot:actions>
            <a href="{{ route('flux-admin.service-videos.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">Add video</flux:button></a>
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
                                <a href="{{ route('flux-admin.service-videos.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this video record?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
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

    <flux:modal wire:model.self="showForm" class="md:w-[680px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">{{ $recordId ? 'Edit video record' : 'New video record' }}</flux:heading>
            <x-flux-admin::field-group label="Booking ID" :error="$errors->first('formData.booking_id')" required>
                <flux:input type="number" wire:model="formData.booking_id" placeholder="e.g. 1234" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Video path" :error="$errors->first('formData.video_path')">
                <flux:input wire:model="formData.video_path" placeholder="renting_service_videos/filename.mp4" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Recorded at" :error="$errors->first('formData.recorded_at')">
                <flux:input type="date" wire:model="formData.recorded_at" />
            </x-flux-admin::field-group>
            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
