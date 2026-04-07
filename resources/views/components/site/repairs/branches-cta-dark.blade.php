@props([
    'heading' => 'Book Your Basic Service Today',
    'intro' => 'Keep your motorcycle in perfect running condition with our comprehensive basic service package',
])

<section class="bg-gray-900 text-white border border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-center">
        <h3 class="text-xl md:text-2xl font-bold text-brand-red mb-3">{{ $heading }}</h3>
        <p class="text-gray-300 text-sm md:text-base mb-6 max-w-3xl mx-auto">{{ $intro }}</p>
        <div class="bg-black/50 p-6 md:p-8 text-left md:text-center">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h4 class="font-semibold text-white mb-2">Catford Branch</h4>
                    <p class="text-sm text-gray-300">📞 <a href="tel:02083141498" class="text-white hover:text-brand-red underline-offset-2">0208 314 1498</a></p>
                    <p class="text-xs text-gray-400 mt-2">
                        <a href="https://www.google.com/maps?q=9-13+Unit+1179+Catford+Hill+London+SE6+4NU" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white underline underline-offset-2">9-13 Unit 1179 Catford Hill London SE6 4NU</a>
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-2">Tooting Branch</h4>
                    <p class="text-sm text-gray-300">📞 <a href="tel:02034095478" class="text-white hover:text-brand-red underline-offset-2">0203 409 5478</a></p>
                    <p class="text-xs text-gray-400 mt-2">
                        <a href="https://www.google.com/maps?q=4A+Penwortham+Road,+London+SW16+6RE" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white underline underline-offset-2">4A Penwortham Road, London SW16 6RE</a>
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-2">Sutton Branch</h4>
                    <p class="text-sm text-gray-300">📞 <a href="tel:02084129275" class="text-white hover:text-brand-red underline-offset-2">0208 412 9275</a></p>
                    <p class="text-xs text-gray-400 mt-2">
                        <a href="https://www.google.com/maps?q=329+High+St,+Sutton+SM1+1LW" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white underline underline-offset-2">329 High St, Sutton SM1 1LW</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
