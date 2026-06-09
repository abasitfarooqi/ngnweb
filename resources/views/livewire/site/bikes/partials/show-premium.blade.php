@php
    $images = $this->galleryImages();
    $financePrice = (float) ($saleInfo->price ?? 0);
    $motStatus = $this->motStatusLabel();
    $mileage = $saleInfo->mileage ?? null;
    $specRows = array_values(array_filter([
        ['label' => 'Last 3 of Reg', 'value' => $this->regLastThree(), 'icon' => 'identification'],
        ['label' => 'Year', 'value' => $bike->year ?? null, 'icon' => 'calendar-days'],
        ['label' => 'Engine', 'value' => $bike->engine ?? $bike->engine_capacity ?? null, 'icon' => 'cog-6-tooth'],
        ['label' => 'Colour', 'value' => $bike->color ?? null, 'icon' => 'swatch'],
        ['label' => 'MOT Status', 'value' => $motStatus, 'icon' => 'exclamation-triangle', 'alert' => $motStatus && strcasecmp($motStatus, 'Expired') === 0],
        ['label' => 'Date of first registration', 'value' => $this->firstRegistrationLabel(), 'icon' => 'document-text'],
        ['label' => 'Mileage', 'value' => $mileage ? number_format((float) $mileage).' miles' : null, 'icon' => 'chart-bar'],
        ['label' => 'Accessories', 'value' => $this->accessoriesLabel(), 'icon' => 'list-bullet'],
        ['label' => 'Branch', 'value' => $bike->branch?->name, 'icon' => 'map-pin'],
    ], fn ($row) => filled($row['value'] ?? null)));
@endphp

<div class="bg-white dark:bg-gray-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6 text-xs">
            <span class="uppercase tracking-wide text-gray-500 dark:text-gray-400">Quality used bikes layout</span>
            <div class="flex gap-2">
                <a href="{{ request()->fullUrlWithQuery(['layout' => 'classic']) }}"
                   class="px-3 py-1 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:border-brand-red hover:text-brand-red">
                    Classic view
                </a>
                <span class="px-3 py-1 bg-brand-red text-white">Premium view</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
            <div x-data="{ active: 0, total: {{ max(count($images), 1) }} }" class="min-w-0">
                @if(count($images) > 0)
                    <div class="relative border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900">
                        <span class="absolute top-4 left-4 z-10 inline-flex items-center gap-2 bg-slate-900 text-white text-xs font-semibold uppercase tracking-wide px-3 py-1.5">
                            <flux:icon name="shield-check" class="size-4" />
                            Quality Used Bikes
                        </span>

                        @foreach($images as $index => $imageUrl)
                            <img src="{{ $imageUrl }}"
                                 alt="{{ $bike->make }} {{ $bike->model }} — image {{ $index + 1 }}"
                                 x-show="active === {{ $index }}"
                                 class="w-full h-[28rem] object-cover">
                        @endforeach

                        @if(count($images) > 1)
                            <button type="button"
                                    @click="active = active === 0 ? total - 1 : active - 1"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 size-10 bg-white/90 dark:bg-gray-800/90 border border-gray-300 dark:border-gray-600 flex items-center justify-center">
                                <flux:icon name="chevron-left" class="size-5" />
                            </button>
                            <button type="button"
                                    @click="active = active === total - 1 ? 0 : active + 1"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 size-10 bg-white/90 dark:bg-gray-800/90 border border-gray-300 dark:border-gray-600 flex items-center justify-center">
                                <flux:icon name="chevron-right" class="size-5" />
                            </button>
                            <div class="absolute bottom-4 right-4 bg-slate-900/85 text-white text-xs px-2 py-1">
                                <span x-text="(active + 1) + ' / ' + total"></span>
                            </div>
                        @endif
                    </div>

                    @if(count($images) > 1)
                        <div class="grid grid-cols-6 gap-2 mt-3">
                            @foreach(array_slice($images, 0, 5) as $index => $thumbUrl)
                                <button type="button" @click="active = {{ $index }}"
                                        :class="active === {{ $index }} ? 'border-brand-red' : 'border-gray-200 dark:border-gray-700'"
                                        class="border overflow-hidden h-16">
                                    <img src="{{ $thumbUrl }}" alt="" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                            @if(count($images) > 6)
                                <button type="button" @click="active = 5"
                                        class="border border-gray-200 dark:border-gray-700 h-16 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-900">
                                    +{{ count($images) - 5 }} More
                                </button>
                            @elseif(count($images) === 6)
                                <button type="button" @click="active = 5"
                                        :class="active === 5 ? 'border-brand-red' : 'border-gray-200 dark:border-gray-700'"
                                        class="border overflow-hidden h-16">
                                    <img src="{{ $images[5] }}" alt="" class="w-full h-full object-cover">
                                </button>
                            @endif
                        </div>
                    @endif
                @else
                    <div class="h-[28rem] border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 flex items-center justify-center text-gray-500">
                        No images available
                    </div>
                @endif
            </div>

            <div class="min-w-0">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Used {{ $bike->make }} {{ $bike->model }}</p>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white leading-tight">
                    Used {{ $bike->make }} {{ $bike->model }}
                </h1>
                @if($bike->year)
                    <p class="text-lg text-gray-500 dark:text-gray-400 mt-1">{{ $bike->year }}</p>
                @endif

                @if($saleInfo)
                    <p class="text-4xl font-bold text-brand-red mt-4">£{{ number_format((float) $saleInfo->price, 0) }}</p>
                @else
                    <p class="text-2xl font-bold text-gray-500 mt-4">Call for price</p>
                @endif

                <div class="flex flex-wrap gap-3 mt-5">
                    <span class="inline-flex items-center gap-2 bg-brand-red text-white text-sm font-semibold px-4 py-2">
                        <flux:icon name="map-pin" class="size-4" />
                        Cash Sale Available
                    </span>
                    <a href="/finance?source=used-bike&bike_id={{ $bike->id }}&bike_type=used&price={{ $financePrice }}"
                       class="inline-flex items-center gap-2 bg-slate-900 text-white text-sm font-semibold px-4 py-2 hover:bg-slate-800">
                        <flux:icon name="calendar-days" class="size-4" />
                        Payment Plan Available
                    </a>
                </div>

                <div class="mt-8 border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($specRows as $row)
                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="size-9 bg-slate-900 text-white inline-flex items-center justify-center shrink-0">
                                    <flux:icon :name="$row['icon']" class="size-4" />
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ $row['label'] }}</span>
                            </div>
                            <span @class([
                                'text-sm font-semibold text-right',
                                'text-brand-red' => ! empty($row['alert']),
                                'text-slate-900 dark:text-white' => empty($row['alert']),
                            ])>{{ $row['value'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 space-y-3">
                    <flux:button href="/finance?source=used-bike&bike_id={{ $bike->id }}&bike_type=used&price={{ $financePrice }}" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
                        Check Payment Plan Options
                    </flux:button>
                    <flux:button href="tel:{{ config('site.phone') }}" variant="outline" class="w-full">
                        Call Us: {{ config('site.phone') }}
                    </flux:button>
                </div>

                <div class="mt-8">
                    @include('livewire.site.partials.sales.enquiry-form', [
                        'submitAction' => 'submitEnquiry',
                        'enquiryTypeLabel' => 'Used motorcycle',
                        'heading' => 'Enquire about this bike',
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
