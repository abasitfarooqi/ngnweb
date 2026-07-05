<div>
{{-- Hero --}}
<div class="site-page-hero py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">MOT Booking Services</h1>
        <p class="text-gray-300 text-lg mb-6">Ensure your motorcycle is roadworthy & safe with our expert MOT services</p>
        <flux:button href="/mot/book" variant="filled" size="base" class="bg-brand-green text-white hover:bg-brand-green-dark hover:text-white">
            Book MOT Now
        </flux:button>
    </div>
</div>

{{-- Info section --}}
<div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-5">What We Check</h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Our MOT tests cover all items required by DVSA. For the full official list of motorcycle MOT inspection items, visit the government guidance:
                </p>
                <a href="https://www.mot-testing.service.gov.uk/login"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 text-brand-green dark:text-emerald-400 font-medium hover:underline text-sm">
                    <flux:icon name="arrow-top-right-on-square" class="h-4 w-4" />
                    Official MOT Booking Guide — gov.uk
                </a>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-5">MOT Test Price</h2>
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 mb-5">
                    <p class="text-4xl font-bold text-brand-green dark:text-emerald-400 mb-1">£29.65</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Standard motorcycle MOT test</p>
                </div>
                <flux:callout variant="info" icon="information-circle">
                    <flux:callout.heading>What to Bring</flux:callout.heading>
                    <flux:callout.text class="text-xs space-y-1">
                        <p>• Valid insurance certificate</p>
                        <p>• Previous MOT certificate (if applicable)</p>
                    </flux:callout.text>
                </flux:callout>
            </div>
        </div>
    </div>
</div>

{{-- FAQs --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">MOT FAQs</h2>
    @php
        $motFaqs = [
            ['q' => 'When does my motorcycle need an MOT?', 'a' => 'Your motorcycle needs its first MOT three years after registration, then annually after that.'],
            ['q' => 'How long does an MOT test take?', 'a' => 'A standard motorcycle MOT test takes approximately 30–45 minutes.'],
            ['q' => 'What if my motorcycle fails?', 'a' => "If your bike fails, we'll provide a detailed report. We can carry out repairs on-site and re-test the same day if possible."],
            ['q' => 'Do you offer free retests?', 'a' => 'Yes! Return within 10 working days and only the failed items are checked – retest is free.'],
        ];
    @endphp
    <x-site.accordion :items="$motFaqs" />
</div>

{{-- CTA --}}
<div class="bg-brand-green text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <p class="text-lg font-semibold text-center md:text-left">Book your MOT today at Catford, Tooting or Sutton.</p>
        <flux:button
            href="/mot/book"
            variant="outline"
            class="shrink-0 border-white text-white hover:bg-white hover:text-gray-900 dark:hover:text-gray-900 font-semibold"
        >
            Book MOT Now
        </flux:button>
    </div>
</div>
{{-- MOT Checker & Alert --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>@livewire('site.mot.checker')</div>
        <div>@livewire('site.mot.alert')</div>
    </div>
</div>

</div>
