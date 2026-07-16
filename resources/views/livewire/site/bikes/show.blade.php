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

@if(! $isNew)
    @include('livewire.site.bikes.partials.show-premium')
@else
    @include('livewire.site.bikes.partials.show-classic')
@endif

<div class="bg-brand-red text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <p class="text-lg font-semibold">Ready to ride? Get in touch today.</p>
        <div x-data>
            <button type="button"
                    @click="$flux.modal('quick-book').show()"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white text-slate-900 font-semibold hover:bg-slate-100 border border-white shadow-sm">
                Enquire Now
            </button>
        </div>
    </div>
</div>
</div>
