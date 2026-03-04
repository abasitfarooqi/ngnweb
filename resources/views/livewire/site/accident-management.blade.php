<div>

{{-- Hero --}}
<div class="relative bg-gray-900 text-white overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-black via-gray-900 to-red-950 opacity-95"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="max-w-2xl">
            <flux:badge color="red" class="mb-4 uppercase tracking-wider text-xs">Road Traffic Accidents</flux:badge>
            <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
                Accident Management<br>
                <span class="text-red-400">We Handle Everything</span>
            </h1>
            <p class="text-gray-300 text-lg mb-8">
                Involved in a motorcycle accident that wasn't your fault? Our expert team manages your entire claim — from replacement bike to full repairs — at no cost to you.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="#claim-form"
                   class="inline-flex items-center px-6 py-3 bg-brand-red text-white font-semibold hover:bg-red-700 transition">
                    Make a Claim
                </a>
                <a href="tel:02083141498"
                   class="inline-flex items-center px-6 py-3 border border-white text-white font-semibold hover:bg-white hover:text-gray-900 transition">
                    Call Us Now
                </a>
            </div>
        </div>
    </div>
</div>

{{-- How it works --}}
<div class="bg-white dark:bg-gray-900 py-16 border-b border-gray-100 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">How It Works</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">We work with your insurer and the third party's insurer to get you back on the road quickly.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['step'=>'01','icon'=>'phone','title'=>'Contact Us','text'=>'Call or fill in the form below. One of our accident management specialists will be in touch within 24 hours.'],
                ['step'=>'02','icon'=>'document-text','title'=>'We Assess','text'=>'We assess liability and take care of all paperwork and communication with insurers on your behalf.'],
                ['step'=>'03','icon'=>'truck','title'=>'Replacement Bike','text'=>'If you\'re not at fault, we arrange a like-for-like replacement motorcycle so you stay on the road.'],
                ['step'=>'04','icon'=>'wrench-screwdriver','title'=>'Repairs & Recovery','text'=>'We repair your motorcycle at one of our London workshops and recover any costs from the at-fault party.'],
            ] as $step)
            <div class="relative text-center px-4">
                <div class="w-12 h-12 bg-brand-red text-white flex items-center justify-center font-bold text-lg mx-auto mb-4">
                    {{ $step['step'] }}
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">{{ $step['title'] }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $step['text'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Benefits --}}
<div class="bg-gray-50 dark:bg-gray-950 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">What We Offer</h2>
                <ul class="space-y-4">
                    @foreach([
                        'Free accident management – no cost to you if the accident wasn't your fault',
                        'Like-for-like replacement motorcycle during your claim',
                        'Expert handling of all insurer communications',
                        'Motorcycle repairs at our London workshops',
                        'Recovery and delivery service',
                        'Support in multiple languages (English, Portuguese, Spanish, French, Arabic, Bengali, Punjabi, Polish)',
                        'No win no fee claim support',
                        '3 convenient London locations: Catford, Tooting & Sutton',
                    ] as $benefit)
                        <li class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-400">
                            <svg class="h-5 w-5 text-brand-red flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ $benefit }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <flux:card class="p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Important: What to Do at the Scene</h3>
                <ol class="space-y-3 text-sm text-gray-600 dark:text-gray-400 list-decimal pl-4">
                    <li>Ensure your safety and call 999 if anyone is injured</li>
                    <li>Do not admit fault – even if you feel you may be partly responsible</li>
                    <li>Exchange details with all parties (name, address, insurer, reg plate)</li>
                    <li>Take photos of all vehicles, road positions, damage and any injuries</li>
                    <li>Get contact details from any witnesses</li>
                    <li>Notify your insurer immediately</li>
                    <li>Contact NGN Motors – we'll guide you through the next steps</li>
                </ol>
                <div class="mt-5 pt-5 border-t border-gray-200 dark:border-gray-700">
                    <a href="tel:02083141498" class="flex items-center gap-2 text-brand-red font-semibold hover:underline">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        0208 314 1498
                    </a>
                </div>
            </flux:card>

        </div>
    </div>
</div>

