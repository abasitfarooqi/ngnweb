<div>
<div class="bg-gray-900 text-white py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold mb-3">Customer Reviews</h1>
        <p class="text-gray-300">What London riders say about us</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    @if(isset($reviews) && count($reviews) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($reviews as $review)
                <flux:card class="p-5">
                    <div class="flex items-center gap-1 mb-2 text-amber-400">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="h-4 w-4 {{ $i < ($review->rating ?? 5) ? '' : 'opacity-30' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">"{{ $review->comment ?? $review->body }}"</p>
                    <div class="text-xs text-gray-500">
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $review->name ?? 'Anonymous' }}</span>
                        @if(isset($review->created_at)) · {{ $review->created_at->format('M Y') }} @endif
                    </div>
                </flux:card>
            @endforeach
        </div>
    @else
        <flux:callout variant="info" icon="information-circle">
            <flux:callout.text>No reviews yet. Be the first to leave a review!</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Leave a review CTA --}}
    <div class="mt-12 text-center">
        <p class="text-gray-600 dark:text-gray-400 mb-4">Had a great experience? Let others know!</p>
        <div class="flex justify-center gap-3 flex-wrap">
            <flux:button href="https://g.page/r/NeguinhoMotors/review" target="_blank" variant="filled" class="bg-brand-red text-white">
                Leave a Google Review
            </flux:button>
            <flux:button href="/contact" variant="outline">Contact Us</flux:button>
        </div>
    </div>
</div>
</div>
