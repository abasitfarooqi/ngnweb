<div>
{{-- Hero --}}
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-3">MOT Testing Services</h1>
        <p class="text-gray-300 text-lg mb-6">Ensure your motorcycle is roadworthy & safe with our expert MOT services</p>
        <flux:button href="/mot/book" variant="filled" class="bg-brand-red text-white hover:bg-brand-red-dark">
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

{{-- Info section --}}
<div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-5">What We Check</h2>
                <ul class="space-y-3">
                    @foreach(['Lights, signals & reflectors', 'Steering & suspension', 'Brakes & tyres', 'Exhaust & emissions', 'Frame & chassis', 'Registration plate & VIN'] as $check)
                        <li class="flex items-start gap-2 text-gray-700 dark:text-gray-300 text-sm">
                            <flux:icon name="check-circle" class="h-5 w-5 text-brand-red flex-shrink-0 mt-0.5" />
                            {{ $check }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-5">MOT Test Price</h2>
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 mb-5">
                    <p class="text-4xl font-bold text-brand-red mb-1">£29.65</p>
                    <p class="text-sm text-gray-500">Standard motorcycle MOT test</p>
                </div>
                <flux:callout variant="info" icon="information-circle">
                    <flux:callout.heading>What to Bring</flux:callout.heading>
                    <flux:callout.text class="text-xs space-y-1">
                        <p>• Valid insurance certificate</p>
                        <p>• V5C registration document (if available)</p>
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
    <div class="space-y-3" x-data="{ open: null }">
        @foreach([
            ['When does my motorcycle need an MOT?', 'Your motorcycle needs its first MOT three years after registration, then annually after that.'],
            ['How long does an MOT test take?', 'A standard motorcycle MOT test takes approximately 30–45 minutes.'],
            ['What if my motorcycle fails?', "If your bike fails, we'll provide a detailed report. We can carry out repairs on-site and re-test the same day if possible."],
            ['Do you offer free retests?', 'Yes! Return within 10 working days and only the failed items are checked – retest is free.'],
        ] as [$q, $a])
            <div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <button
                    @click="open = open === '{{ $loop->index }}' ? null : '{{ $loop->index }}'"
                    class="w-full flex items-center justify-between p-5 text-left font-semibold text-gray-900 dark:text-white text-sm"
                >
                    {{ $q }}
                    <span class="h-4 w-4 text-gray-500 transition-transform" :class="open === '{{ $loop->index }}' ? 'rotate-180' : ''">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                    </span>
                </button>
                <div x-show="open === '{{ $loop->index }}'" x-collapse class="px-5 pb-5 text-sm text-gray-600 dark:text-gray-400">
                    {{ $a }}
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- CTA --}}
<div class="bg-brand-red text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <p class="text-lg font-semibold">Book your MOT today at Catford, Tooting or Sutton.</p>
        <flux:button href="/mot/book" variant="filled" class="bg-white text-brand-red hover:bg-gray-100 font-semibold">
            Book MOT Now
        </flux:button>
    </div>
</div>
</div>
