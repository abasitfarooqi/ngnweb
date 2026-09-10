<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('flux-admin.tracking.index') }}" class="text-sm text-emerald-600">← Tracking sources</a>
            <h1 class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $source->source_type)) }} history</h1>
            <p class="text-sm text-zinc-500">{{ $source->customer ? trim($source->customer->first_name.' '.$source->customer->last_name) : 'No customer' }} · {{ $source->vehicle?->reg_no ?? 'No vehicle' }}</p>
        </div>
        <flux:badge color="{{ $source->sharing_enabled ? 'green' : 'zinc' }}">{{ $source->sharing_enabled ? 'Sharing' : 'Disabled' }}</flux:badge>
    </div>

    <flux:callout variant="warning" icon="information-circle">
        <flux:callout.text>This is Customer Phone Location. It may not represent the exact location of the vehicle.</flux:callout.text>
    </flux:callout>

    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-950">
        <div id="tracking-map" class="h-[28rem] w-full"></div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <flux:table>
            <flux:table.columns><flux:table.column>Recorded</flux:table.column><flux:table.column>Coordinates</flux:table.column><flux:table.column>Accuracy</flux:table.column><flux:table.column>Agreement</flux:table.column></flux:table.columns>
            <flux:table.rows>
                @forelse($locations as $location)
                    <flux:table.row>
                        <flux:table.cell>{{ $location->recorded_at->timezone(config('app.timezone'))->format('d M Y H:i') }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs">{{ $location->latitude }}, {{ $location->longitude }}</flux:table.cell>
                        <flux:table.cell>{{ $location->accuracy_metres ? $location->accuracy_metres.' m' : '—' }}</flux:table.cell>
                        <flux:table.cell>{{ class_basename($location->trackable_type) }} #{{ $location->trackable_id }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="4" class="py-10 text-center text-zinc-500">No location history.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    @if($locations->isNotEmpty())
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
        <script>
            (() => {
                const points = @js($locations->reverse()->map(fn ($point) => ['lat' => (float) $point->latitude, 'lng' => (float) $point->longitude, 'time' => $point->recorded_at->timezone(config('app.timezone'))->format('d M Y H:i'), 'accuracy' => $point->accuracy_metres])->values());
                const map = L.map('tracking-map');
                L.tileLayer(@js(config('tracking.map_tile_url')), { attribution: @js(config('tracking.map_attribution')) }).addTo(map);
                const line = points.map(point => [point.lat, point.lng]);
                L.polyline(line, { color: '#059669', weight: 3 }).addTo(map);
                points.forEach((point, index) => L.marker([point.lat, point.lng]).addTo(map).bindPopup(`<strong>${point.time}</strong><br>Customer Phone<br>Accuracy: ${point.accuracy ?? '—'}m`));
                map.fitBounds(line, { padding: [24, 24] });
            })();
        </script>
    @endif
</div>
