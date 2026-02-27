<div>
    <div class="ngn-page-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-3xl font-black text-white mb-2">Contact Us</h1>
            <p class="text-zinc-400">Get in touch — we typically respond within 2 hours during business hours.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Contact form --}}
        <div class="lg:col-span-2">
            @if($submitted)
                <div class="bg-green-50 border-l-4 border-green-500 p-8">
                    <h2 class="text-xl font-black text-zinc-900 mb-2">Message Sent!</h2>
                    <p class="text-zinc-600 text-sm">Thank you for getting in touch. We'll reply to your email within 2 hours during business hours.</p>
                </div>
            @else
                <form wire:submit="submit" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="ngn-label">Full Name *</label>
                            <input wire:model="name" type="text" class="ngn-input w-full" placeholder="John Smith">
                            @error('name')<span class="ngn-error">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="ngn-label">Phone Number *</label>
                            <input wire:model="phone" type="tel" class="ngn-input w-full" placeholder="07900 000000">
                            @error('phone')<span class="ngn-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="ngn-label">Email Address *</label>
                        <input wire:model="email" type="email" class="ngn-input w-full" placeholder="john@example.com">
                        @error('email')<span class="ngn-error">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="ngn-label">Message *</label>
                        <textarea wire:model="message" rows="6" class="ngn-input w-full" placeholder="Tell us how we can help..."></textarea>
                        @error('message')<span class="ngn-error">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" wire:loading.attr="disabled" class="btn-ngn text-sm px-6 py-3">
                        <span wire:loading.remove>Send Message</span>
                        <span wire:loading>Sending...</span>
                    </button>
                </form>
            @endif
        </div>

        {{-- Sidebar info --}}
        <div class="space-y-6">
            <div class="bg-zinc-900 text-white p-6">
                <h3 class="font-black text-lg mb-4">Contact Details</h3>
                <ul class="space-y-4 text-sm">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-orange-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-zinc-300">Unit 2, 15 Empress Avenue<br>London, E12 5HH</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 8V5z"/></svg>
                        <a href="tel:+447907600611" class="text-zinc-300 hover:text-orange-400 transition-colors">+44 7907 600 611</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <a href="mailto:info@neguinhomotors.co.uk" class="text-zinc-300 hover:text-orange-400 transition-colors">info@neguinhomotors.co.uk</a>
                    </li>
                </ul>
            </div>
            <div class="bg-zinc-50 border border-zinc-200 p-6">
                <h3 class="font-black text-lg text-zinc-900 mb-3">Opening Hours</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between border-b border-zinc-200 pb-2"><dt class="text-zinc-500">Mon – Fri</dt><dd class="font-semibold text-zinc-900">9:00am – 6:00pm</dd></div>
                    <div class="flex justify-between border-b border-zinc-200 pb-2"><dt class="text-zinc-500">Saturday</dt><dd class="font-semibold text-zinc-900">10:00am – 4:00pm</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-500">Sunday</dt><dd class="text-zinc-400">Closed</dd></div>
                </dl>
            </div>
            <div class="bg-orange-50 border border-orange-200 p-5 text-center">
                <p class="text-sm text-zinc-700 mb-3 font-semibold">Broken down? Call us now.</p>
                <a href="tel:+447907600611" class="btn-ngn w-full justify-center text-sm">07907 600 611</a>
            </div>
        </div>
    </div>
</div>
