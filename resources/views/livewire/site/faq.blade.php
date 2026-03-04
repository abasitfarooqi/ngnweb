<div>

{{-- Hero --}}
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <flux:badge color="red" class="mb-4 uppercase tracking-wider text-xs">Help Centre</flux:badge>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Frequently Asked Questions</h1>
        <p class="text-gray-300 text-lg mb-8">Everything you need to know about our services</p>
        <div class="max-w-xl mx-auto">
            <flux:input
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="Search FAQs…"
                icon="magnifying-glass"
                class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-base"
            />
        </div>
    </div>
</div>

{{-- Quick links --}}
<div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-14 lg:top-16 z-20 shadow-sm">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 overflow-x-auto scrollbar-none py-3">
            @foreach(['Rentals','MOT','Repairs & Servicing','Sales & Finance','General','NGN Club'] as $cat)
                <a href="#{{ Str::slug($cat) }}"
                   class="flex-shrink-0 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:border-brand-red hover:text-brand-red transition">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- FAQ content --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">

    @if(count($this->filteredFaqs) === 0)
        <div class="text-center py-16">
            <flux:icon name="question-mark-circle" class="h-12 w-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
            <p class="text-gray-500 dark:text-gray-400 text-lg mb-2">No results found for "{{ $search }}"</p>
            <p class="text-gray-400 dark:text-gray-500 text-sm">Try a different search term or
                <a href="{{ route('contact.me') }}" class="text-brand-red underline">contact us directly</a>.
            </p>
        </div>
    @else
        @foreach($this->groupedFaqs as $category => $items)
            <section id="{{ Str::slug($category) }}" class="scroll-mt-32">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1 h-7 bg-brand-red flex-shrink-0"></div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $category }}</h2>
                    <flux:badge size="sm" class="ml-auto">{{ count($items) }}</flux:badge>
                </div>

                <flux:accordion class="divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700">
                    @foreach($items as $i => $faq)
                        <flux:accordion.item :value="$category . '-' . $i">
                            <flux:accordion.heading class="text-gray-800 dark:text-gray-200 font-medium text-left py-4 px-4 hover:text-brand-red">
                                {{ $faq['q'] }}
                            </flux:accordion.heading>
                            <flux:accordion.content class="px-4 pb-4 text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                {{ $faq['a'] }}
                            </flux:accordion.content>
                        </flux:accordion.item>
                    @endforeach
                </flux:accordion>
            </section>
        @endforeach
    @endif

</div>

{{-- CTA --}}
<div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Still have questions?</h2>
        <p class="text-gray-500 dark:text-gray-400 mb-6">Our team is happy to help. Get in touch with your nearest branch.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <flux:button href="{{ route('contact.me') }}" variant="filled" class="bg-brand-red text-white hover:bg-red-700">
                Contact Us
            </flux:button>
            <flux:button href="/locations" variant="outline">
                Find a Branch
            </flux:button>
            <flux:button href="https://wa.me/447951790568?text=Hello%20NGN" variant="ghost" icon="chat-bubble-left-ellipsis" target="_blank">
                WhatsApp
            </flux:button>
        </div>
    </div>
</div>

</div>
