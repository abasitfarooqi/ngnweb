<div>
{{-- Hero --}}
<div class="bg-gradient-to-r from-brand-red to-red-700 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl font-bold mb-2">{{ $survey['title'] }}</h1>
        <p class="text-red-100">{{ $survey['description'] }}</p>
    </div>
</div>

{{-- Survey Form --}}
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <flux:card>
        <form wire:submit="submitSurvey" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                <input type="text" wire:model="name" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">How would you rate our service? *</label>
                <div class="flex gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <button 
                            type="button" 
                            wire:click="$set('rating', {{ $i }})" 
                            class="w-12 h-12 flex items-center justify-center border-2 transition-all {{ $rating == $i ? 'border-brand-red bg-brand-red text-white' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-brand-red' }}"
                        >
                            {{ $i }}
                        </button>
                    @endfor
                </div>
                @error('rating') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Feedback *</label>
                <textarea wire:model="feedback" rows="5" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Tell us about your experience..."></textarea>
                @error('feedback') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            
            <flux:button type="submit" class="w-full" size="base">Submit Survey</flux:button>
        </form>
    </flux:card>
</div>
</div>
