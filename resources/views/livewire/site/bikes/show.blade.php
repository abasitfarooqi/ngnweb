<div>
<div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="/motorbikes">Motorbikes</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $bike->make }} {{ $bike->model }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
</div>

@if($layoutMode === 'premium' && ! $isNew)
    @include('livewire.site.bikes.partials.show-premium')
@else
    @if(! $isNew)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            <div class="flex justify-end gap-2 text-xs">
                <span class="px-3 py-1 bg-brand-red text-white">Classic view</span>
                <a href="{{ request()->fullUrlWithQuery(['layout' => 'premium']) }}"
                   class="px-3 py-1 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:border-brand-red hover:text-brand-red">
                    Premium view
                </a>
            </div>
        </div>
    @endif
    @include('livewire.site.bikes.partials.show-classic')
@endif

<div class="bg-brand-red text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <p class="text-lg font-semibold">Ready to ride? Get in touch today.</p>
        <flux:button x-data @click="$flux.modal('quick-book').show()" variant="filled" class="bg-white text-brand-red hover:bg-gray-100 font-semibold">
            Enquire Now
        </flux:button>
    </div>
</div>
</div>
