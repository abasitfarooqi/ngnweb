{{-- Recovery + NGN Club — light & dark, no heavy rounding --}}
<section class="border-b border-gray-200 dark:border-gray-800 bg-gray-100 dark:bg-black text-gray-900 dark:text-white" aria-label="Recovery and NGN Club">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <article class="relative border border-gray-300 dark:border-gray-800 overflow-hidden min-h-[220px] flex flex-col items-center justify-center p-8 text-center bg-white dark:bg-gray-950 shadow-sm dark:shadow-none">
                <div class="absolute inset-0 bg-center bg-cover opacity-[0.12] dark:opacity-[0.18] pointer-events-none" style="background-image: url('{{ asset('images/recovery.jpg') }}');"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-white via-white/90 to-transparent dark:from-gray-950 dark:via-gray-950/90 dark:to-transparent pointer-events-none"></div>
                <div class="relative z-10">
                    <a href="{{ route('site.recovery') }}" class="block focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-red focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-950">
                        <span class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Free motorcycle recovery</span>
                    </a>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-3 mb-5 max-w-sm mx-auto leading-relaxed">Free recovery for qualifying riders — find out how it works.</p>
                    <flux:button href="{{ route('site.recovery') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">
                        View details
                    </flux:button>
                </div>
            </article>
            <article class="relative border border-gray-300 dark:border-gray-800 overflow-hidden min-h-[220px] flex flex-col items-center justify-center p-8 text-center bg-white dark:bg-gray-950 shadow-sm dark:shadow-none">
                <div class="absolute inset-0 bg-center bg-cover opacity-[0.12] dark:opacity-[0.18] pointer-events-none" style="background-image: url('{{ asset('images/recovery.jpg') }}');"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-white via-white/90 to-transparent dark:from-gray-950 dark:via-gray-950/90 dark:to-transparent pointer-events-none"></div>
                <div class="relative z-10">
                    <a href="{{ route('ngnclub.subscribe') }}" class="block focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-red focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-950">
                        <span class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">NGN Club membership</span>
                    </a>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-3 mb-5 max-w-sm mx-auto leading-relaxed">Join NGN Club for rewards, credits and member-only benefits.</p>
                    <flux:button href="{{ route('ngnclub.subscribe') }}" variant="filled" size="sm" class="bg-brand-red text-white hover:bg-brand-red-dark">
                        Join now
                    </flux:button>
                </div>
            </article>
        </div>
    </div>
</section>
