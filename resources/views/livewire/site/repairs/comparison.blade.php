<div>
<div class="relative bg-gray-900 text-white py-14 md:py-20 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-black via-gray-900 to-brand-red/25"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-5xl font-bold mb-4">Choose Your Service Package</h1>
        <nav class="text-sm text-gray-400" aria-label="Breadcrumb">
            <ol class="flex flex-wrap gap-2 list-none p-0 m-0">
                <li><a href="{{ route('site.home') }}" class="hover:text-white font-semibold underline-offset-2">Home Page</a></li>
                <li aria-hidden="true">/</li>
                <li><span class="text-gray-300 font-semibold">Compare Motorcycle Services</span></li>
            </ol>
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
    <div class="max-w-3xl mx-auto text-center mb-10 md:mb-14">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">Find the Perfect Service for Your Motorcycle</h2>
        <p class="text-base text-gray-600 dark:text-gray-400 leading-relaxed">Compare our service packages side by side and choose the one that best fits your needs.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        {{-- Basic Service Package (legacy list copy) --}}
        <div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
            <div class="bg-white dark:bg-gray-800 text-center py-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-0">Basic Service</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-0 text-sm mt-1">Essential Care Package</p>
                <div class="mt-3">
                    <span class="inline-block text-xs font-medium px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">Recommended for Regular Maintenance</span>
                </div>
            </div>
            <div class="p-5 md:p-6 text-gray-900 dark:text-gray-100">
                @include('livewire.site.repairs.partials.comparison-basic-body')
                <div class="text-center mb-4 mt-6">
                    <span class="inline-block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-900/60">Recommended Every 6,000 Miles</span>
                </div>
                <flux:button href="{{ route('site.repairs.basic') }}" variant="filled" class="w-full justify-center bg-brand-red text-white hover:bg-brand-red-dark">
                    Choose basic service
                </flux:button>
            </div>
        </div>

        {{-- Full (major) service package --}}
        <div class="relative border-2 border-emerald-600 dark:border-emerald-500 bg-white dark:bg-gray-800 shadow-md overflow-hidden">
            <div class="absolute top-0 right-5 -translate-y-1/2 z-10 bg-emerald-600 text-white text-xs font-bold px-4 py-1.5 uppercase tracking-wide">Recommended</div>
            <div class="bg-emerald-700 text-white text-center py-6">
                <h2 class="text-2xl font-bold mb-0">Full Service</h2>
                <p class="mb-0 mt-2 text-sm text-emerald-100">Complete Care Package</p>
                <div class="mt-3">
                    <span class="inline-block text-xs font-medium px-3 py-1 bg-white text-emerald-700">Comprehensive Maintenance</span>
                </div>
            </div>
            <div class="p-5 md:p-6 text-gray-900 dark:text-gray-100">
                @include('livewire.site.repairs.partials.comparison-major-body')
                <div class="text-center mb-4 mt-6">
                    <span class="inline-block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-900/60">Recommended Every 12,000 Miles</span>
                </div>
                <flux:button href="{{ route('site.repairs.full') }}" variant="filled" class="w-full justify-center bg-emerald-700 text-white hover:bg-emerald-800">
                    Choose full service
                </flux:button>
            </div>
        </div>
    </div>

    <flux:callout variant="info" icon="information-circle" class="mt-12 max-w-3xl mx-auto">
        <flux:callout.text>Looking for MOT, delivery, sales, rental or finance? Open the <a href="{{ route('all-services') }}" class="font-semibold text-brand-red underline underline-offset-2">full services overview</a> or the <a href="{{ route('site.repairs') }}" class="font-semibold text-brand-red underline underline-offset-2">repairs hub</a>.</flux:callout.text>
    </flux:callout>

    <x-site.repairs.branches-cta-dark heading="Questions about which package to book?" intro="Call any branch — we will talk you through basic versus full service and availability." />
</div>

<style>
    .svc-cat { border-bottom: 1px solid rgb(229 231 235); padding-bottom: 1rem; margin-bottom: 1rem; }
    .dark .svc-cat { border-bottom-color: rgb(55 65 81); }
    .svc-cat:last-of-type { border-bottom: none; }
    .feature-list { list-style: none; padding: 0; margin: 0; }
    .feature-list li { position: relative; padding: 0.5rem 0 0.5rem 1.5rem; font-size: 0.875rem; line-height: 1.45; }
    .feature-list li.included::before { content: "✓"; color: #16a34a; position: absolute; left: 0; font-weight: bold; }
    .feature-list li.excluded { color: #6b7280; }
    .dark .feature-list li.excluded { color: #9ca3af; }
    .feature-list li.excluded::before { content: "×"; color: #dc2626; position: absolute; left: 0; font-weight: bold; }
</style>
</div>
