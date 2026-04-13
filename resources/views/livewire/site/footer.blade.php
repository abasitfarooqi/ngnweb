<footer class="bg-gray-900 text-white mt-auto">

    {{-- Main footer grid --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

            {{-- Brand + Social --}}
            <div>
                <a href="/"><img src="{{ asset('img/ngn-motor-logo-fit-small.png') }}" alt="NGN Motors" class="h-12 w-auto mb-4 brightness-0 invert"></a>
                <p class="text-sm text-gray-400 mb-4">London's trusted motorcycle specialists since 2018. Catford, Tooting & Sutton.</p>
                <div class="flex items-center gap-4 mb-6">
                    <a href="https://www.facebook.com/p/Neguinho-Motors-LTD-100063111406747/" target="_blank" rel="noopener" aria-label="Facebook" class="text-gray-400 hover:text-white transition">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/neguinho_motors/" target="_blank" rel="noopener" aria-label="Instagram" class="text-gray-400 hover:text-white transition">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="https://www.tiktok.com/@ngn_neguinho" target="_blank" rel="noopener" aria-label="TikTok" class="text-gray-400 hover:text-white transition">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.28 6.28 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.82a8.17 8.17 0 0 0 4.78 1.52V6.9a4.85 4.85 0 0 1-1.01-.21z"/></svg>
                    </a>
                </div>
                <img src="{{ asset('assets/images/MCIA-Logo-Landscape-POS.png') }}" alt="MCIA Member" class="h-8 w-auto opacity-70">
            </div>

            {{-- Services --}}
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-200 mb-4">Services</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="/mot" class="hover:text-white transition">MOT Testing</a></li>
                    <li><a href="/repairs" class="hover:text-white transition">Repairs & Servicing</a></li>
                    <li><a href="{{ route('rental-hire') }}" class="hover:text-white transition">Motorcycle Rentals</a></li>
                    <li><a href="/bikes" class="hover:text-white transition">Bikes For Sale</a></li>
                    <li><a href="/finance" class="hover:text-white transition">Finance</a></li>
                    <li><a href="/motorcycle-delivery" class="hover:text-white transition">Recovery & Delivery</a></li>
                    <li><a href="/shop" class="hover:text-white transition">Shop</a></li>
                    <li><a href="/ebikes" class="hover:text-white transition">E-Bikes</a></li>
                </ul>
            </div>

            {{-- Locations --}}
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-200 mb-4">Our Locations</h3>
                <div class="space-y-5 text-sm text-gray-400">
                    @foreach($branches as $branch)
                        @php
                            $phone   = $branch->phone       ?? config('site.branches.' . strtolower($branch->name) . '.phone');
                            $address = $branch->address     ?? config('site.branches.' . strtolower($branch->name) . '.address');
                            $postcode= $branch->postal_code ?? config('site.branches.' . strtolower($branch->name) . '.postcode');
                            $city    = $branch->city        ?? 'London';
                        @endphp
                        <address class="not-italic">
                            <p class="font-bold text-white uppercase mb-1">{{ $branch->name }}</p>
                            <p>{{ $address }}, {{ $city }} {{ $postcode }}</p>
                            <a href="tel:{{ $phone }}" class="text-brand-red hover:text-red-300 transition block">{{ $phone }}</a>
                        </address>
                    @endforeach
                </div>
            </div>

            {{-- Newsletter + Account + Legal --}}
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-200 mb-4">Stay Connected</h3>

                {{-- Newsletter --}}
                <div class="mb-6">
                    @if(session()->has('newsletter_success'))
                        <flux:callout variant="success" icon="check-circle" class="mb-3 text-sm">
                            {{ session('newsletter_success') }}
                        </flux:callout>
                    @endif
                    <form wire:submit.prevent="subscribe" class="space-y-2">
                        <flux:input
                            wire:model="email"
                            type="email"
                            placeholder="Your email address"
                            class="bg-gray-800 border-gray-700 text-white placeholder-gray-500"
                        />
                        @error('email') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
                        <label class="flex items-start gap-2 text-xs text-gray-400">
                            <input type="checkbox" wire:model="consent" class="mt-0.5">
                            <span>I agree to receive marketing emails</span>
                        </label>
                        @error('consent') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
                        <flux:button type="submit" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark">
                            Subscribe
                        </flux:button>
                    </form>
                </div>

                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Account</h4>
                <ul class="space-y-1 text-sm text-gray-400 mb-4">
                    <li><a href="{{ route('site.careers.index') }}" class="hover:text-white transition">Careers</a></li>
                    <li><a href="{{ route('contact.me') }}" class="hover:text-white transition">Contact Us</a></li>
                    <li><a href="/dashboard" class="hover:text-white transition">My Account</a></li>
                </ul>

                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Legal</h4>
                <ul class="space-y-1 text-sm text-gray-400">
                    <li><a href="/terms-of-use" class="hover:text-white transition">Terms of Use</a></li>
                    <li><a href="/cookie-and-privacy-policy" class="hover:text-white transition">Cookies & Privacy</a></li>
                    <li><a href="/refund-policy" class="hover:text-white transition">Refund Policy</a></li>
                    <li><a href="/return-policy" class="hover:text-white transition">Return Policy</a></li>
                    <li><a href="/shipping-policy" class="hover:text-white transition">Shipping Policy</a></li>
                </ul>
            </div>

        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 text-center text-xs text-gray-500">
            <div class="mb-3 flex justify-center">
                <x-theme-toggle-icon variant="footer" />
            </div>
            <p class="mb-1">&copy; {{ date('Y') }} Neguinho Motors Limited – All Rights Reserved</p>
            <p>Registered Company: NEGUINHO MOTORS LTD | Company No: 11600635 | Registered: 9–13 Catford Hill, London SE6 4NU</p>
            <p class="mt-1">Customer Service: <a href="mailto:enquiries@neguinhomotors.co.uk" class="hover:text-gray-300">enquiries@neguinhomotors.co.uk</a> | 0208 314 1498</p>
        </div>
    </div>

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "AutoDealer",
        "name": "Neguinho Motors Ltd",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('img/ngn-motor-logo-fit-small.png') }}",
        "telephone": "+442083141498",
        "email": "enquiries@neguinhomotors.co.uk"
    }
    </script>
</footer>
