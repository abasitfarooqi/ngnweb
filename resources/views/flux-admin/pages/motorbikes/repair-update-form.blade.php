<div>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-1 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                <a href="{{ route('flux-admin.motorbike-repairs.index') }}" class="transition hover:text-zinc-700 dark:hover:text-zinc-200" wire:navigate>Motorbike repairs</a>
                <span>/</span>
                <span>Find repair</span>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Add or edit a repair update</h1>
            <p class="mt-1 text-sm text-zinc-500">Job lines live on the repair. Search the bike or customer, then edit them on that page.</p>
        </div>
        <a href="{{ route('flux-admin.motorbike-repairs.index') }}" wire:navigate>
            <flux:button variant="ghost" size="sm" class="!rounded-none">Cancel</flux:button>
        </a>
    </div>

    <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
        <x-flux-admin::field-group label="Search repair" required>
            <div class="{{ count($repairSuggestions) ? 'flux-admin-autocomplete flux-admin-autocomplete-open' : 'flux-admin-autocomplete' }}">
                <flux:input wire:model.live.debounce.300ms="repairSearch" placeholder="Registration, customer, repair ID or bike ID…" autocomplete="off" />
                @if(count($repairSuggestions))
                    <ul class="flux-admin-autocomplete-menu" role="listbox">
                        @foreach($repairSuggestions as $row)
                            <li role="option" wire:mousedown.prevent="openRepair({{ $row['id'] }})">{{ $row['label'] }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </x-flux-admin::field-group>
    </div>
</div>