{{-- Claim Form --}}
<div id="claim-form" class="py-16 bg-white dark:bg-gray-900 scroll-mt-24">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Make a Claim</h2>
            <p class="text-gray-500 dark:text-gray-400">Complete the form and we'll contact you within 24 hours.</p>
        </div>

        @if($submitted)
            <flux:callout variant="success" icon="check-circle" class="text-center">
                <flux:callout.heading>Claim Received</flux:callout.heading>
                <flux:callout.text>
                    Thank you {{ $name ?: 'for your submission' }}. One of our accident management specialists will contact you shortly.
                    You can also call us directly on <a href="tel:02083141498" class="underline font-medium">0208 314 1498</a>.
                </flux:callout.text>
            </flux:callout>
        @else
            <form wire:submit.prevent="submit" class="space-y-5">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <flux:field>
                        <flux:label for="acc-name">Full Name *</flux:label>
                        <flux:input id="acc-name" wire:model="name" type="text" placeholder="John Smith" required />
                        <flux:error name="name" />
                    </flux:field>
                    <flux:field>
                        <flux:label for="acc-phone">Phone Number *</flux:label>
                        <flux:input id="acc-phone" wire:model="phone" type="tel" placeholder="07700 900000" required />
                        <flux:error name="phone" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label for="acc-email">Email Address *</flux:label>
                    <flux:input id="acc-email" wire:model="email" type="email" placeholder="john@example.com" required />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label for="acc-reg">Your Number Plate / VRM *</flux:label>
                    <flux:input id="acc-reg" wire:model="reg_no" type="text" placeholder="e.g. AB12 CDE" class="uppercase" required />
                    <flux:error name="reg_no" />
                </flux:field>

                <flux:field>
                    <flux:label for="acc-lang">Language Preference</flux:label>
                    <flux:select id="acc-lang" wire:model="language">
                        @foreach(['English','Arabic','Bengali','French','Panjabi','Polish','Portuguese','Spanish'] as $lang)
                            <option value="{{ $lang }}">{{ $lang }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="language" />
                </flux:field>

                <flux:field>
                    <div class="flex items-start gap-3">
                        <flux:checkbox id="acc-privacy" wire:model="privacy_policy" />
                        <label for="acc-privacy" class="text-sm text-gray-600 dark:text-gray-400 leading-snug cursor-pointer">
                            By submitting this form I agree to the
                            <a href="/cookie-and-privacy-policy" class="text-brand-red underline" target="_blank">Privacy Policy</a>
                            and consent to NGN Motors contacting me regarding my claim. *
                        </label>
                    </div>
                    <flux:error name="privacy_policy" />
                </flux:field>

                <flux:button type="submit" variant="filled" class="w-full bg-brand-red text-white hover:bg-red-700 font-semibold py-3">
                    <span wire:loading.remove>Submit Claim</span>
                    <span wire:loading>Submitting…</span>
                </flux:button>

            </form>
        @endif
    </div>
</div>

{{-- Related services --}}
<div class="bg-gray-50 dark:bg-gray-950 border-t border-gray-200 dark:border-gray-800 py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Related Services</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="/repairs" class="block p-5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 hover:border-brand-red transition group">
                <div class="flex items-center gap-3 mb-2">
                    <flux:icon name="wrench-screwdriver" class="h-5 w-5 text-brand-red" />
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-brand-red">Repairs & Servicing</h3>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Expert motorcycle repairs in our South London workshops.</p>
            </a>
            <a href="/recovery" class="block p-5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 hover:border-brand-red transition group">
                <div class="flex items-center gap-3 mb-2">
                    <flux:icon name="bolt" class="h-5 w-5 text-brand-red" />
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-brand-red">Recovery & Delivery</h3>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">24/7 motorcycle recovery and breakdown assistance.</p>
            </a>
            <a href="{{ route('rental-hire') }}" class="block p-5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 hover:border-brand-red transition group">
                <div class="flex items-center gap-3 mb-2">
                    <flux:icon name="key" class="h-5 w-5 text-brand-red" />
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-brand-red">Motorcycle Rentals</h3>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Replacement bike from £80/week while your claim is processed.</p>
            </a>
        </div>
    </div>
</div>

</div>
