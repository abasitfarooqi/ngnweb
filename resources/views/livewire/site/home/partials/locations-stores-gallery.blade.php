{{-- Legacy: locationsHomeSection --}}
<section id="locations" class="py-14 md:py-16 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900" aria-label="Our stores">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-center text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-10 md:mb-12">Our Stores</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <article class="border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800 shadow-sm">
                <img loading="lazy" src="{{ url('img/catford.jpg') }}" alt="Catford branch" class="w-full h-48 object-cover">
                <div class="p-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Catford</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                        <a href="https://www.google.com/maps/search/?api=1&query=9-13+Unit+1179+Catford+Hill,+London+SE6+4NU" target="_blank" rel="noopener noreferrer" class="hover:text-brand-red underline underline-offset-2">9-13 Unit 1179 Catford Hill, London SE6 4NU</a>
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Phone:</strong> <a href="tel:02083141498" class="text-brand-red hover:underline">0208 314 1498</a> (Catford)</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Mon-Sat: 9:00 AM - 6:00 PM</p>
                    <p class="text-sm mt-2"><a href="https://wa.me/447951790568?text=Hello%20NGN%2C%20I%20would%20like%20to%20inquire%20about%20your%20services." target="_blank" rel="noopener noreferrer" class="text-green-600 dark:text-green-400 font-medium hover:underline">WhatsApp us</a></p>
                </div>
            </article>
            <article class="border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800 shadow-sm">
                <img loading="lazy" src="{{ url('img/tooting.jpg') }}" alt="Tooting branch" class="w-full h-48 object-cover">
                <div class="p-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Tooting</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                        <a href="https://www.google.com/maps/search/?api=1&query=4A+Penwortham+Road,+London+SW16+6RE" target="_blank" rel="noopener noreferrer" class="hover:text-brand-red underline underline-offset-2">4A Penwortham Road, London SW16 6RE</a>
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Phone:</strong> <a href="tel:02034095478" class="text-brand-red hover:underline">0203 409 5478</a> (Tooting)</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Mon-Sat: 9:00 AM - 6:00 PM</p>
                    <p class="text-sm mt-2"><a href="https://wa.me/447951790565?text=Hello%20NGN%2C%20I%20would%20like%20to%20inquire%20about%20your%20services." target="_blank" rel="noopener noreferrer" class="text-green-600 dark:text-green-400 font-medium hover:underline">WhatsApp us</a></p>
                </div>
            </article>
            <article class="border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800 shadow-sm">
                <img loading="lazy" src="{{ url('img/sutton.png') }}" alt="Sutton branch" class="w-full h-48 object-cover">
                <div class="p-5">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Sutton</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                        <a href="https://www.google.com/maps/search/?api=1&query=329+High+St,+Sutton+SM1+1LW" target="_blank" rel="noopener noreferrer" class="hover:text-brand-red underline underline-offset-2">329 High St, Sutton SM1 1LW</a>
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Phone:</strong> <a href="tel:02084129275" class="text-brand-red hover:underline">0208 412 9275</a> (Sutton)</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Mon-Sat: 9:00 AM - 6:00 PM</p>
                    <p class="text-sm mt-2"><a href="https://wa.me/447946295530?text=Hello%20NGN%2C%20I%20would%20like%20to%20inquire%20about%20your%20services." target="_blank" rel="noopener noreferrer" class="text-green-600 dark:text-green-400 font-medium hover:underline">WhatsApp us</a></p>
                </div>
            </article>
        </div>
        <p class="text-center mt-8">
            <a href="{{ route('site.locations') }}" class="text-sm font-semibold text-brand-red hover:underline">All location details →</a>
        </p>
    </div>
</section>
