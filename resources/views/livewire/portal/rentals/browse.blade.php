<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Rent a Motorbike</flux:heading>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Browse bikes and submit a rental enquiry. We will contact you to confirm availability and contract details.</p>
        </div>
    </div>

    {{-- Legacy static rental highlights --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Prices Start From GBP 70/Week</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
                ['name' => 'Honda Forza 125cc', 'price' => 100, 'img' => '/img/rentals/honda-forza-125.jpg'],
                ['name' => 'Honda PCX 125cc', 'price' => 75, 'img' => '/img/rentals/honda-pcx-125.jpg'],
                ['name' => 'Honda SH 125cc', 'price' => 75, 'img' => '/img/rentals/honda-sh-125.jpg'],
                ['name' => 'Honda Vision 125cc', 'price' => 70, 'img' => '/img/rentals/honda-vision-125.jpg'],
                ['name' => 'Yamaha NMAX 125cc', 'price' => 75, 'img' => '/img/rentals/yamaha-nmax-125.jpg'],
                ['name' => 'Yamaha XMAX 125cc', 'price' => 100, 'img' => '/img/rentals/yamaha-xmax-125.jpg'],
            ] as $item)
                <article class="border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <img src="{{ url($item['img']) }}" alt="{{ $item['name'] }}" class="w-full h-52 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $item['name'] }}</h3>
                        <p class="text-brand-red font-bold mt-1">From GBP {{ number_format((float) $item['price'], 2) }} per week</p>
                        <flux:button
                            href="{{ route('account.rentals', [
                                'service' => 'Motorcycle Rental Enquiry',
                                'model' => $item['name'],
                                'message' => 'Interested in: '.$item['name'],
                            ]) }}#rental-enquiry"
                            variant="outline"
                            size="sm"
                            class="w-full mt-3"
                        >
                            Submit enquiry
                        </flux:button>
                    </div>
                </article>
            @endforeach
        </div>
    </div>

    {{-- Filters
    <flux:card class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:field>
                <flux:label>Branch</flux:label>
                <flux:select wire:model.live="selectedBranch" variant="listbox" searchable placeholder="All Branches">
                    <flux:select.option value="">All Branches</flux:select.option>
                    @foreach ($branches as $branch)
                        <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Type</flux:label>
                <flux:select wire:model.live="filterType" variant="listbox" placeholder="All Types">
                    <flux:select.option value="all">All Types</flux:select.option>
                    <flux:select.option value="scooter">Scooters (≤125cc)</flux:select.option>
                    <flux:select.option value="motorbike">Motorbikes (&gt;125cc)</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Search</flux:label>
                <flux:input wire:model.live.debounce.300ms="searchQuery" type="text" placeholder="Make, model, reg..." />
            </flux:field>
        </div>
    </flux:card>
    --}}
{{--     
    @if ($motorbikes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($motorbikes as $motorbike)
                <flux:card class="overflow-hidden p-0 hover:shadow-lg transition">
                   
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

                   
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $motorbike->make }} {{ $motorbike->model }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $motorbike->reg_no }} &bull; {{ $motorbike->year }}
                        </p>
                        <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                            <flux:icon name="map-pin" class="h-3.5 w-3.5" />
                            {{ $motorbike->branch_name ?? 'Branch TBC' }}
                        </p>

                        <div class="mt-4 flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-brand-red">£{{ number_format((float) ($motorbike->weekly_price ?? 0), 2) }}</span>
                                <span class="text-sm text-gray-500">/week</span>
                            </div>
                            <flux:button
                                href="{{ route('account.rentals', [
                                    'service' => 'Motorcycle Rental Enquiry',
                                    'make' => (string) $motorbike->make,
                                    'model' => (string) $motorbike->model,
                                    'reg' => (string) $motorbike->reg_no,
                                    'message' => 'Interested in: '.$motorbike->make.' '.$motorbike->model.' ('.$motorbike->reg_no.')',
                                ]) }}#rental-enquiry"
                                variant="filled"
                                size="sm"
                                class="bg-brand-red text-white hover:bg-brand-red-dark"
                            >
                                Submit enquiry
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
    @endif --}}
   
    <div id="rental-enquiry" class="scroll-mt-24">
        <livewire:site.contact.service-booking
            :embedded="true"
            :rental-compact-mode="true"
            initialServiceType="Motorcycle Rental Enquiry"
            wire:key="portal-rental-shared-enquiry"
        />
    </div>
</div>
