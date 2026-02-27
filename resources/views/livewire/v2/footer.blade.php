<footer class="ngn-footer">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 md:grid-cols-4 gap-10">

        {{-- Brand --}}
        <div class="col-span-1 md:col-span-1">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-orange-400 font-black text-2xl tracking-tight">NGN</span>
                <span class="text-white font-light text-sm tracking-widest uppercase">Motors</span>
            </div>
            <p class="text-zinc-400 text-sm leading-relaxed">
                London's trusted motorbike specialists — sales, rental, servicing and recovery since 2015.
            </p>
            <div class="flex gap-3 mt-5">
                <a href="https://www.facebook.com/ngnmotors" target="_blank" rel="noopener" aria-label="Facebook" class="ngn-social-link">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                </a>
                <a href="https://www.instagram.com/ngnmotors" target="_blank" rel="noopener" aria-label="Instagram" class="ngn-social-link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="20" height="20" x="2" y="2" stroke-width="2" stroke-linejoin="miter"/><circle cx="12" cy="12" r="4" stroke-width="2"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/></svg>
                </a>
                <a href="https://www.youtube.com/@ngnmotors" target="_blank" rel="noopener" aria-label="YouTube" class="ngn-social-link">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.5C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.96A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon fill="white" points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/></svg>
                </a>
            </div>
        </div>

        {{-- Services --}}
        <div>
            <h4 class="ngn-footer-heading">Services</h4>
            <ul class="space-y-2 mt-3">
                <li><a href="{{ route('v2.bikes.sale') }}" class="ngn-footer-link">Bikes for Sale</a></li>
                <li><a href="{{ route('v2.rentals') }}" class="ngn-footer-link">Motorcycle Rentals</a></li>
                <li><a href="{{ route('v2.services') }}" class="ngn-footer-link">Servicing & Maintenance</a></li>
                <li><a href="{{ route('v2.recovery') }}" class="ngn-footer-link">Recovery & Breakdown</a></li>
                <li><a href="{{ route('v2.mot.checker') }}" class="ngn-footer-link">Free MOT Checker</a></li>
                <li><a href="{{ route('v2.ebikes') }}" class="ngn-footer-link">Electric Bikes</a></li>
            </ul>
        </div>

        {{-- Company --}}
        <div>
            <h4 class="ngn-footer-heading">Company</h4>
            <ul class="space-y-2 mt-3">
                <li><a href="{{ route('v2.about') }}" class="ngn-footer-link">About NGN Motors</a></li>
                <li><a href="{{ route('v2.contact') }}" class="ngn-footer-link">Contact Us</a></li>
                <li><a href="{{ route('ngnclub.subscribe') }}" class="ngn-footer-link">NGN Club</a></li>
                <li><a href="{{ route('ngnpartner.subscribe') }}" class="ngn-footer-link">NGN Partner</a></li>
                <li><a href="{{ route('careers.index') }}" class="ngn-footer-link">Careers</a></li>
            </ul>
        </div>

        {{-- Contact --}}
        <div>
            <h4 class="ngn-footer-heading">Contact</h4>
            <ul class="space-y-3 mt-3 text-sm text-zinc-400">
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Unit 2, 15 Empress Avenue, London, E12 5HH</span>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 8V5z"/></svg>
                    <a href="tel:+447907600611" class="hover:text-orange-400 transition-colors">+44 7907 600 611</a>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <a href="mailto:info@neguinhomotors.co.uk" class="hover:text-orange-400 transition-colors">info@neguinhomotors.co.uk</a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-zinc-500">
            <span>&copy; {{ date('Y') }} NGN Motors Ltd. All rights reserved.</span>
            <div class="flex items-center gap-4 flex-wrap justify-center">
                <a href="{{ route('v2.legal.privacy') }}" class="hover:text-orange-400 transition-colors">Privacy &amp; Cookies</a>
                <a href="{{ route('v2.legal.terms') }}" class="hover:text-orange-400 transition-colors">Terms of Use</a>
                <a href="{{ route('v2.legal.refund') }}" class="hover:text-orange-400 transition-colors">Refund Policy</a>
            </div>
        </div>
    </div>
</footer>
