<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Rent a Motorbike</flux:heading>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Browse our available fleet and start your rental</p>
        </div>
    </div>

    {{-- Filters --}}
    <flux:card class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:field>
                <flux:label>Branch</flux:label>
                <flux:select wire:model.live="selectedBranch">
                    <option value="">All Branches</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Type</flux:label>
                <flux:select wire:model.live="filterType">
                    <option value="all">All Types</option>
                    <option value="scooter">Scooters (≤125cc)</option>
                    <option value="motorbike">Motorbikes (>125cc)</option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Search</flux:label>
                <flux:input wire:model.live.debounce.300ms="searchQuery" type="text" placeholder="Make, model, reg..." />
            </flux:field>
        </div>
    </flux:card>

    {{-- Motorbike Grid --}}
    @if ($motorbikes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($motorbikes as $motorbike)
                <flux:card class="overflow-hidden p-0 hover:shadow-lg transition">
                    {{-- Image --}}
                    <div class="h-48 bg-gray-200 dark:bg-gray-700 relative">
                        @php
                            $primaryImage = null;
                            if (isset($motorbike->images) && is_iterable($motorbike->images)) {
                                foreach ($motorbike->images as $img) {
                                    $imgObj = (object) $img;
                                    if (isset($imgObj->is_primary) && $imgObj->is_primary) {
                                        $primaryImage = $imgObj;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        @if ($primaryImage)
                            <img src="{{ Storage::url($primaryImage->file_path) }}"
                                alt="{{ $motorbike->make }} {{ $motorbike->model }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full">
                                <flux:icon name="camera" class="h-16 w-16 text-gray-400" />
                            </div>
                        @endif
                        <div class="absolute top-2 right-2">
                            <flux:badge color="green" size="sm">Available</flux:badge>
                        </div>
                    </div>

                    {{-- Details --}}
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $motorbike->make }} {{ $motorbike->model }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $motorbike->reg_no }} &bull; {{ $motorbike->year }}
                        </p>
                        <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                            <flux:icon name="map-pin" class="h-3.5 w-3.5" />
                            {{ $motorbike->branch->name ?? 'Branch TBC' }}
                        </p>

                        <div class="mt-4 flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-brand-red">£{{ number_format($motorbike->weekly_rent ?? 0, 2) }}</span>
                                <span class="text-sm text-gray-500">/week</span>
                            </div>
                            <flux:button href="{{ route('account.rentals.create', $motorbike->id) }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">
                                Rent Now
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @else
        <flux:card class="p-12 text-center">
            <flux:icon name="face-frown" class="h-12 w-12 text-gray-400 mx-auto mb-3" />
            <h3 class="text-sm font-medium text-gray-900 dark:text-white">No motorbikes available</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or check back later.</p>
        </flux:card>
    @endif
</div>
