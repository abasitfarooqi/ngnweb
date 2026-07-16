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
        ['label' => 'Branch', 'value' => $bike->branch?->name, 'icon' => 'map-pin'],
    ], fn ($row) => filled($row['value'] ?? null)));
@endphp

<div class="bg-white dark:bg-gray-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6 text-xs">
            <span class="uppercase tracking-wide text-gray-500 dark:text-gray-400">Quality used bikes</span>
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
                    <p class="text-4xl font-bold text-brand-red mt-4">£{{ number_format((float) $saleInfo->price, 2) }}</p>
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

                @if($accessoriesHtml = $this->accessoriesHtml())
                    <flux:card class="mt-6 border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="list-bullet" class="size-5 text-slate-900 dark:text-white" />
                            <flux:heading size="sm" class="uppercase tracking-wide">Accessories included</flux:heading>
                        </div>
                        <div class="text-sm leading-relaxed text-gray-700 dark:text-gray-300 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:my-1 [&_p]:my-1">
                            {!! $accessoriesHtml !!}
                        </div>
                    </flux:card>
                @endif

                <div class="mt-8 space-y-3">
                    <a href="{{ $this->whatsappEnquiryUrl() }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex w-full items-center justify-center gap-2 bg-green-600 px-4 py-3 text-sm font-semibold text-white hover:bg-green-700">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp enquiry (Catford)
                    </a>
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
