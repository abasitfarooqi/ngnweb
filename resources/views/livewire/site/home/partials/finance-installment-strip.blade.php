{{-- Legacy: Payment plan strip --}}
<section class="border-y border-gray-200 dark:border-gray-800 bg-slate-100 dark:bg-gray-950" id="payment-plan-services" aria-label="Payment plans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div class="max-w-3xl">
                <a href="{{ route('site.finance') }}" class="group block">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight group-hover:text-brand-green transition-colors">
                        Get Your Dream Motorbike <span class="text-brand-green">with Affordable Payment Plans!</span>
                    </h2>
                </a>
            </div>
            <div class="flex flex-col sm:items-end gap-3 shrink-0">
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Call Us: (Mon–Fri 09:00–18:00 · Sat 09:00–15:45)</p>
                <flux:button href="tel:02083141498" variant="filled" class="bg-brand-green text-white hover:bg-brand-green-dark w-full sm:w-auto justify-center">
                    Call now
                </flux:button>
            </div>
        </div>
    </div>
</section>
