<div>
{{-- Hero --}}
<div class="site-page-hero bg-gradient-to-r from-brand-green to-emerald-800 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl font-bold mb-2">{{ $survey['title'] }}</h1>
        <p class="text-emerald-100">{{ $survey['description'] }}</p>
    </div>
</div>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <x-site.form-panel title="Your feedback">
        <form wire:submit="submitSurvey" class="site-form site-form-stack">
            <flux:field>
                <flux:label>Name *</flux:label>
                <flux:input wire:model="name" />
                @error('name') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
            </flux:field>

            <flux:field>
                <flux:label>Email *</flux:label>
                <flux:input wire:model="email" type="email" />
                @error('email') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
            </flux:field>

            <div>
                <flux:label class="mb-3">How would you rate our service? *</flux:label>
                <div class="flex gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <button
                            type="button"
                            wire:click="$set('rating', {{ $i }})"
                            class="w-12 h-12 flex items-center justify-center border-2 transition-all {{ $rating == $i ? 'border-brand-green bg-brand-green text-white' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-brand-green' }}"
                        >
                            {{ $i }}
                        </button>
                    @endfor
                </div>
                @error('rating') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <flux:field>
                <flux:label>Your Feedback *</flux:label>
                <flux:textarea wire:model="feedback" rows="5" placeholder="Tell us about your experience..." />
                @error('feedback') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
            </flux:field>

            <flux:button type="submit" class="w-full bg-brand-green text-white hover:bg-brand-green-dark" size="base">Submit Survey</flux:button>
        </form>
    </x-site.form-panel>
</div>
</div>
