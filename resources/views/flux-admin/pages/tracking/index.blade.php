<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <flux:icon name="map" class="size-7 text-emerald-600" />
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Location tracking</h1>
            </div>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Customer phone locations are separate from future vehicle GPS sources.</p>
        </div>
        <flux:badge :color="$enabled ? 'green' : 'zinc'">{{ $enabled ? 'Available in this environment' : 'Disabled' }}</flux:badge>
    </div>

    @if(! $enabled)
        <flux:callout variant="info" icon="shield-check">
            <flux:callout.heading>Tracking is dormant</flux:callout.heading>
            <flux:callout.text>Customer phones will not request permission, collect GPS, submit locations, or create tracking alerts while LOCATION_TRACKING_ENABLED is false.</flux:callout.text>
        </flux:callout>
    @endif

    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Source</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Vehicle</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Last seen</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($sources as $source)
                    <flux:table.row>
                        <flux:table.cell><a class="font-medium text-emerald-600" href="{{ route('flux-admin.tracking.show', $source) }}">{{ str_replace('_', ' ', ucfirst($source->source_type)) }}</a></flux:table.cell>
                        <flux:table.cell>{{ $source->customer ? trim($source->customer->first_name.' '.$source->customer->last_name) : '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $source->vehicle?->reg_no ?? '—' }}</flux:table.cell>
                        <flux:table.cell><flux:badge color="{{ $source->sharing_enabled ? 'green' : 'zinc' }}">{{ $source->sharing_enabled ? 'Sharing' : 'Disabled' }}</flux:badge></flux:table.cell>
                        <flux:table.cell>{{ $source->last_seen_at?->timezone(config('app.timezone'))->format('d M Y H:i') ?? '—' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="5" class="py-10 text-center text-zinc-500">No tracking sources have been created.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
    {{ $sources->links() }}
</div>
