<div>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-brand-red via-red-700 to-gray-900">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <div class="text-8xl mb-8">🏍️</div>
        
        <h1 class="text-5xl md:text-7xl font-bold mb-6">Coming Soon</h1>
        
        <p class="text-2xl text-red-100 mb-12">We're working on something exciting. Stay tuned!</p>
        
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500 text-white">
                {{ session('success') }}
            </div>
        @endif
        
        <form wire:submit="notify" class="max-w-md mx-auto mb-12">
            <div class="flex gap-2">
                <input 
                    type="email" 
                    wire:model="email" 
                    placeholder="Enter your email to get notified" 
                    class="flex-1 px-4 py-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-white"
                >
                <flux:button type="submit" size="base">Notify Me</flux:button>
            </div>
            @error('email') <p class="mt-2 text-sm text-red-200">{{ $message }}</p> @enderror
        </form>
        
        <div class="flex justify-center gap-4">
            <flux:button variant="outline" onclick="window.location='/'" size="base">Back to Home</flux:button>
            <flux:button variant="outline" onclick="window.location='/contact'" size="base">Contact Us</flux:button>
        </div>
    </div>
</div>
</div>
