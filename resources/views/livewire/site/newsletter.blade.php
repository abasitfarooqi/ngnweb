<div>
    @if(session('newsletter_success'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 text-sm">
            {{ session('newsletter_success') }}
        </div>
    @endif
    
    <form wire:submit="subscribe" class="flex gap-2">
        <input 
            type="email" 
            wire:model="email" 
            placeholder="Your email address" 
            class="flex-1 px-4 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 focus:border-brand-red focus:ring-1 focus:ring-brand-red"
        >
        <button 
            type="submit" 
            class="px-6 py-2 bg-brand-red text-white hover:bg-red-700 font-medium transition"
        >
            Subscribe
        </button>
    </form>
    
    @error('email')
        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
    @enderror
    
    <p class="text-xs text-gray-400 mt-2">Join our newsletter for exclusive deals and updates</p>
</div>
