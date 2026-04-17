<div>
    @if($images->count())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($images as $image)
                <div wire:key="img-{{ $image->id }}" class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden">
                    <img
                        src="{{ asset('storage/' . $image->image_path) }}"
                        alt="{{ $image->alt_text ?? 'Motorbike image' }}"
                        class="w-full h-48 object-cover"
                        loading="lazy"
                    />
                    @if($image->alt_text)
                        <div class="px-3 py-2 text-xs text-zinc-500 dark:text-zinc-400 truncate">
                            {{ $image->alt_text }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="border border-dashed border-zinc-300 dark:border-zinc-600 p-8 text-center">
            <flux:icon name="photo" variant="outline" class="w-8 h-8 mx-auto text-zinc-400 dark:text-zinc-500 mb-3" />
            <p class="text-sm text-zinc-500 dark:text-zinc-400">No images uploaded for this motorbike.</p>
        </div>
    @endif
</div>
