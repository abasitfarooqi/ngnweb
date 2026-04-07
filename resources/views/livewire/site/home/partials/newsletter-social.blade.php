{{-- Legacy: body/newsletter --}}
<section class="bg-slate-100 dark:bg-gray-950 border-t border-gray-200 dark:border-gray-800 py-10 md:py-12" aria-label="Offers and social">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Get offers and discounts</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Subscribe for news and promotions.</p>
            </div>
            <div class="flex-1 max-w-xl w-full">
                @if(session('newsletter_ok'))
                    <flux:callout variant="success" icon="check-circle" class="mb-3">
                        <flux:callout.text>{{ session('newsletter_ok') }}</flux:callout.text>
                    </flux:callout>
                @endif
                <form action="{{ url('/subscribe') }}" method="post" class="flex flex-col sm:flex-row gap-2 sm:gap-0">
                    @csrf
                    <input type="email" name="subscribe-email" required placeholder="Your email" class="flex-1 min-w-0 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-transparent sm:border-r-0">
                    <button type="submit" class="px-6 py-2.5 bg-gray-900 dark:bg-brand-red text-white text-sm font-bold hover:bg-gray-800 dark:hover:bg-brand-red-dark shrink-0">
                        SUBSCRIBE
                    </button>
                </form>
                @error('subscribe-email')
                    <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                @enderror
                <ul class="flex flex-wrap items-center gap-4 mt-6 list-none p-0 m-0">
                    <li>
                        <a href="https://www.facebook.com/p/Neguinho-Motors-LTD-100063111406747/" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 hover:text-brand-red text-sm font-medium">Facebook</a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/neguinho_motors/" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 hover:text-brand-red text-sm font-medium">Instagram</a>
                    </li>
                    <li>
                        <a href="https://www.tiktok.com/@ngn_neguinho?is_from_webapp=1&sender_device=pc" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 hover:text-brand-red text-sm font-medium">TikTok</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
